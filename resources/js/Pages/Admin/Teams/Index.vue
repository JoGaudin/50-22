<script setup lang="ts">
import AdminSubnav from '@/Components/AdminSubnav.vue';
import ConfirmActionDialog from '@/Components/ConfirmActionDialog.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import PageHeader from '@/Components/PageHeader.vue';
import TeamFormDrawer from '@/Components/TeamFormDrawer.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Button } from '@/Components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { useInertiaForm } from '@/composables/useInertiaForm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

type Team = { id: string; name: string; slug: string; logo: string | null; logo_url: string | null; stade: string; ville: string };

const props = defineProps<{
    teams: Array<Team>;
}>();

const { form, drawerOpen, editingId, openCreate, openEdit, submit } = useInertiaForm<{
    name: string;
    slug: string;
    logo: File | null;
    stade: string;
    ville: string;
}>({
    name: '',
    slug: '',
    logo: null,
    stade: '',
    ville: '',
});

const editingTeam = computed(() => props.teams.find((t) => t.id === editingId.value) ?? null);

function openEditTeam(team: Team) {
    openEdit(team.id, { name: team.name, slug: team.slug, stade: team.stade, ville: team.ville });
}

function submitCreate() {
    submit(route('admin.teams.store'), 'post');
}

function submitEdit() {
    if (!editingId.value) return;
    submit(route('admin.teams.update', editingId.value), 'put');
}

function destroyTeam(team: { id: string }) {
    router.delete(route('admin.teams.destroy', team.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Administration — Équipes" />
    <AuthenticatedLayout>
        <template #header>
            <AdminSubnav />
        </template>

        <div class="space-y-6 p-6">
            <FlashMessages />

            <PageHeader title="Équipes" description="Gérez les équipes du système.">
                <template #actions>
                    <Button @click="openCreate">Nouvelle équipe</Button>
                </template>
            </PageHeader>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-12"></TableHead>
                        <TableHead>Nom</TableHead>
                        <TableHead>Ville</TableHead>
                        <TableHead>Stade</TableHead>
                        <TableHead class="w-24">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="team in teams" :key="team.id">
                        <TableCell>
                            <Avatar class="size-8">
                                <AvatarImage :src="team.logo_url ?? undefined" />
                                <AvatarFallback>{{ team.name[0] }}</AvatarFallback>
                            </Avatar>
                        </TableCell>
                        <TableCell>{{ team.name }}</TableCell>
                        <TableCell>{{ team.ville }}</TableCell>
                        <TableCell>{{ team.stade }}</TableCell>
                        <TableCell class="flex gap-2">
                            <Button variant="outline" size="sm" @click="openEditTeam(team)">
                                Éditer
                            </Button>
                            <ConfirmActionDialog
                                title="Confirmer la suppression"
                                :description="`Supprimer l'équipe « ${team.name} » ?`"
                                @confirm="destroyTeam(team)"
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

        <TeamFormDrawer
            v-model:open="drawerOpen"
            :editing="!!editingId"
            :editing-team="editingTeam"
            :form="form"
            @submit="editingId ? submitEdit() : submitCreate()"
        />
    </AuthenticatedLayout>
</template>
