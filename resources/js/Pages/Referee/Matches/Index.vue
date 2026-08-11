<script setup lang="ts">
import Badge from '@/Components/Badge.vue';
import CreateMatchDrawer from '@/Components/CreateMatchDrawer.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Button } from '@/Components/ui/button';
import { NativeSelect, NativeSelectOption } from '@/Components/ui/native-select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { useInertiaForm } from '@/composables/useInertiaForm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { CalendarIcon, PlusIcon } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface MatchRow {
    id: string;
    date: string;
    status: string;
    home_team_score: number | null;
    outside_team_score: number | null;
    journee: { id: string; number: number } | null;
    home_team: { id: string; name: string } | null;
    outside_team: { id: string; name: string } | null;
    referee: { id: string; name: string } | null;
    referee_name: string | null;
    created_by: string | null;
    creator: { id: string; name: string } | null;
}

interface LeagueOption {
    id: string;
    name: string;
    teams: Array<{ id: string; name: string }>;
    journees: Array<{ id: string; number: number }>;
}

const props = defineProps<{ matches: MatchRow[]; leagues: LeagueOption[] }>();

const page = usePage();
const currentUserName = () => (page.props.auth as { user: { name: string } }).user.name;
const currentUserId = () => (page.props.auth as { user: { id: string } }).user.id;

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

const selectedLeagueId = ref('');
const selectedLeague = computed(() => props.leagues.find((league) => league.id === selectedLeagueId.value) ?? null);

type SortColumn = 'date' | 'status' | 'journee';

const sortBy = ref<SortColumn>('date');
const sortDir = ref<'asc' | 'desc'>('desc');
const statusFilter = ref('');
const journeeFilter = ref('');
const homeTeamFilter = ref('');
const outsideTeamFilter = ref('');
const createdByMeFilter = ref(false);
const refereedByMeFilter = ref(false);

function setSort(column: SortColumn) {
    if (sortBy.value === column) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = column;
        sortDir.value = 'asc';
    }
}

const filterTeams = computed(() => {
    const seen = new Map<string, { id: string; name: string }>();
    for (const match of props.matches) {
        if (match.home_team) seen.set(match.home_team.id, match.home_team);
        if (match.outside_team) seen.set(match.outside_team.id, match.outside_team);
    }
    return [...seen.values()].sort((a, b) => a.name.localeCompare(b.name));
});

const filterJournees = computed(() => {
    const seen = new Map<string, { id: string; number: number }>();
    for (const match of props.matches) {
        if (match.journee) seen.set(match.journee.id, match.journee);
    }
    return [...seen.values()].sort((a, b) => a.number - b.number);
});

const filteredSortedMatches = computed(() => {
    const dir = sortDir.value === 'asc' ? 1 : -1;

    return props.matches
        .filter((m) => !statusFilter.value || m.status === statusFilter.value)
        .filter((m) => !journeeFilter.value || m.journee?.id === journeeFilter.value)
        .filter((m) => !homeTeamFilter.value || m.home_team?.id === homeTeamFilter.value)
        .filter((m) => !outsideTeamFilter.value || m.outside_team?.id === outsideTeamFilter.value)
        .filter((m) => !createdByMeFilter.value || m.created_by === currentUserId())
        .filter((m) => !refereedByMeFilter.value || m.referee?.id === currentUserId())
        .slice()
        .sort((a, b) => {
            if (sortBy.value === 'date') return a.date.localeCompare(b.date) * dir;
            if (sortBy.value === 'status') return a.status.localeCompare(b.status) * dir;
            return ((a.journee?.number ?? 0) - (b.journee?.number ?? 0)) * dir;
        });
});

const { form, drawerOpen, openCreate, submit } = useInertiaForm({
    date: '',
    journee_id: '',
    home_team_id: '',
    outside_team_id: '',
    referee_name: '',
    as_referee: false,
});

watch(
    () => form.as_referee,
    (checked) => {
        form.referee_name = checked ? currentUserName() : '';
    },
);

function openCreateMatch() {
    selectedLeagueId.value = '';
    openCreate();
}

function submitMatch() {
    if (!selectedLeagueId.value) return;
    submit(route('referee.leagues.matches.store', selectedLeagueId.value), 'post');
}
</script>

<template>
    <Head title="Mes matchs" />
    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Mes matchs" description="Tous les matchs que vous avez créés." :separator="false" />
        </template>

        <div class="space-y-6 p-6">
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
                        <NativeSelectOption v-for="j in filterJournees" :key="j.id" :value="j.id">J{{ j.number }}</NativeSelectOption>
                    </NativeSelect>
                    <NativeSelect v-model="homeTeamFilter" class="w-48">
                        <NativeSelectOption value="">Toutes équipes domicile</NativeSelectOption>
                        <NativeSelectOption v-for="t in filterTeams" :key="t.id" :value="t.id">{{ t.name }}</NativeSelectOption>
                    </NativeSelect>
                    <NativeSelect v-model="outsideTeamFilter" class="w-48">
                        <NativeSelectOption value="">Toutes équipes extérieures</NativeSelectOption>
                        <NativeSelectOption v-for="t in filterTeams" :key="t.id" :value="t.id">{{ t.name }}</NativeSelectOption>
                    </NativeSelect>
                    <Button
                        size="sm"
                        :variant="createdByMeFilter ? 'default' : 'outline'"
                        @click="createdByMeFilter = !createdByMeFilter"
                    >
                        Créé par moi
                    </Button>
                    <Button
                        size="sm"
                        :variant="refereedByMeFilter ? 'default' : 'outline'"
                        @click="refereedByMeFilter = !refereedByMeFilter"
                    >
                        Arbitré par moi
                    </Button>
                </div>
                <div v-else></div>
                <Button @click="openCreateMatch"><PlusIcon class="mr-2 size-4" />Nouveau match</Button>
            </div>

            <EmptyState v-if="matches.length === 0" title="Aucun match" :icon="CalendarIcon" />
            <Table v-else>
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
                        <TableHead>Créé par</TableHead>
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
                        <TableCell class="font-display font-semibold tabular-nums text-primary">J{{ match.journee?.number }}</TableCell>
                        <TableCell>{{ match.home_team?.name }} — {{ match.outside_team?.name }}</TableCell>
                        <TableCell class="text-right font-display tabular-nums">
                            <span v-if="match.home_team_score !== null">{{ match.home_team_score }} - {{ match.outside_team_score }}</span>
                            <span v-else class="text-muted-foreground">—</span>
                        </TableCell>
                        <TableCell><Badge :variant="statusVariants[match.status] ?? 'outline'">{{ statusLabels[match.status] ?? match.status }}</Badge></TableCell>
                        <TableCell>{{ match.referee?.name ?? match.referee_name ?? '—' }}</TableCell>
                        <TableCell>{{ match.creator?.name ?? '—' }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <CreateMatchDrawer
            v-model:open="drawerOpen"
            v-model:league-id="selectedLeagueId"
            :leagues="leagues"
            :teams="selectedLeague?.teams ?? []"
            :journees="selectedLeague?.journees ?? []"
            :form="form"
            @submit="submitMatch"
        />
    </AuthenticatedLayout>
</template>
