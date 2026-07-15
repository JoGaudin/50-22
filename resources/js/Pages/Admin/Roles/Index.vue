<script setup>
import AdminSubnav from '@/Components/AdminSubnav.vue';
import ConfirmActionDialog from '@/Components/ConfirmActionDialog.vue';
import Drawer from '@/Components/Drawer.vue';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import { Input } from '@/Components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    roles: { type: Array, required: true },
    rights: { type: Array, required: true },
});

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

const createForm = useForm({
    name: '',
    slug: '',
    description: '',
    right_ids: [],
});

const editingId = ref(null);
const createDrawerOpen = ref(false);

const editForm = useForm({
    name: '',
    slug: '',
    description: '',
    right_ids: [],
});

watch(editingId, (id) => {
    if (id === null) {
        return;
    }
    const role = props.roles.find((r) => r.id === id);
    if (!role) {
        return;
    }
    editForm.name = role.name;
    editForm.slug = role.slug;
    editForm.description = role.description ?? '';
    editForm.right_ids = [...role.right_ids];
    editForm.clearErrors();
});

function toggleCreateRight(rightId, checked) {
    const ids = createForm.right_ids;
    if (checked && !ids.includes(rightId)) {
        ids.push(rightId);
    }
    if (!checked) {
        const i = ids.indexOf(rightId);
        if (i >= 0) {
            ids.splice(i, 1);
        }
    }
}

function toggleEditRight(rightId, checked) {
    const ids = editForm.right_ids;
    if (checked && !ids.includes(rightId)) {
        ids.push(rightId);
    }
    if (!checked) {
        const i = ids.indexOf(rightId);
        if (i >= 0) {
            ids.splice(i, 1);
        }
    }
}

function submitCreate() {
    createForm.post(route('admin.roles.store'), {
        preserveScroll: true,
        onSuccess: () => {
            createForm.reset();
            createForm.right_ids = [];
            createDrawerOpen.value = false;
        },
    });
}

function submitEdit() {
    if (editingId.value === null) {
        return;
    }
    editForm.put(route('admin.roles.update', editingId.value), {
        preserveScroll: true,
        onSuccess: () => {
            editingId.value = null;
        },
    });
}

function destroyRole(role) {
    router.delete(route('admin.roles.destroy', role.id), {
        preserveScroll: true,
    });
}

function isAdminSlug(slug) {
    return slug === 'admin';
}
</script>

