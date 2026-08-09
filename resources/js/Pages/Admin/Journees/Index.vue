<script setup lang="ts">
import AdminSubnav from '@/Components/AdminSubnav.vue';
import ConfirmActionDialog from '@/Components/ConfirmActionDialog.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import JourneeFormDrawer from '@/Components/JourneeFormDrawer.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Button } from '@/Components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { useInertiaForm } from '@/composables/useInertiaForm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

defineProps<{
    journees: Array<{
        id: string;
        number: number;
        start_date: string;
        end_date: string;
        season: { id: string; name: string } | null;
    }>;
    seasons: Array<{ id: string; name: string }>;
}>();

const { form, drawerOpen, editingId, openCreate, openEdit, submit } = useInertiaForm({
    number: 1,
    start_date: '',
    end_date: '',
    season_id: '',
});

function submitCreate() {
    submit(route('admin.journees.store'), 'post');
}

function submitEdit() {
    if (!editingId.value) return;
    submit(route('admin.journees.update', editingId.value), 'put');
}

function destroyJournee(journee: { id: string }) {
    router.delete(route('admin.journees.destroy', journee.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Administration — Journées" />
    <AuthenticatedLayout>
        <template #header>
            <AdminSubnav />
        </template>

        <div class="space-y-6 p-6">
            <FlashMessages />

            <PageHeader title="Journées" description="Gérez les journées de championnat.">
                <template #actions>
                    <Button @click="openCreate">Nouvelle journée</Button>
                </template>
            </PageHeader>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>N°</TableHead>
                        <TableHead>Saison</TableHead>
                        <TableHead>Début</TableHead>
                        <TableHead>Fin</TableHead>
                        <TableHead class="w-24">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="journee in journees" :key="journee.id">
                        <TableCell>{{ journee.number }}</TableCell>
                        <TableCell>{{ journee.season?.name }}</TableCell>
                        <TableCell>{{ journee.start_date }}</TableCell>
                        <TableCell>{{ journee.end_date }}</TableCell>
                        <TableCell class="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                @click="openEdit(journee.id, { ...journee, season_id: journee.season?.id ?? '' })"
                            >
                                Éditer
                            </Button>
                            <ConfirmActionDialog
                                title="Confirmer la suppression"
                                :description="`Supprimer la journée n°${journee.number} ?`"
                                @confirm="destroyJournee(journee)"
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

        <JourneeFormDrawer
            v-model:open="drawerOpen"
            :editing="!!editingId"
            :seasons="seasons"
            :form="form"
            @submit="editingId ? submitEdit() : submitCreate()"
        />
    </AuthenticatedLayout>
</template>
