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
            <div class="flex flex-wrap items-center justify-end gap-3">
                <NativeSelect v-model="selectedLeagueId" class="w-56">
                    <NativeSelectOption value="" disabled>Sélectionner un championnat…</NativeSelectOption>
                    <NativeSelectOption v-for="league in leagues" :key="league.id" :value="league.id">{{ league.name }}</NativeSelectOption>
                </NativeSelect>
                <Button :disabled="!selectedLeagueId" @click="openCreate"><PlusIcon class="mr-2 size-4" />Nouveau match</Button>
            </div>

            <EmptyState v-if="matches.length === 0" title="Aucun match" :icon="CalendarIcon" />
            <Table v-else>
                <TableHeader>
                    <TableRow>
                        <TableHead>Date</TableHead>
                        <TableHead>Journée</TableHead>
                        <TableHead>Rencontre</TableHead>
                        <TableHead class="text-right">Score</TableHead>
                        <TableHead>Statut</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="match in matches"
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
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <CreateMatchDrawer
            v-model:open="drawerOpen"
            :teams="selectedLeague?.teams ?? []"
            :journees="selectedLeague?.journees ?? []"
            :form="form"
            @submit="submitMatch"
        />
    </AuthenticatedLayout>
</template>
