<script setup lang="ts">
import AdminSubnav from '@/Components/AdminSubnav.vue';
import ConfirmActionDialog from '@/Components/ConfirmActionDialog.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import PageHeader from '@/Components/PageHeader.vue';
import SeasonFormDrawer from '@/Components/SeasonFormDrawer.vue';
import { Button } from '@/Components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { useInertiaForm } from '@/composables/useInertiaForm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

defineProps<{
    seasons: Array<{ id: string; name: string; start: string; end: string; league: { id: string; name: string } | null }>;
    leagues: Array<{ id: string; name: string }>;
}>();

const { form, drawerOpen, editingId, openCreate, openEdit, submit } = useInertiaForm({
    name: '',
    start: '',
    end: '',
    league_id: '',
});

function submitCreate() {
    submit(route('admin.seasons.store'), 'post');
}

function submitEdit() {
    if (!editingId.value) return;
    submit(route('admin.seasons.update', editingId.value), 'put');
}

function destroySeason(season: { id: string }) {
    router.delete(route('admin.seasons.destroy', season.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Administration — Saisons" />
    <AuthenticatedLayout>
        <template #header>
            <AdminSubnav />
        </template>

        <div class="space-y-6 p-6">
            <FlashMessages />

            <PageHeader title="Saisons" description="Gérez les saisons du système.">
                <template #actions>
                    <Button @click="openCreate">Nouvelle saison</Button>
                </template>
            </PageHeader>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Nom</TableHead>
                        <TableHead>Ligue</TableHead>
                        <TableHead>Début</TableHead>
                        <TableHead>Fin</TableHead>
                        <TableHead class="w-24">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="season in seasons" :key="season.id">
                        <TableCell>{{ season.name }}</TableCell>
                        <TableCell>{{ season.league?.name }}</TableCell>
                        <TableCell>{{ season.start }}</TableCell>
                        <TableCell>{{ season.end }}</TableCell>
                        <TableCell class="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                @click="openEdit(season.id, { ...season, league_id: season.league?.id ?? '' })"
                            >
                                Éditer
                            </Button>
                            <ConfirmActionDialog
                                title="Confirmer la suppression"
                                :description="`Supprimer la saison « ${season.name} » ?`"
                                @confirm="destroySeason(season)"
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

        <SeasonFormDrawer
            v-model:open="drawerOpen"
            :editing="!!editingId"
            :leagues="leagues"
            :form="form"
            @submit="editingId ? submitEdit() : submitCreate()"
        />
    </AuthenticatedLayout>
</template>
