<script setup>
import AdminSubnav from '@/Components/AdminSubnav.vue';
import ConfirmActionDialog from '@/Components/ConfirmActionDialog.vue';
import Drawer from '@/Components/Drawer.vue';
import { Button } from '@/Components/ui/button';
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
    rights: { type: Array, required: true },
});

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

const createForm = useForm({
    name: '',
    slug: '',
    description: '',
});

const editingId = ref(null);
const createDrawerOpen = ref(false);

const editForm = useForm({
    name: '',
    slug: '',
    description: '',
});

watch(editingId, (id) => {
    if (id === null) {
        return;
    }
    const right = props.rights.find((r) => r.id === id);
    if (!right) {
        return;
    }
    editForm.name = right.name;
    editForm.slug = right.slug;
    editForm.description = right.description ?? '';
    editForm.clearErrors();
});

function submitCreate() {
    createForm.post(route('admin.rights.store'), {
        preserveScroll: true,
        onSuccess: () => {
            createForm.reset();
            createDrawerOpen.value = false;
        },
    });
}

function submitEdit() {
    if (editingId.value === null) {
        return;
    }
    editForm.put(route('admin.rights.update', editingId.value), {
        preserveScroll: true,
        onSuccess: () => {
            editingId.value = null;
        },
    });
}

function destroyRight(right) {
    router.delete(route('admin.rights.destroy', right.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Administration — Droits" />

    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-4">
                <AdminSubnav />
                <h2
                    class="text-xl font-semibold leading-tight text-foreground"
                >
                    Droits
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
                    title="Nouveau droit"
                    description="Les nouveaux droits sont automatiquement associés au rôle administrateur."
                >
                    <form
                        id="create-right-form"
                        class="space-y-4"
                        @submit.prevent="submitCreate"
                    >
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-foreground"
                                    for="cr-name"
                                >Nom</label>
                                <Input
                                    id="cr-name"
                                    v-model="createForm.name"
                                    type="text"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-foreground"
                                    for="cr-slug"
                                >Identifiant (slug)</label>
                                <Input
                                    id="cr-slug"
                                    v-model="createForm.slug"
                                    type="text"
                                    required
                                    class="font-mono text-sm"
                                    placeholder="ex. reports.view"
                                />
                            </div>
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-foreground"
                                for="cr-desc"
                            >Description</label>
                            <Textarea
                                id="cr-desc"
                                v-model="createForm.description"
                                rows="2"
                                class="min-h-[4rem] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                            />
                        </div>
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
                                form="create-right-form"
                                :disabled="createForm.processing"
                            >
                                Créer le droit
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
                                Droits existants
                            </h3>
                            <Button
                                type="button"
                                @click="createDrawerOpen = true"
                            >
                                Nouveau droit
                            </Button>
                        </div>
                    </div>
                    <div class="overflow-x-auto p-6">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nom</TableHead>
                                    <TableHead>Slug</TableHead>
                                    <TableHead>Rôles</TableHead>
                                    <TableHead class="text-end">
                                        Actions
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="r in rights"
                                    :key="r.id"
                                >
                                    <TableCell class="font-medium">
                                        {{ r.name }}
                                    </TableCell>
                                    <TableCell class="font-mono text-sm">
                                        {{ r.slug }}
                                    </TableCell>
                                    <TableCell class="text-muted-foreground text-sm">
                                        {{ r.roles_count }}
                                    </TableCell>
                                    <TableCell class="text-end space-x-2">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            @click="
                                                editingId =
                                                    editingId === r.id
                                                        ? null
                                                        : r.id
                                            "
                                        >
                                            {{
                                                editingId === r.id
                                                    ? 'Fermer'
                                                    : 'Modifier'
                                            }}
                                        </Button>
                                        <ConfirmActionDialog
                                            v-if="!r.is_system"
                                            title="Confirmer la suppression"
                                            :description="`Supprimer le droit « ${r.name} » ? Il sera retiré des rôles qui l’utilisent.`"
                                            confirm-label="Supprimer"
                                            cancel-label="Annuler"
                                            @confirm="destroyRight(r)"
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
                            Modifier le droit
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
                                    for="ed-name"
                                >Nom</label>
                                <Input
                                    id="ed-name"
                                    v-model="editForm.name"
                                    type="text"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-foreground"
                                    for="ed-slug"
                                >Identifiant (slug)</label>
                                <Input
                                    id="ed-slug"
                                    v-model="editForm.slug"
                                    type="text"
                                    :disabled="
                                        rights.find((x) => x.id === editingId)
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
                                for="ed-desc"
                            >Description</label>
                            <Textarea
                                id="ed-desc"
                                v-model="editForm.description"
                                rows="2"
                                class="min-h-[4rem] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                            />
                        </div>
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
