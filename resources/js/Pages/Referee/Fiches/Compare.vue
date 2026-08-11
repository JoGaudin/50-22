<script setup lang="ts">
import FormField from '@/Components/FormField.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Input } from '@/Components/ui/input';
import { NativeSelect, NativeSelectOption } from '@/Components/ui/native-select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface ParamValue {
    id: string;
    name: string;
    pivot: { description: string };
}

interface FicheRow {
    id: string;
    name: string;
    creator: { id: string; name: string } | null;
    param_descriptions: ParamValue[];
    created_at: string;
    match: { id: string; journee?: { number: number } | null; date?: string } | null;
}

const props = defineProps<{
    teams: Array<{ id: string; name: string }>;
    team: { id: string; name: string } | null;
    fiches: FicheRow[];
    paramDescriptions: Array<{ id: string; name: string }>;
}>();

const query = new URLSearchParams(window.location.search);
const teamId = ref(query.get('team_id') ?? props.team?.id ?? '');

function reload() {
    router.get(
        route('referee.fiches.compare'),
        { team_id: teamId.value || undefined },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

function answerFor(fiche: FicheRow, paramId: string): string {
    return fiche.param_descriptions.find((p) => p.id === paramId)?.pivot.description || '—';
}

const search = ref('');
const sortDir = ref<'asc' | 'desc'>('desc');

function toggleSort() {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
}

const filteredFiches = computed(() => {
    const term = search.value.trim().toLowerCase();
    const dir = sortDir.value === 'asc' ? 1 : -1;

    return props.fiches
        .filter(
            (fiche) =>
                !term ||
                fiche.name.toLowerCase().includes(term) ||
                fiche.param_descriptions.some((p) => p.pivot.description?.toLowerCase().includes(term)),
        )
        .slice()
        .sort((a, b) => a.created_at.localeCompare(b.created_at) * dir);
});
</script>

<template>
    <Head title="Historique des fiches" />
    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Historique des fiches" description="Choisissez une équipe pour voir tout son historique." :separator="false" />
        </template>

        <div class="space-y-6 p-6">
            <FormField label="Équipe" class="max-w-xs">
                <NativeSelect v-model="teamId" class="w-full" @change="reload">
                    <NativeSelectOption value="">Sélectionner…</NativeSelectOption>
                    <NativeSelectOption v-for="t in teams" :key="t.id" :value="t.id">{{ t.name }}</NativeSelectOption>
                </NativeSelect>
            </FormField>

            <div v-if="team" class="space-y-4">
                <Input v-model="search" placeholder="Rechercher par nom de fiche ou valeur…" class="max-w-sm" />

                <div class="overflow-x-auto">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="cursor-pointer select-none" @click="toggleSort">
                                Fiche {{ sortDir === 'asc' ? '▲' : '▼' }}
                            </TableHead>
                            <TableHead>Match</TableHead>
                            <TableHead v-for="param in paramDescriptions" :key="param.id">{{ param.name }}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="fiche in filteredFiches" :key="fiche.id">
                            <TableCell>
                                <p class="font-medium">{{ fiche.name }}</p>
                                <p class="text-xs text-muted-foreground">{{ fiche.creator?.name ?? '—' }}</p>
                            </TableCell>
                            <TableCell>
                                <Link
                                    v-if="fiche.match"
                                    :href="route('referee.matches.show', fiche.match.id)"
                                    class="text-primary underline-offset-2 hover:underline"
                                >
                                    J{{ fiche.match.journee?.number ?? '—' }}
                                </Link>
                                <span v-else>—</span>
                            </TableCell>
                            <TableCell v-for="param in paramDescriptions" :key="param.id" class="whitespace-pre-wrap">
                                {{ answerFor(fiche, param.id) }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
