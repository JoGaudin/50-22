<script setup>
import Drawer from '@/Components/Drawer.vue';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/Components/ui/command';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/Components/ui/popover';
import { cn } from '@/lib/utils';
import { Check, ChevronsUpDown, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    user: {
        type: Object,
        default: null,
    },
    allRoles: {
        type: Array,
        required: true,
    },
    selectedRoleIds: {
        type: Array,
        default: () => [],
    },
    hasRoleChanges: {
        type: Boolean,
        default: false,
    },
    formatCreatedAt: {
        type: Function,
        required: true,
    },
});

const emit = defineEmits([
    'update:open',
    'set-role-ids',
    'save-roles',
    'send-invitation',
]);

const drawerOpen = computed({
    get: () => props.open,
    set: (value) => emit('update:open', value),
});

const drawerTitle = computed(() =>
    props.user ? `Utilisateur : ${props.user.name}` : 'Détails utilisateur',
);
const selectedRolesModel = computed({
    get: () => props.selectedRoleIds.map((id) => String(Number(id))),
    set: (roleIds) => emit(
        'set-role-ids',
        (Array.isArray(roleIds) ? roleIds : []).map((id) => Number(id)),
    ),
});
const selectedRoles = computed(() => {
    const selected = new Set(props.selectedRoleIds.map((id) => Number(id)));

    return props.allRoles
        .filter((role) => selected.has(Number(role.id)));
});
const rolesPopoverOpen = ref(false);
const selectedRolesCountLabel = computed(() => `${selectedRoles.value.length} rôle(s) sélectionné(s)`);

function closeDrawer() {
    emit('update:open', false);
}

function saveRoles() {
    if (!props.user) {
        return;
    }

    emit('save-roles', props.user.id);
}

function sendInvitation() {
    if (!props.user) {
        return;
    }

    emit('send-invitation', props.user);
}

function removeRole(roleId) {
    selectedRolesModel.value = selectedRolesModel.value
        .filter((id) => Number(id) !== Number(roleId));
}

function clearRoles() {
    selectedRolesModel.value = [];
}

function isRoleSelected(roleId) {
    return selectedRolesModel.value.includes(String(Number(roleId)));
}

function toggleRole(roleId) {
    const normalizedRoleId = String(Number(roleId));

    if (isRoleSelected(normalizedRoleId)) {
        selectedRolesModel.value = selectedRolesModel.value
            .filter((id) => id !== normalizedRoleId);
        return;
    }

    selectedRolesModel.value = [...selectedRolesModel.value, normalizedRoleId];
}

function onSelectRole(event, roleId) {
    const selectedValue = String(event?.detail?.value ?? roleId);
    toggleRole(selectedValue);
}
</script>

<template>
    <Drawer
        v-model:open="drawerOpen"
        :title="drawerTitle"
        description="Consultez les informations du compte et gérez les rôles depuis ce panneau."
    >
        <div
            v-if="user"
            class="space-y-6"
        >
            <div class="space-y-2 rounded-lg border border-border p-4">
                <h4 class="text-sm font-semibold text-foreground">
                    Informations du compte
                </h4>
                <dl class="space-y-2 text-sm">
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-muted-foreground">Nom</dt>
                        <dd class="font-medium text-foreground">
                            {{ user.name }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-muted-foreground">E-mail</dt>
                        <dd class="font-medium text-foreground">
                            {{ user.email }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-muted-foreground">Créé le</dt>
                        <dd class="font-medium text-foreground">
                            {{ formatCreatedAt(user.created_at) }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-muted-foreground">Mot de passe</dt>
                        <dd class="font-medium">
                            <span
                                v-if="user.password_set"
                                class="text-foreground"
                            >
                                Défini
                            </span>
                            <span
                                v-else
                                class="text-amber-700 dark:text-amber-400"
                            >
                                En attente
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-3 rounded-lg border border-border p-4">
                <h4 class="text-sm font-semibold text-foreground">
                    Rôles attribués
                </h4>
                <div class="flex items-center justify-between gap-2 text-xs text-muted-foreground">
                    <span>{{ selectedRolesCountLabel }}</span>
                    <Button
                        v-if="selectedRoles.length"
                        type="button"
                        variant="ghost"
                        size="sm"
                        class="h-7 px-2"
                        @click="clearRoles"
                    >
                        Effacer tout
                    </Button>
                </div>
                <Popover v-model:open="rolesPopoverOpen">
                    <PopoverTrigger as-child>
                        <Button
                            variant="outline"
                            role="combobox"
                            :aria-expanded="rolesPopoverOpen"
                            class="w-full justify-between font-normal"
                        >
                            <span class="truncate text-left">
                                {{ selectedRoles.length ? selectedRoles.map((r) => r.name).join(', ') : 'Sélectionner des rôles...' }}
                            </span>
                            <ChevronsUpDown class="ml-2 size-4 shrink-0 opacity-50" />
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-[--reka-popover-trigger-width] p-0">
                        <Command>
                            <CommandInput
                                class="h-9"
                                placeholder="Rechercher un rôle..."
                            />
                            <CommandList>
                                <CommandEmpty>Aucun rôle trouvé.</CommandEmpty>
                                <CommandGroup>
                                    <CommandItem
                                        v-for="role in allRoles"
                                        :key="role.id"
                                        :value="String(role.id)"
                                        @select="(event) => onSelectRole(event, role.id)"
                                    >
                                        {{ role.name }}
                                        <Check
                                            :class="cn(
                                                'ml-auto',
                                                isRoleSelected(role.id) ? 'opacity-100' : 'opacity-0',
                                            )"
                                        />
                                    </CommandItem>
                                </CommandGroup>
                            </CommandList>
                        </Command>
                    </PopoverContent>
                </Popover>
                <div
                    v-if="selectedRoles.length"
                    class="flex flex-wrap gap-2"
                >
                    <Badge
                        v-for="role in selectedRoles"
                        :key="role.id"
                        variant="secondary"
                        class="gap-1.5 border border-border bg-secondary/70 py-1 pl-2 pr-1 text-secondary-foreground"
                    >
                        <span>{{ role.name }}</span>
                        <button
                            type="button"
                            class="inline-flex size-5 items-center justify-center rounded-full text-muted-foreground transition-colors hover:bg-background/60 hover:text-foreground"
                            :aria-label="`Retirer le rôle ${role.name}`"
                            @click="removeRole(role.id)"
                        >
                            <X class="size-3" aria-hidden="true" />
                        </button>
                    </Badge>
                </div>
                <p
                    v-else
                    class="text-xs text-muted-foreground"
                >
                    Aucun rôle sélectionné.
                </p>
            </div>

            <div
                v-if="!user.password_set"
                class="rounded-lg border border-border p-4"
            >
                <p class="mb-3 text-sm text-muted-foreground">
                    Aucun mot de passe défini pour ce compte.
                </p>
                <Button
                    type="button"
                    variant="outline"
                    @click="sendInvitation"
                >
                    Envoyer l’invitation
                </Button>
            </div>
        </div>
        <template #actions>
            <div class="flex w-full items-center justify-end gap-2">
                <Button
                    type="button"
                    variant="outline"
                    @click="closeDrawer"
                >
                    Fermer
                </Button>
                <Button
                    v-if="user"
                    type="button"
                    :disabled="!hasRoleChanges"
                    @click="saveRoles"
                >
                    Enregistrer les rôles
                </Button>
            </div>
        </template>
    </Drawer>
</template>
