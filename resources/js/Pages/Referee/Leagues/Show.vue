<script setup lang="ts">
import AddLeagueDropdown from '@/Components/AddLeagueDropdown.vue';
import Badge from '@/Components/Badge.vue';
import CreateMatchDrawer from '@/Components/CreateMatchDrawer.vue';
import EmptyState from '@/Components/EmptyState.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import LeaveLeagueButton from '@/Components/LeaveLeagueButton.vue';
import PageHeader from '@/Components/PageHeader.vue';
import TeamInfoDialog from '@/Components/TeamInfoDialog.vue';
import { Button } from '@/Components/ui/button';
import { NativeSelect, NativeSelectOption } from '@/Components/ui/native-select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import { useInertiaForm } from '@/composables/useInertiaForm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { CalendarIcon, PlusIcon, ShieldIcon } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Team {
    id: string;
    name: string;
    slug: string;
    logo: string | null;
    logo_url: string | null;
    ville: string;
}

interface MatchRow {
    id: string;
    date: string;
    status: string;
    home_team_score: number | null;
    outside_team_score: number | null;
    home_team: Team;
    outside_team: Team;
    journee: { id: string; number: number };
    referee: { id: string; name: string } | null;
    referee_name: string | null;
}

const props = defineProps<{
    league: { id: string; name: string };
    season: { id: string; name: string } | null;
    teams: Team[];
    matches: MatchRow[];
    journees: Array<{ id: string; number: number; start_date: string; end_date: string }>;
    availableLeagues: Array<{ id: string; name: string }>;
}>();

const page = usePage();
const currentUserName = () => (page.props.auth as { user: { name: string } }).user.name;

const { form, drawerOpen, openCreate, submit } = useInertiaForm({
    date: '',
    journee_id: '',
    home_team_id: '',
    outside_team_id: '',
    referee_name: '',
    as_referee: false,
});

function submitMatch() {
    submit(route('referee.leagues.matches.store', props.league.id), 'post');
}

watch(
    () => form.as_referee,
    (checked) => {
        form.referee_name = checked ? currentUserName() : '';
    },
);

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

type SortColumn = 'date' | 'status' | 'journee';

const sortBy = ref<SortColumn>('date');
const sortDir = ref<'asc' | 'desc'>('desc');
const statusFilter = ref('');
const journeeFilter = ref('');
const homeTeamFilter = ref('');
const outsideTeamFilter = ref('');

function setSort(column: SortColumn) {
    if (sortBy.value === column) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = column;
        sortDir.value = 'asc';
    }
}

const filteredSortedMatches = computed(() => {
    const dir = sortDir.value === 'asc' ? 1 : -1;

    return props.matches
        .filter((m) => !statusFilter.value || m.status === statusFilter.value)
        .filter((m) => !journeeFilter.value || m.journee.id === journeeFilter.value)
        .filter((m) => !homeTeamFilter.value || m.home_team.id === homeTeamFilter.value)
        .filter((m) => !outsideTeamFilter.value || m.outside_team.id === outsideTeamFilter.value)
        .slice()
        .sort((a, b) => {
            if (sortBy.value === 'date') return a.date.localeCompare(b.date) * dir;
            if (sortBy.value === 'status') return a.status.localeCompare(b.status) * dir;
            return (a.journee.number - b.journee.number) * dir;
        });
});
</script>

