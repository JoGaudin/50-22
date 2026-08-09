<script setup lang="ts">
import FicheForm from '@/Components/FicheForm.vue';
import FormField from '@/Components/FormField.vue';
import LoadingButton from '@/Components/LoadingButton.vue';
import { Dialog, DialogHeader, DialogScrollContent, DialogTitle } from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { NativeSelect, NativeSelectOption } from '@/Components/ui/native-select';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, reactive, ref, watch } from 'vue';

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

interface AnswerRow {
    fiche_name: string;
    description: string | null;
    created_at: string;
}

const props = defineProps<{
    homeTeam: { id: string; name: string };
    outsideTeam: { id: string; name: string };
    paramDescriptions: Array<{ id: string; name: string }>;
    homeFiche: FicheFull | null;
    outsideFiche: FicheFull | null;
    action: string;
}>();

const open = defineModel<boolean>('open', { default: false });

const editMode = computed(() => !!(props.homeFiche && props.outsideFiche));

function seedValuesFor(fiche: FicheFull | null) {
    return Object.fromEntries(
        props.paramDescriptions.map((param) => [param.id, fiche?.param_descriptions.find((p) => p.id === param.id)?.pivot.description ?? '']),
    );
}

const homeName = ref(props.homeFiche?.name ?? `Fiche ${props.homeTeam.name}`);
const outsideName = ref(props.outsideFiche?.name ?? `Fiche ${props.outsideTeam.name}`);
const homeValues = reactive<Record<string, string>>(seedValuesFor(props.homeFiche));
const outsideValues = reactive<Record<string, string>>(seedValuesFor(props.outsideFiche));

const referenceSide = ref<'home' | 'outside'>('home');
const referenceParamId = ref('');
const referenceAnswers = ref<AnswerRow[]>([]);
const referenceLoading = ref(false);

const referenceTeam = computed(() => (referenceSide.value === 'home' ? props.homeTeam : props.outsideTeam));

function loadReferenceAnswers() {
    if (!referenceParamId.value) {
        referenceAnswers.value = [];
        return;
    }

    referenceLoading.value = true;
    axios
        .get(route('referee.teams.param-answers', { team: referenceTeam.value.id, paramDescription: referenceParamId.value }))
        .then((response) => {
            referenceAnswers.value = response.data;
        })
        .finally(() => {
            referenceLoading.value = false;
        });
}

watch([referenceSide, referenceParamId], loadReferenceAnswers);

const form = useForm({ fiches: [] as unknown[] });
const editHomeForm = useForm({ name: '', descriptions: [] as unknown[] });
const editOutsideForm = useForm({ name: '', descriptions: [] as unknown[] });

const processing = computed(() => (editMode.value ? editHomeForm.processing || editOutsideForm.processing : form.processing));

watch(open, (isOpen) => {
    if (isOpen) {
        form.clearErrors();
        editHomeForm.clearErrors();
        editOutsideForm.clearErrors();
        homeName.value = props.homeFiche?.name ?? `Fiche ${props.homeTeam.name}`;
        outsideName.value = props.outsideFiche?.name ?? `Fiche ${props.outsideTeam.name}`;
        Object.assign(homeValues, seedValuesFor(props.homeFiche));
        Object.assign(outsideValues, seedValuesFor(props.outsideFiche));
        referenceSide.value = 'home';
        referenceParamId.value = '';
        referenceAnswers.value = [];
    }
});

function errorsFor(index: number): Record<string, string> {
    if (editMode.value) {
        const editForm = index === 0 ? editHomeForm : editOutsideForm;
        return Object.fromEntries(props.paramDescriptions.map((param, i) => [param.id, editForm.errors[`descriptions.${i}.description`]]));
    }
    return Object.fromEntries(
        props.paramDescriptions.map((param, i) => [param.id, form.errors[`fiches.${index}.descriptions.${i}.description`]]),
    );
}

const homeNameError = computed(() => (editMode.value ? editHomeForm.errors.name : form.errors['fiches.0.name']));
const outsideNameError = computed(() => (editMode.value ? editOutsideForm.errors.name : form.errors['fiches.1.name']));

function submit() {
    if (editMode.value) {
        submitEdit();
    } else {
        submitCreate();
    }
}

