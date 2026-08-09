<script setup lang="ts">
import AdminMatchFormDrawer from '@/Components/AdminMatchFormDrawer.vue';
import AdminSubnav from '@/Components/AdminSubnav.vue';
import Badge from '@/Components/Badge.vue';
import ConfirmActionDialog from '@/Components/ConfirmActionDialog.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Button } from '@/Components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { useInertiaForm } from '@/composables/useInertiaForm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

interface MatchRow {
    id: string;
    date: string;
    home_team_score: number | null;
    outside_team_score: number | null;
    status: string;
    referee_name: string | null;
    journee: { id: string; number: number } | null;
    home_team: { id: string; name: string } | null;
    outside_team: { id: string; name: string } | null;
}

defineProps<{
    matches: MatchRow[];
    journees: Array<{ id: string; number: number }>;
    teams: Array<{ id: string; name: string }>;
}>();

const statuses = [
    { value: 'scheduled', label: 'Programmé' },
    { value: 'played', label: 'Joué' },
    { value: 'cancelled', label: 'Annulé' },
];

const statusVariants: Record<string, 'outline' | 'success' | 'destructive'> = {
    scheduled: 'outline',
    played: 'success',
    cancelled: 'destructive',
};

const { form, drawerOpen, editingId, openCreate, openEdit, submit } = useInertiaForm({
    date: '',
    journee_id: '',
    home_team_id: '',
    outside_team_id: '',
    home_team_score: '',
    outside_team_score: '',
    referee_name: '',
    status: 'scheduled',
});

function submitCreate() {
    submit(route('admin.matches.store'), 'post');
}

function submitEdit() {
    if (!editingId.value) return;
    submit(route('admin.matches.update', editingId.value), 'put');
}

function destroyMatch(match: { id: string }) {
    router.delete(route('admin.matches.destroy', match.id), { preserveScroll: true });
}

function openEditMatch(match: MatchRow) {
    openEdit(match.id, {
        date: match.date,
        journee_id: match.journee?.id ?? '',
        home_team_id: match.home_team?.id ?? '',
        outside_team_id: match.outside_team?.id ?? '',
        home_team_score: match.home_team_score ?? '',
        outside_team_score: match.outside_team_score ?? '',
        referee_name: match.referee_name ?? '',
        status: match.status,
    });
}
</script>

<template>
    <Head title="Administration — Matchs" />
    <AuthenticatedLayout>
        <template #header>
            <AdminSubnav />
        </template>

        <div class="space-y-6 p-6">
            <FlashMessages />

            <PageHeader title="Matchs" description="Gérez les matchs du championnat.">
                <template #actions>
                    <Button @click="openCreate">Nouveau match</Button>
                </template>
            </PageHeader>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Date</TableHead>
                        <TableHead>Journée</TableHead>
                        <TableHead>Rencontre</TableHead>
                        <TableHead class="text-right">Score</TableHead>
                        <TableHead>Statut</TableHead>
                        <TableHead>Arbitre</TableHead>
                        <TableHead class="w-24">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="match in matches" :key="match.id">
                        <TableCell class="tabular-nums">{{ match.date }}</TableCell>
                        <TableCell class="font-display font-semibold tabular-nums text-primary">J{{ match.journee?.number }}</TableCell>
                        <TableCell>{{ match.home_team?.name }} — {{ match.outside_team?.name }}</TableCell>
                        <TableCell class="text-right font-display tabular-nums">
                            <span v-if="match.home_team_score !== null && match.outside_team_score !== null">
                                {{ match.home_team_score }} - {{ match.outside_team_score }}
                            </span>
                        </TableCell>
                        <TableCell>
                            <Badge :variant="statusVariants[match.status] ?? 'outline'">
                                {{ statuses.find((s) => s.value === match.status)?.label ?? match.status }}
                            </Badge>
                        </TableCell>
                        <TableCell>{{ match.referee_name ?? '—' }}</TableCell>
                        <TableCell class="flex gap-2">
                            <Button variant="outline" size="sm" @click="openEditMatch(match)">
                                Éditer
                            </Button>
                            <ConfirmActionDialog
                                title="Confirmer la suppression"
                                description="Supprimer ce match ?"
                                @confirm="destroyMatch(match)"
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

        <AdminMatchFormDrawer
            v-model:open="drawerOpen"
            :editing="!!editingId"
            :journees="journees"
            :teams="teams"
            :statuses="statuses"
            :form="form"
            @submit="editingId ? submitEdit() : submitCreate()"
        />
    </AuthenticatedLayout>
</template>