<template>
    <Head :title="`Arbitrage — ${league.name}`" />
    <AuthenticatedLayout>
        <template #header>
            <PageHeader
                eyebrow="Championnat"
                :title="league.name"
                :description="season ? `Saison ${season.name}` : 'Aucune saison en cours'"
            >
                <template #actions>
                    <LeaveLeagueButton :league="league" />
                    <AddLeagueDropdown :leagues="availableLeagues" />
                </template>
            </PageHeader>
        </template>

        <div class="space-y-6 p-6">
            <FlashMessages />

            <EmptyState
                v-if="!season"
                title="Aucune saison en cours"
                description="Ce championnat n'a pas de saison active pour le moment."
                :icon="CalendarIcon"
            />

            <Tabs v-else default-value="teams">
                <TabsList>
                    <TabsTrigger value="teams">Équipes</TabsTrigger>
                    <TabsTrigger value="matches">Matchs</TabsTrigger>
                </TabsList>

                <TabsContent value="teams" class="space-y-4">
                    <EmptyState v-if="teams.length === 0" title="Aucune équipe" :icon="ShieldIcon" />
                    <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <TeamInfoDialog v-for="team in teams" :key="team.id" :team="team" :matches="matches" />
                    </div>
                </TabsContent>

                <TabsContent value="matches" class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div v-if="matches.length" class="flex flex-wrap gap-3">
                            <NativeSelect v-model="statusFilter" class="w-40">
                                <NativeSelectOption value="">Tous les statuts</NativeSelectOption>
                                <NativeSelectOption v-for="(label, value) in statusLabels" :key="value" :value="value">
                                    {{ label }}
                                </NativeSelectOption>
                            </NativeSelect>
                            <NativeSelect v-model="journeeFilter" class="w-40">
                                <NativeSelectOption value="">Toutes les journées</NativeSelectOption>
                                <NativeSelectOption v-for="j in journees" :key="j.id" :value="j.id">J{{ j.number }}</NativeSelectOption>
                            </NativeSelect>
                            <NativeSelect v-model="homeTeamFilter" class="w-48">
                                <NativeSelectOption value="">Toutes équipes domicile</NativeSelectOption>
                                <NativeSelectOption v-for="t in teams" :key="t.id" :value="t.id">{{ t.name }}</NativeSelectOption>
                            </NativeSelect>
                            <NativeSelect v-model="outsideTeamFilter" class="w-48">
                                <NativeSelectOption value="">Toutes équipes extérieures</NativeSelectOption>
                                <NativeSelectOption v-for="t in teams" :key="t.id" :value="t.id">{{ t.name }}</NativeSelectOption>
                            </NativeSelect>
                        </div>
                        <div v-else></div>
                        <Button @click="openCreate"><PlusIcon class="mr-2 size-4" />Nouveau match</Button>
                    </div>

                    <EmptyState v-if="matches.length === 0" title="Aucun match" :icon="CalendarIcon" />

                    <template v-else>
                        <div class="border-t border-secondary/30"></div>

                        <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="cursor-pointer select-none" @click="setSort('date')">
                                    Date {{ sortBy === 'date' ? (sortDir === 'asc' ? '▲' : '▼') : '' }}
                                </TableHead>
                                <TableHead class="cursor-pointer select-none" @click="setSort('journee')">
                                    Journée {{ sortBy === 'journee' ? (sortDir === 'asc' ? '▲' : '▼') : '' }}
                                </TableHead>
                                <TableHead>Rencontre</TableHead>
                                <TableHead class="text-right">Score</TableHead>
                                <TableHead class="cursor-pointer select-none" @click="setSort('status')">
                                    Statut {{ sortBy === 'status' ? (sortDir === 'asc' ? '▲' : '▼') : '' }}
                                </TableHead>
                                <TableHead>Arbitre</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="match in filteredSortedMatches"
                                :key="match.id"
                                class="cursor-pointer"
                                @click="$inertia.visit(route('referee.matches.show', match.id))"
                            >
                                <TableCell class="tabular-nums">{{ match.date }}</TableCell>
                                <TableCell class="font-display font-semibold tabular-nums text-primary">J{{ match.journee.number }}</TableCell>
                                <TableCell>{{ match.home_team.name }} — {{ match.outside_team.name }}</TableCell>
                                <TableCell class="text-right font-display tabular-nums">
                                    <span v-if="match.home_team_score !== null">{{ match.home_team_score }} - {{ match.outside_team_score }}</span>
                                    <span v-else class="text-muted-foreground">—</span>
                                </TableCell>
                                <TableCell><Badge :variant="statusVariants[match.status] ?? 'outline'">{{ statusLabels[match.status] ?? match.status }}</Badge></TableCell>
                                <TableCell>{{ match.referee?.name ?? match.referee_name ?? '—' }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                    </template>
                </TabsContent>
            </Tabs>
        </div>

        <CreateMatchDrawer
            v-model:open="drawerOpen"
            :teams="teams"
            :journees="journees"
            :form="form"
            @submit="submitMatch"
        />
    </AuthenticatedLayout>
</template>
