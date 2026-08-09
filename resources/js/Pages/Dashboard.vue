<script setup lang="ts">
import Badge from '@/Components/Badge.vue';
import CreateFicheDialog from '@/Components/CreateFicheDialog.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { CalendarIcon, FileTextIcon } from 'lucide-vue-next';
import { ref } from 'vue';

interface MatchRow {
    id: string;
    date: string;
    status: string;
    journee: { id: string; number: number } | null;
    home_team: { id: string; name: string } | null;
    outside_team: { id: string; name: string } | null;
}

interface FicheRow {
    id: string;
    name: string;
    team: { id: string; name: string };
    creator?: { name: string } | null;
    param_descriptions: Array<{ id: string; name: string; pivot: { description: string } }>;
}

defineProps<{
    upcomingMatches: MatchRow[];
    pastMatches: MatchRow[];
    recentFiches: FicheRow[];
    paramDescriptions: Array<{ id: string; name: string }>;
}>();

const statusLabels: Record<string, string> = {
    scheduled: 'À venir',
    played: 'Joué',
    cancelled: 'Annulé',
};

const statusVariants: Record<string, 'outline' | 'success' | 'destructive'> = {
    scheduled: 'outline',
    played: 'success',
    cancelled: 'destructive',
};

const consultFiche = ref<FicheRow | null>(null);
const consultFicheOpen = ref(false);

function openConsultFiche(fiche: FicheRow) {
    consultFiche.value = fiche;
    consultFicheOpen.value = true;
}
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="space-y-6 p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Prochains matchs</CardTitle>
                </CardHeader>
                <CardContent>
                    <EmptyState v-if="upcomingMatches.length === 0" title="Aucun match à venir" :icon="CalendarIcon" />
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Date</TableHead>
                                <TableHead>Journée</TableHead>
                                <TableHead>Rencontre</TableHead>
                                <TableHead>Statut</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="match in upcomingMatches"
                                :key="match.id"
                                class="cursor-pointer"
                                @click="$inertia.visit(route('referee.matches.show', match.id))"
                            >
                                <TableCell class="tabular-nums">{{ match.date }}</TableCell>
                                <TableCell class="font-display font-semibold tabular-nums text-primary">J{{ match.journee?.number }}</TableCell>
                                <TableCell>{{ match.home_team?.name }} — {{ match.outside_team?.name }}</TableCell>
                                <TableCell><Badge :variant="statusVariants[match.status] ?? 'outline'">{{ statusLabels[match.status] ?? match.status }}</Badge></TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Matchs récents</CardTitle>
                </CardHeader>
                <CardContent>
                    <EmptyState v-if="pastMatches.length === 0" title="Aucun match récent" :icon="CalendarIcon" />
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Date</TableHead>
                                <TableHead>Journée</TableHead>
                                <TableHead>Rencontre</TableHead>
                                <TableHead>Statut</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="match in pastMatches"
                                :key="match.id"
                                class="cursor-pointer"
                                @click="$inertia.visit(route('referee.matches.show', match.id))"
                            >
                                <TableCell class="tabular-nums">{{ match.date }}</TableCell>
                                <TableCell class="font-display font-semibold tabular-nums text-primary">J{{ match.journee?.number }}</TableCell>
                                <TableCell>{{ match.home_team?.name }} — {{ match.outside_team?.name }}</TableCell>
                                <TableCell><Badge :variant="statusVariants[match.status] ?? 'outline'">{{ statusLabels[match.status] ?? match.status }}</Badge></TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Dernières fiches créées</CardTitle>
                </CardHeader>
                <CardContent>
                    <EmptyState v-if="recentFiches.length === 0" title="Aucune fiche créée" :icon="FileTextIcon" />
                    <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="fiche in recentFiches" :key="fiche.id" class="cursor-pointer" @click="openConsultFiche(fiche)">
                            <Card class="group relative h-full overflow-hidden transition-colors hover:border-primary">
                                <div
                                    class="pointer-events-none absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-primary/15 to-transparent transition-opacity group-hover:from-primary/25"
                                />
                                <CardHeader class="relative">
                                    <CardTitle class="text-base">{{ fiche.name }}</CardTitle>
                                    <span class="mt-0.5 block h-0.5 w-6 rounded-full bg-primary" />
                                </CardHeader>
                                <CardContent class="relative text-sm text-muted-foreground">
                                    {{ fiche.team.name }}
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <CreateFicheDialog
            v-if="consultFiche"
            v-model:open="consultFicheOpen"
            :team="consultFiche.team"
            :param-descriptions="paramDescriptions"
            :fiche="consultFiche"
            :can-edit="false"
        />
    </AuthenticatedLayout>
</template>