<template>
    <Head title="Administration — Rôles" />

    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-4">
                <AdminSubnav />
                <h2
                    class="text-xl font-semibold leading-tight text-foreground"
                >
                    Rôles et droits
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <div
                    v-if="flashSuccess"
                    class="rounded-md border border-primary/30 bg-primary/5 px-4 py-3 text-sm text-foreground"
                >
                    {{ flashSuccess }}
                </div>
                <div
                    v-if="flashError"
                    class="rounded-md border border-destructive/30 bg-destructive/5 px-4 py-3 text-sm text-destructive"
                >
                    {{ flashError }}
                </div>

                <Drawer
                    v-model:open="createDrawerOpen"
                    title="Nouveau rôle"
                >
                    <form
                        id="create-role-form"
                        class="space-y-4"
                        @submit.prevent="submitCreate"
                    >
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-foreground"
                                    for="create-name"
                                >Nom</label>
                                <Input
                                    id="create-name"
                                    v-model="createForm.name"
                                    type="text"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-foreground"
                                    for="create-slug"
                                >Identifiant (slug)</label>
                                <Input
                                    id="create-slug"
                                    v-model="createForm.slug"
                                    type="text"
                                    required
                                    class="font-mono text-sm"
                                    placeholder="ex. moderator"
                                />
                            </div>
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-foreground"
                                for="create-desc"
                            >Description</label>
                            <Textarea
                                id="create-desc"
                                v-model="createForm.description"
                                rows="2"
                                class="min-h-[4rem] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                            />
                        </div>
                        <fieldset class="space-y-2">
                            <legend class="text-sm font-medium text-foreground">
                                Droits
                            </legend>
                            <div
                                class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3"
                            >
                                <label
                                    v-for="r in rights"
                                    :key="r.id"
                                    class="flex cursor-pointer items-center gap-2 text-sm"
                                >
                                    <Checkbox
                                        :checked="
                                            createForm.right_ids.includes(r.id)
                                        "
                                        @update:checked="
                                            (v) =>
                                                toggleCreateRight(r.id, !!v)
                                        "
                                    />
                                    <span>{{ r.name }}</span>
                                    <span
                                        class="font-mono text-xs text-muted-foreground"
                                    >({{ r.slug }})</span>
                                </label>
                            </div>
                        </fieldset>
                    </form>
                    <template #actions>
                        <div class="flex w-full items-center justify-end gap-2">
                            <Button
                                type="button"
                                variant="outline"
                                @click="createDrawerOpen = false"
                            >
                                Annuler
                            </Button>
                            <Button
                                type="submit"
                                form="create-role-form"
                                :disabled="createForm.processing"
                            >
                                Créer le rôle
                            </Button>
                        </div>
                    </template>
                </Drawer>

                <section
                    class="overflow-hidden border border-border bg-card shadow-sm sm:rounded-lg"
                >
                    <div class="border-b border-border px-6 py-4">
                        <div
                            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <h3 class="text-lg font-medium text-card-foreground">
                                Rôles existants
                            </h3>
                            <Button
                                type="button"
                                @click="createDrawerOpen = true"
                            >
                                Nouveau rôle
                            </Button>
                        </div>
                    </div>
                    <div class="overflow-x-auto p-6">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nom</TableHead>
                                    <TableHead>Slug</TableHead>
                                    <TableHead>Droits</TableHead>
                                    <TableHead class="text-end">
                                        Actions
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="role in roles"
                                    :key="role.id"
                                >
                                    <TableCell class="font-medium">
                                        {{ role.name }}
                                    </TableCell>
                                    <TableCell class="font-mono text-sm">
                                        {{ role.slug }}
                                    </TableCell>
                                    <TableCell>
                                        <div
                                            class="flex flex-wrap gap-1"
                                        >
                                            <span
                                                v-for="rt in role.rights"
                                                :key="rt.id"
                                                class="rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                                            >{{ rt.slug }}</span>
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-end space-x-2">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            @click="
                                                editingId =
                                                    editingId === role.id
                                                        ? null
                                                        : role.id
                                            "
                                        >
                                            {{
                                                editingId === role.id
                                                    ? 'Fermer'
                                                    : 'Modifier'
                                            }}
                                        </Button>
                                        <ConfirmActionDialog
                                            v-if="!role.is_system"
                                            title="Confirmer la suppression"
                                            :description="`Supprimer le rôle « ${role.name} » ? Les utilisateurs ne seront plus liés à ce rôle.`"
                                            confirm-label="Supprimer"
                                            cancel-label="Annuler"
                                            @confirm="destroyRole(role)"
                                        >
                                            <template #trigger>
                                                <Button
                                                    type="button"
                                                    variant="destructive"
                                                    size="sm"
                                                >
                                                    Supprimer
                                                </Button>
                                            </template>
                                        </ConfirmActionDialog>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </section>

                <section
                    v-if="editingId !== null"
                    class="overflow-hidden border border-border bg-card shadow-sm sm:rounded-lg"
                >
                    <div class="border-b border-border px-6 py-4">
                        <h3 class="text-lg font-medium text-card-foreground">
                            Modifier le rôle
                        </h3>
                    </div>
                    <form
                        class="space-y-4 px-6 py-6"
                        @submit.prevent="submitEdit"
                    >
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-foreground"
                                    for="edit-name"
                                >Nom</label>
                                <Input
                                    id="edit-name"
                                    v-model="editForm.name"
                                    type="text"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-foreground"
                                    for="edit-slug"
                                >Identifiant (slug)</label>
                                <Input
                                    id="edit-slug"
                                    v-model="editForm.slug"
                                    type="text"
                                    :disabled="
                                        roles.find((r) => r.id === editingId)
                                            ?.is_system
                                    "
                                    required
                                    class="font-mono text-sm"
                                />
                            </div>
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-foreground"
                                for="edit-desc"
                            >Description</label>
                            <Textarea
                                id="edit-desc"
                                v-model="editForm.description"
                                rows="2"
                                class="min-h-[4rem] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                            />
                        </div>
                        <p
                            v-if="
                                editingId &&
                                isAdminSlug(
                                    roles.find((r) => r.id === editingId)
                                        ?.slug,
                                )
                            "
                            class="text-sm text-muted-foreground"
                        >
                            Le rôle administrateur possède toujours tous les
                            droits (y compris les futurs).
                        </p>
                        <fieldset
                            v-else
                            class="space-y-2"
                        >
                            <legend class="text-sm font-medium text-foreground">
                                Droits
                            </legend>
                            <div
                                class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3"
                            >
                                <label
                                    v-for="r in rights"
                                    :key="r.id"
                                    class="flex cursor-pointer items-center gap-2 text-sm"
                                >
                                    <Checkbox
                                        :checked="
                                            editForm.right_ids.includes(r.id)
                                        "
                                        @update:checked="
                                            (v) => toggleEditRight(r.id, !!v)
                                        "
                                    />
                                    <span>{{ r.name }}</span>
                                </label>
                            </div>
                        </fieldset>
                        <Button
                            type="submit"
                            :disabled="editForm.processing"
                        >
                            Enregistrer
                        </Button>
                    </form>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
