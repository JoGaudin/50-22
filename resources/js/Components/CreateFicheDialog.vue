<script setup lang="ts">
import FicheDetail from '@/Components/FicheDetail.vue';
import FicheForm from '@/Components/FicheForm.vue';
import FormField from '@/Components/FormField.vue';
import LoadingButton from '@/Components/LoadingButton.vue';
import { Button } from '@/Components/ui/button';
import { Dialog, DialogHeader, DialogScrollContent, DialogTitle } from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface ParamValue {
    id: string;
    name: string;
    pivot: { description: string };
}

interface FicheFull {
    id: string;
    name: string;
    creator?: { name: string } | null;
    param_descriptions: ParamValue[];
}

const props = defineProps<{
    team: { id: string; name: string };
    paramDescriptions: Array<{ id: string; name: string }>;
    action?: string;
    extraFields?: Record<string, string>;
    fiche?: FicheFull | null;
    canEdit?: boolean;
    updateAction?: string;
    opponentFiche?: FicheFull | null;
    opponentTeamName?: string;
}>();

const emit = defineEmits<{ success: [] }>();

const open = defineModel<boolean>('open', { default: false });

const editing = ref(false);

function seedValues() {
    const source = props.fiche?.param_descriptions;
    return Object.fromEntries(
        props.paramDescriptions.map((param) => {
            const previous = source?.find((p) => p.id === param.id);
            return [param.id, previous?.pivot.description ?? ''];
        }),
    );
}

const values = ref<Record<string, string>>(seedValues());

const form = useForm({
    name: props.fiche?.name ?? `Fiche ${props.team.name}`,
});

function resetForm() {
    form.reset();
    form.clearErrors();
    form.name = props.fiche?.name ?? `Fiche ${props.team.name}`;
    values.value = seedValues();
    editing.value = false;
}

watch(open, (isOpen) => {
    if (isOpen) resetForm();
});

watch(
    () => props.fiche,
    () => resetForm(),
);

const title = computed(() => (props.fiche ? props.fiche.name : `Nouvelle fiche — ${props.team.name}`));

function submit() {
    const transformed = form.transform((data) => ({
        ...data,
        ...props.extraFields,
        descriptions: props.paramDescriptions.map((param) => ({
            param_description_id: param.id,
            description: values.value[param.id] ?? '',
        })),
    }));

    const options = {
        preserveScroll: true,
        onSuccess: () => {
            emit('success');
            if (props.fiche) {
                editing.value = false;
            } else {
                open.value = false;
            }
        },
    };

    if (props.fiche) {
        transformed.put(props.updateAction!, options);
    } else {
        transformed.post(props.action!, options);
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogScrollContent :class="opponentFiche ? 'max-w-5xl' : 'max-w-2xl'">
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
            </DialogHeader>

            <div :class="opponentFiche ? 'grid gap-6 md:grid-cols-2' : ''">
                <div v-if="fiche && !editing" class="space-y-4">
                    <div class="flex items-center justify-between gap-2">
                        <p v-if="fiche.creator" class="text-xs text-muted-foreground">Par {{ fiche.creator.name }}</p>
                        <Button v-if="canEdit" size="sm" variant="outline" class="ml-auto" @click="editing = true">Modifier</Button>
                    </div>
                    <FicheDetail :fiche="fiche" :show-header="false" />
                </div>

                <form v-else class="space-y-6" @submit.prevent="submit">
                    <FormField label="Nom" :error="form.errors.name" required>
                        <Input v-model="form.name" />
                    </FormField>

                    <FicheForm v-model="values" :param-descriptions="paramDescriptions" :errors="form.errors" />

                    <div class="flex gap-2">
                        <LoadingButton type="submit" :loading="form.processing" class="flex-1">
                            {{ fiche ? 'Enregistrer' : 'Créer la fiche' }}
                        </LoadingButton>
                        <Button v-if="fiche" type="button" variant="outline" @click="editing = false">Annuler</Button>
                    </div>
                </form>

                <div v-if="opponentFiche" class="space-y-3 border-t pt-6 md:border-l md:border-t-0 md:pl-6 md:pt-0">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                        {{ opponentTeamName }}
                    </h3>
                    <FicheDetail :fiche="opponentFiche" />
                </div>
            </div>
        </DialogScrollContent>
    </Dialog>
</template>
