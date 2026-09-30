<script setup lang="ts">
import Badge from '@/Components/Badge.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import CreateDualFicheDialog from '@/Components/CreateDualFicheDialog.vue';
import FicheDetail from '@/Components/FicheDetail.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import FormField from '@/Components/FormField.vue';
import LoadingButton from '@/Components/LoadingButton.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Button } from '@/Components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { NativeSelect, NativeSelectOption } from '@/Components/ui/native-select';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface ParamValue {
    id: string;
    name: string;
    pivot: { description: string };
}

interface FicheFull {
    id: string;
    name: string;
    param_descriptions: ParamValue[];
}

const props = defineProps<{
    match: {
        id: string;
        date: string;
        status: string;
        home_team_score: number | null;
        outside_team_score: number | null;
        home_team: { id: string; name: string };
        outside_team: { id: string; name: string };
        journee: { id: string; number: number; season: { id: string; name: string; league: { id: string; name: string } } };
        referee: { id: string; name: string } | null;
        referee_name: string | null;
        home_fiche_id: string | null;
        outside_fiche_id: string | null;
    };
    homeFiche: FicheFull | null;
    outsideFiche: FicheFull | null;
    paramDescriptions: Array<{ id: string; name: string }>;
    journees: Array<{ id: string; number: number }>;
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

function claimMatch() {
    router.patch(route('referee.matches.claim', props.match.id), {}, { preserveScroll: true });
}

const createFicheOpen = ref(false);

const editMatchOpen = ref(false);
const editForm = useForm({
    date: props.match.date.slice(0, 10),
    journee_id: props.match.journee.id,
    home_team_score: props.match.home_team_score,
    outside_team_score: props.match.outside_team_score,
    status: props.match.status,
});

function submitEditMatch() {
    editForm.patch(route('referee.matches.update', props.match.id), {
        preserveScroll: true,
        onSuccess: () => {
            editMatchOpen.value = false;
        },
    });
}
</script>

<template>
    <Head :title="`${match.home_team.name} — ${match.outside_team.name}`" />
    <AuthenticatedLayout>
        <template #header>
            <PageHeader
                :title="`${match.home_team.name} — ${match.outside_team.name}`"
                :description="`${match.date} · J${match.journee.number} · ${match.journee.season.league.name}`"
                :separator="false"
            />
        </template>

        <div class="space-y-6 p-6">
            <FlashMessages />

            <Breadcrumbs
                :items="[
                    { label: match.journee.season.league.name, href: route('referee.leagues.show', match.journee.season.league.id) },
                    { label: `${match.home_team.name} — ${match.outside_team.name}` },
                ]"
            />

            <div class="flex items-center gap-3">
                <Badge :variant="statusVariants[match.status] ?? 'outline'">{{ statusLabels[match.status] ?? match.status }}</Badge>
                <span v-if="match.home_team_score !== null" class="font-display text-sm font-semibold tabular-nums text-primary">
                    {{ match.home_team_score }} - {{ match.outside_team_score }}
                </span>
                <span class="text-sm text-muted-foreground">
                    Arbitre : {{ match.referee?.name ?? match.referee_name ?? '—' }}
                </span>
                <Button v-if="!match.referee && !match.referee_name" size="sm" variant="outline" @click="claimMatch">Je suis l'arbitre</Button>
            </div>

            <div class="flex justify-end gap-2">
                <Button size="sm" variant="outline" @click="editMatchOpen = true">Modifier le match</Button>
                <a v-if="homeFiche && outsideFiche" :href="route('referee.matches.fiches.pdf', match.id)">
                    <Button size="sm" variant="outline">Exporter en PDF</Button>
                </a>
                <Button size="sm" @click="createFicheOpen = true">
                    {{ homeFiche && outsideFiche ? 'Modifier les fiches' : 'Créer les fiches' }}
                </Button>
            </div>

            <div v-if="homeFiche || outsideFiche" class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-3">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                        {{ match.home_team.name }}
                    </h2>
                    <FicheDetail v-if="homeFiche" :fiche="homeFiche" />
                    <p v-else class="text-sm text-muted-foreground">Aucune fiche liée.</p>
                </div>
                <div class="space-y-3">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                        {{ match.outside_team.name }}
                    </h2>
                    <FicheDetail v-if="outsideFiche" :fiche="outsideFiche" />
                    <p v-else class="text-sm text-muted-foreground">Aucune fiche liée.</p>
                </div>
            </div>
        </div>

        <CreateDualFicheDialog
            v-model:open="createFicheOpen"
            :home-team="match.home_team"
            :outside-team="match.outside_team"
            :param-descriptions="paramDescriptions"
            :home-fiche="homeFiche"
            :outside-fiche="outsideFiche"
            :action="route('referee.matches.fiches.store', match.id)"
        />

        <Dialog v-model:open="editMatchOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Modifier le match</DialogTitle>
                </DialogHeader>
                <form class="space-y-4" @submit.prevent="submitEditMatch">
                    <div class="grid grid-cols-2 gap-4">
                        <FormField label="Date" :error="editForm.errors.date" required>
                            <Input v-model="editForm.date" type="date" />
                        </FormField>
                        <FormField label="Journée" :error="editForm.errors.journee_id" required>
                            <NativeSelect v-model="editForm.journee_id" class="w-full">
                                <NativeSelectOption v-for="journee in journees" :key="journee.id" :value="journee.id">
                                    J{{ journee.number }}
                                </NativeSelectOption>
                            </NativeSelect>
                        </FormField>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <FormField label="Score domicile" :error="editForm.errors.home_team_score">
                            <Input v-model.number="editForm.home_team_score" type="number" min="0" />
                        </FormField>
                        <FormField label="Score extérieur" :error="editForm.errors.outside_team_score">
                            <Input v-model.number="editForm.outside_team_score" type="number" min="0" />
                        </FormField>
                    </div>
                    <FormField label="Statut" :error="editForm.errors.status" required>
                        <NativeSelect v-model="editForm.status" class="w-full">
                            <NativeSelectOption v-for="(label, value) in statusLabels" :key="value" :value="value">
                                {{ label }}
                            </NativeSelectOption>
                        </NativeSelect>
                    </FormField>
                    <LoadingButton type="submit" :loading="editForm.processing" class="w-full">Enregistrer</LoadingButton>
                </form>
            </DialogContent>
        </Dialog>
    </AuthenticatedLayout>
</template>
