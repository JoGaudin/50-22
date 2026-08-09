<script setup lang="ts">
import AdminSubnav from '@/Components/AdminSubnav.vue';
import ConfirmActionDialog from '@/Components/ConfirmActionDialog.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import LeagueFormDrawer from '@/Components/LeagueFormDrawer.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Button } from '@/Components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { useInertiaForm } from '@/composables/useInertiaForm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

interface LeagueRow {
    id: string;
    name: string;
    logo: string | null;
    referees: Array<{ id: string; name: string }>;
}

const props = defineProps<{
    leagues: LeagueRow[];
    users: Array<{ id: string; name: string }>;
}>();

const userOptions = props.users.map((u) => ({ value: u.id, label: u.name }));

const { form, drawerOpen, editingId, openCreate, openEdit, submit } = useInertiaForm<{
    name: string;
    logo: string;
    referee_user_ids: string[];
}>({
    name: '',
    logo: '',
    referee_user_ids: [],
});

function submitCreate() {
    submit(route('admin.leagues.store'), 'post');
}

function submitEdit() {
    if (!editingId.value) return;
    submit(route('admin.leagues.update', editingId.value), 'put');
}

function openEditLeague(league: LeagueRow) {
    openEdit(league.id, {
        name: league.name,
        logo: league.logo ?? '',
        referee_user_ids: league.referees.map((r) => r.id),
    });
}

function destroyLeague(league: { id: string }) {
    router.delete(route('admin.leagues.destroy', league.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Administration — Ligues" />
    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-4">
                <AdminSubnav />
            </div>
        </template>

        <div class="space-y-6 p-6">
            <FlashMessages />

            <PageHeader title="Ligues" description="Gérez les ligues du système.">
                <template #actions>
                    <Button @click="openCreate">Nouvelle ligue</Button>
                </template>
            </PageHeader>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Nom</TableHead>
                        <TableHead class="w-24">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="league in leagues" :key="league.id">
                        <TableCell>{{ league.name }}</TableCell>
                        <TableCell class="flex gap-2">
                            <Button variant="outline" size="sm" @click="openEditLeague(league)">
                                Éditer
                            </Button>
                            <ConfirmActionDialog
                                title="Confirmer la suppression"
                                :description="`Supprimer la ligue « ${league.name} » ?`"
                                @confirm="destroyLeague(league)"
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

        <LeagueFormDrawer
            v-model:open="drawerOpen"
            :editing="!!editingId"
            :user-options="userOptions"
            :form="form"
            @submit="editingId ? submitEdit() : submitCreate()"
        />
    </AuthenticatedLayout>
</template>
