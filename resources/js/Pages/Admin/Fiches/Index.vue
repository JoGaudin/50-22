<script setup lang="ts">
import AdminSubnav from '@/Components/AdminSubnav.vue';
import ConfirmActionDialog from '@/Components/ConfirmActionDialog.vue';
import FicheFormDrawer from '@/Components/FicheFormDrawer.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Button } from '@/Components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { useInertiaForm } from '@/composables/useInertiaForm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

interface FicheRow {
    id: string;
    name: string;
    team: { id: string; name: string } | null;
    creator: { id: string; name: string } | null;
}

defineProps<{
    fiches: FicheRow[];
    teams: Array<{ id: string; name: string }>;
}>();

const { form, drawerOpen, editingId, openCreate, openEdit, submit } = useInertiaForm({
    name: '',
    team_id: '',
});

function submitCreate() {
    submit(route('admin.fiches.store'), 'post');
}

function submitEdit() {
    if (!editingId.value) return;
    submit(route('admin.fiches.update', editingId.value), 'put');
}

function destroyFiche(fiche: { id: string }) {
    router.delete(route('admin.fiches.destroy', fiche.id), { preserveScroll: true });
}

function openEditFiche(fiche: FicheRow) {
    openEdit(fiche.id, {
        name: fiche.name,
        team_id: fiche.team?.id ?? '',
    });
}
</script>

<template>
    <Head title="Administration — Fiches" />
    <AuthenticatedLayout>
        <template #header>
            <AdminSubnav />
        </template>

        <div class="space-y-6 p-6">
            <FlashMessages />

            <PageHeader title="Fiches" description="Gérez les fiches d'équipe.">
                <template #actions>
                    <Button @click="openCreate">Nouvelle fiche</Button>
                </template>
            </PageHeader>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Nom</TableHead>
                        <TableHead>Équipe</TableHead>
                        <TableHead>Créée par</TableHead>
                        <TableHead class="w-24">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="fiche in fiches" :key="fiche.id">
                        <TableCell>{{ fiche.name }}</TableCell>
                        <TableCell>{{ fiche.team?.name }}</TableCell>
                        <TableCell>{{ fiche.creator?.name }}</TableCell>
                        <TableCell class="flex gap-2">
                            <Button variant="outline" size="sm" @click="openEditFiche(fiche)">
                                Éditer
                            </Button>
                            <ConfirmActionDialog
                                title="Confirmer la suppression"
                                :description="`Supprimer la fiche « ${fiche.name} » ?`"
                                @confirm="destroyFiche(fiche)"
                            >
                                <template #trigger>
                                    <Button variant="destructive" size="sm">Supprimer</Button>
                                </template>
                            </ConfirmActionDialog>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <FicheFormDrawer
            v-model:open="drawerOpen"
            :editing="!!editingId"
            :teams="teams"
            :form="form"
            @submit="editingId ? submitEdit() : submitCreate()"
        />
    </AuthenticatedLayout>
</template>