function submitEdit() {
    editHomeForm.name = homeName.value;
    editHomeForm.descriptions = props.paramDescriptions.map((param) => ({
        param_description_id: param.id,
        description: homeValues[param.id] ?? '',
    }));
    editOutsideForm.name = outsideName.value;
    editOutsideForm.descriptions = props.paramDescriptions.map((param) => ({
        param_description_id: param.id,
        description: outsideValues[param.id] ?? '',
    }));

    editHomeForm.put(route('referee.fiches.update', props.homeFiche!.id), {
        preserveScroll: true,
        onSuccess: () => {
            editOutsideForm.put(route('referee.fiches.update', props.outsideFiche!.id), {
                preserveScroll: true,
                onSuccess: () => {
                    open.value = false;
                },
            });
        },
    });
}

function submitCreate() {
    form.fiches = [
        {
            side: 'home',
            name: homeName.value,
            descriptions: props.paramDescriptions.map((param) => ({
                param_description_id: param.id,
                description: homeValues[param.id] ?? '',
            })),
        },
        {
            side: 'outside',
            name: outsideName.value,
            descriptions: props.paramDescriptions.map((param) => ({
                param_description_id: param.id,
                description: outsideValues[param.id] ?? '',
            })),
        },
    ];

    form.post(props.action, {
        preserveScroll: true,
        onSuccess: () => {
            open.value = false;
        },
    });
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogScrollContent class="max-w-6xl">
            <DialogHeader>
                <DialogTitle>
                    {{ editMode ? 'Modifier les fiches' : 'Nouvelles fiches' }} — {{ homeTeam.name }} / {{ outsideTeam.name }}
                </DialogTitle>
            </DialogHeader>

            <form class="grid gap-6 md:grid-cols-3" @submit.prevent="submit">
                <div class="space-y-6">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">{{ homeTeam.name }}</h3>
                    <FormField label="Nom" :error="homeNameError" required>
                        <Input v-model="homeName" />
                    </FormField>
                    <FicheForm v-model="homeValues" :param-descriptions="paramDescriptions" :errors="errorsFor(0)" />
                </div>

                <div class="space-y-6 border-t pt-6 md:border-l md:border-t-0 md:pl-6 md:pt-0">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">{{ outsideTeam.name }}</h3>
                    <FormField label="Nom" :error="outsideNameError" required>
                        <Input v-model="outsideName" />
                    </FormField>
                    <FicheForm v-model="outsideValues" :param-descriptions="paramDescriptions" :errors="errorsFor(1)" />
                </div>

                <div class="space-y-4 border-t pt-6 md:border-l md:border-t-0 md:pl-6 md:pt-0">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Référence</h3>
                    <FormField label="Équipe">
                        <NativeSelect v-model="referenceSide" class="w-full">
                            <NativeSelectOption value="home">{{ homeTeam.name }}</NativeSelectOption>
                            <NativeSelectOption value="outside">{{ outsideTeam.name }}</NativeSelectOption>
                        </NativeSelect>
                    </FormField>
                    <FormField label="Paramètre">
                        <NativeSelect v-model="referenceParamId" class="w-full">
                            <NativeSelectOption value="">Sélectionner…</NativeSelectOption>
                            <NativeSelectOption v-for="param in paramDescriptions" :key="param.id" :value="param.id">
                                {{ param.name }}
                            </NativeSelectOption>
                        </NativeSelect>
                    </FormField>

                    <p v-if="referenceLoading" class="text-sm text-muted-foreground">Chargement…</p>
                    <p v-else-if="referenceParamId && referenceAnswers.length === 0" class="text-sm text-muted-foreground">
                        Aucune réponse trouvée.
                    </p>
                    <ul v-else class="space-y-2">
                        <li v-for="(answer, i) in referenceAnswers" :key="i" class="rounded-md border border-border p-2 text-sm">
                            <p class="font-medium">{{ answer.fiche_name }}</p>
                            <p class="whitespace-pre-wrap text-muted-foreground">{{ answer.description || '—' }}</p>
                        </li>
                    </ul>
                </div>

                <LoadingButton type="submit" :loading="processing" class="md:col-span-3">
                    {{ editMode ? 'Enregistrer' : 'Créer les fiches' }}
                </LoadingButton>
            </form>
        </DialogScrollContent>
    </Dialog>
</template>
