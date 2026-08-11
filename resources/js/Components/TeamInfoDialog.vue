<script setup lang="ts">
import Badge from '@/Components/Badge.vue';
import CreateFicheDialog from '@/Components/CreateFicheDialog.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Dialog, DialogScrollContent, DialogTrigger } from '@/Components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Textarea } from '@/Components/ui/textarea';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, reactive, ref } from 'vue';

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
}

interface ParamValue {
    id: string;
    name: string;
    pivot: { description: string };
}

interface FicheVersion {
    id: string;
    name: string;
    creator: { id: string; name: string };
    param_descriptions: ParamValue[];
    match: {
        id: string;
        home_team?: { name: string } | null;
        outside_team?: { name: string } | null;
        date?: string;
    } | null;
}

interface TeamSummary {
    id: string;
    name: string;
    pivot: { description: string };
}

interface PanelData {
    versions: FicheVersion[];
    latestFiche: { param_descriptions: Array<{ id: string; pivot: { description: string } }> } | null;
    summaries: TeamSummary[];
    paramDescriptions: Array<{ id: string; name: string }>;
}

const props = defineProps<{
    team: Team;
    matches: MatchRow[];
}>();

const page = usePage();
const currentUserId = () => (page.props.auth as { user: { id: string } }).user.id;

const resultLabels: Record<string, string> = { win: 'Victoire', loss: 'Défaite', draw: 'Nul' };
const matchStatusLabels: Record<string, string> = { scheduled: 'À venir', played: 'Joué', cancelled: 'Annulé' };

const recentResults = computed(() =>
    props.matches
        .filter((m) => m.home_team.id === props.team.id || m.outside_team.id === props.team.id)
        .slice(0, 5)
        .map((m) => {
            const isHome = m.home_team.id === props.team.id;
            const opponent = isHome ? m.outside_team.name : m.home_team.name;

            if (m.status !== 'played' || m.home_team_score === null || m.outside_team_score === null) {
                return {
                    id: m.id,
                    date: m.date,
                    opponent,
                    ownScore: null as number | null,
                    opponentScore: null as number | null,
                    resultLabel: matchStatusLabels[m.status] ?? m.status,
                    color: 'bg-muted-foreground/40',
                    title: `${opponent} — ${matchStatusLabels[m.status] ?? m.status}`,
                };
            }

            const own = isHome ? m.home_team_score : m.outside_team_score;
            const opponentScore = isHome ? m.outside_team_score : m.home_team_score;
            const result = own === opponentScore ? 'draw' : own > opponentScore ? 'win' : 'loss';
            return {
                id: m.id,
                date: m.date,
                opponent,
                ownScore: own,
                opponentScore,
                resultLabel: resultLabels[result],
                color: result === 'win' ? 'bg-emerald-500' : result === 'loss' ? 'bg-red-500' : 'bg-amber-500',
                title: `${opponent} — ${own} - ${opponentScore}`,
            };
        }),
);

const panelData = ref<PanelData | null>(null);
const panelLoading = ref(false);
const summaryDraft = ref('');

function loadPanel(): Promise<void> {
    if (panelData.value || panelLoading.value) return Promise.resolve();
    panelLoading.value = true;
    return axios
        .get(route('referee.teams.panel', props.team.id))
        .then((response) => {
            panelData.value = response.data;
            summaryDraft.value = ownSummary();
        })
        .finally(() => {
            panelLoading.value = false;
        });
}

function refreshPanel(): Promise<void> {
    panelData.value = null;
    return loadPanel();
}

const createFicheOpen = ref(false);

const consultFicheOpen = ref(false);
const consultFicheVersion = ref<FicheVersion | null>(null);

function openConsultFiche(version: FicheVersion) {
    consultFicheVersion.value = version;
    consultFicheOpen.value = true;
}

function onConsultFicheSuccess() {
    if (!consultFicheVersion.value) return;
    const versionId = consultFicheVersion.value.id;
    refreshPanel().then(() => {
        consultFicheVersion.value = panelData.value?.versions.find((v) => v.id === versionId) ?? null;
    });
}

const summaryForm = useForm({ description: '' });

function ownSummary(): string {
    return panelData.value?.summaries.find((s) => s.id === currentUserId())?.pivot.description ?? '';
}

