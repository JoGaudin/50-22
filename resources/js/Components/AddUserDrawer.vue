<script setup>
import Drawer from '@/Components/Drawer.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { computed } from 'vue';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    form: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update:open', 'submit']);

const drawerOpen = computed({
    get: () => props.open,
    set: (value) => emit('update:open', value),
});

function closeDrawer() {
    emit('update:open', false);
}

function submitForm() {
    emit('submit');
}
</script>

<template>
    <Drawer
        v-model:open="drawerOpen"
        title="Inviter un utilisateur"
        description="Un e-mail avec un lien signé sera envoyé pour définir le mot de passe. Le rôle « Utilisateur » est attribué par défaut."
    >
        <form
            id="invite-user-form"
            class="space-y-4"
            @submit.prevent="submitForm"
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label
                        class="mb-1 block text-sm font-medium text-foreground"
                        for="invite-name"
                    >Nom</label>
                    <Input
                        id="invite-name"
                        v-model="form.name"
                        type="text"
                        required
                        autocomplete="name"
                    />
                </div>
                <div>
                    <label
                        class="mb-1 block text-sm font-medium text-foreground"
                        for="invite-email"
                    >E-mail</label>
                    <Input
                        id="invite-email"
                        v-model="form.email"
                        type="email"
                        required
                        autocomplete="email"
                    />
                </div>
            </div>
        </form>
        <template #actions>
            <div class="flex w-full items-center justify-end gap-2">
                <Button
                    type="button"
                    variant="outline"
                    @click="closeDrawer"
                >
                    Annuler
                </Button>
                <Button
                    type="submit"
                    form="invite-user-form"
                    :disabled="form.processing"
                >
                    Créer et envoyer l’invitation
                </Button>
            </div>
        </template>
    </Drawer>
</template>