function submitSummary() {
    summaryForm.description = summaryDraft.value;
    summaryForm.post(route('referee.teams.summary.store', props.team.id), {
        preserveScroll: true,
        onSuccess: () => refreshPanel(),
    });
}
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <Card class="group relative h-full cursor-pointer overflow-hidden transition-colors hover:border-primary" @click="loadPanel">
                <div
                    class="pointer-events-none absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-primary/15 to-transparent transition-opacity group-hover:from-primary/25"
                />
                <CardContent class="relative flex flex-col items-center gap-2 pt-6 text-center">
                    <Avatar class="size-16 shrink-0 ring-2 ring-primary/50 ring-offset-2 ring-offset-card transition-all group-hover:ring-primary">
                        <AvatarImage :src="team.logo_url ?? undefined" />
                        <AvatarFallback class="bg-primary/10 text-primary">{{ team.name[0] }}</AvatarFallback>
                    </Avatar>
                    <div>
                        <p class="font-display text-lg font-semibold uppercase tracking-wide">{{ team.name }}</p>
                        <span class="mx-auto mt-0.5 block h-0.5 w-6 rounded-full bg-primary" />
                        <p class="mt-1.5 text-sm text-muted-foreground">{{ team.ville }}</p>
                        <div v-if="recentResults.length" class="mt-2 flex justify-center gap-1">
                            <span
                                v-for="r in recentResults"
                                :key="r.id"
                                class="size-2.5 rounded-full"
                                :class="r.color"
                                :title="r.title"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </DialogTrigger>
        <DialogScrollContent class="h-[90vh] max-h-[90vh] w-[95vw] max-w-5xl content-start space-y-4 overflow-y-auto">
            <div v-if="!panelData" class="text-sm text-muted-foreground">Chargement…</div>
            <template v-else>
                <div class="flex items-center gap-3">
                    <Avatar class="size-10">
                        <AvatarImage :src="team.logo_url ?? undefined" />
                        <AvatarFallback>{{ team.name[0] }}</AvatarFallback>
                    </Avatar>
                    <h2 class="text-lg font-semibold">{{ team.name }}</h2>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium">Derniers résultats</span>
                        <span
                            v-for="r in recentResults"
                            :key="r.id"
                            class="size-3 rounded-full"
                            :class="r.color"
                            :title="r.title"
                        />
                    </div>
                </div>

                <div class="space-y-2 border-b pb-4">
                    <p class="text-sm font-medium">Résumé</p>
                    <Textarea v-model="summaryDraft" rows="2" placeholder="Votre résumé sur cette équipe…" />
                    <Button size="sm" variant="outline" :disabled="summaryForm.processing" @click="submitSummary">
                        Enregistrer
                    </Button>
                    <ul class="space-y-1 text-xs text-muted-foreground">
                        <li v-for="summary in panelData.summaries.filter((s) => s.id !== currentUserId())" :key="summary.id">
                            <span class="font-medium">{{ summary.name }}</span> — {{ summary.pivot.description }}
                        </li>
                    </ul>
                </div>

                <div v-if="recentResults.length" class="space-y-2 border-b pb-4">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Date</TableHead>
                                <TableHead>Adversaire</TableHead>
                                <TableHead>Score</TableHead>
                                <TableHead>Résultat</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="r in recentResults"
                                :key="r.id"
                                class="cursor-pointer"
                                @click="$inertia.visit(route('referee.matches.show', r.id))"
                            >
                                <TableCell>{{ r.date }}</TableCell>
                                <TableCell>{{ r.opponent }}</TableCell>
                                <TableCell>
                                    <span v-if="r.ownScore !== null">{{ r.ownScore }} - {{ r.opponentScore }}</span>
                                    <span v-else class="text-muted-foreground">—</span>
                                </TableCell>
                                <TableCell>{{ r.resultLabel }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <div class="flex gap-2">
                        <Button size="sm" @click="createFicheOpen = true">Nouvelle fiche</Button>
                        <Button as-child size="sm" variant="outline">
                            <Link :href="route('referee.fiches.compare', { team_id: team.id })">Historique</Link>
                        </Button>
                    </div>
                </div>

                <div>
                    <p class="mb-1 text-sm font-medium">Fiches</p>
                    <EmptyState v-if="panelData.versions.length === 0" title="Aucune fiche" />
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nom</TableHead>
                                <TableHead>Créée par</TableHead>
                                <TableHead>Match</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="version in panelData.versions"
                                :key="version.id"
                                class="cursor-pointer"
                                @click="openConsultFiche(version)"
                            >
                                <TableCell>
                                    {{ version.name }}
                                    <Badge v-if="version.id === panelData.versions[0].id" variant="default" class="ml-2">
                                        Actuelle
                                    </Badge>
                                </TableCell>
                                <TableCell>{{ version.creator?.name ?? '—' }}</TableCell>
                                <TableCell>
                                    <Link
                                        v-if="version.match"
                                        :href="route('referee.matches.show', version.match.id)"
                                        class="text-primary underline-offset-2 hover:underline"
                                        @click.stop
                                    >
                                        {{ version.match.home_team?.name ?? '—' }} - {{ version.match.outside_team?.name ?? '—' }}
                                    </Link>
                                    <span v-else>—</span>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </template>
        </DialogScrollContent>
    </Dialog>

    <CreateFicheDialog
        v-model:open="createFicheOpen"
        :team="team"
        :param-descriptions="panelData?.paramDescriptions ?? []"
        :action="route('referee.teams.fiches.store', team.id)"
        @success="refreshPanel"
    />

    <CreateFicheDialog
        v-if="consultFicheVersion"
        v-model:open="consultFicheOpen"
        :team="team"
        :param-descriptions="panelData?.paramDescriptions ?? []"
        :action="route('referee.teams.fiches.store', team.id)"
        :fiche="consultFicheVersion"
        :can-edit="consultFicheVersion.id === panelData?.versions[0]?.id"
        :update-action="route('referee.fiches.update', consultFicheVersion.id)"
        @success="onConsultFicheSuccess"
    />
</template>
