<script setup lang="ts">
import Drawer from '@/Components/Drawer.vue';
import FormField from '@/Components/FormField.vue';
import LoadingButton from '@/Components/LoadingButton.vue';
import { Input } from '@/Components/ui/input';
import { NativeSelect, NativeSelectOption } from '@/Components/ui/native-select';
import { computed } from 'vue';

const props = defineProps<{
    open: boolean;
    editing: boolean;
    journees: Array<{ id: string; number: number }>;
    teams: Array<{ id: string; name: string }>;
    statuses: Array<{ value: string; label: string }>;
    form: {
        date: string;
        journee_id: string;
        home_team_id: string;
        outside_team_id: string;
        home_team_score: string | number;
        outside_team_score: string | number;
        referee_name: string;
        status: string;
        processing: boolean;
        errors: Record<string, string>;
    };
}>();

const emit = defineEmits<{
    'update:open': [boolean];
    submit: [];
}>();

const drawerOpen = computed({
    get: () => props.open,
    set: (value: boolean) => emit('update:open', value),
});
</script>

<template>
    <Drawer v-model:open="drawerOpen" :title="editing ? 'Modifier le match' : 'Créer un match'">
        <form class="space-y-4" @submit.prevent="emit('submit')">
            <FormField label="Date" :error="form.errors.date" required>
                <Input v-model="form.date" type="date" />
            </FormField>
            <FormField label="Journée" :error="form.errors.journee_id" required>
                <NativeSelect v-model="form.journee_id" class="w-full">
                    <NativeSelectOption value="" disabled>Sélectionner…</NativeSelectOption>
                    <NativeSelectOption v-for="j in journees" :key="j.id" :value="j.id">
                        Journée {{ j.number }}
                    </NativeSelectOption>
                </NativeSelect>
            </FormField>
            <FormField label="Équipe à domicile" :error="form.errors.home_team_id" required>
                <NativeSelect v-model="form.home_team_id" class="w-full">
                    <NativeSelectOption value="" disabled>Sélectionner…</NativeSelectOption>
                    <NativeSelectOption v-for="t in teams" :key="t.id" :value="t.id">
                        {{ t.name }}
                    </NativeSelectOption>
                </NativeSelect>
            </FormField>
            <FormField label="Équipe à l'extérieur" :error="form.errors.outside_team_id" required>
                <NativeSelect v-model="form.outside_team_id" class="w-full">
                    <NativeSelectOption value="" disabled>Sélectionner…</NativeSelectOption>
                    <NativeSelectOption v-for="t in teams" :key="t.id" :value="t.id">
                        {{ t.name }}
                    </NativeSelectOption>
                </NativeSelect>
            </FormField>
            <div class="grid grid-cols-2 gap-4">
                <FormField label="Score domicile" :error="form.errors.home_team_score">
                    <Input v-model="form.home_team_score" type="number" min="0" />
                </FormField>
                <FormField label="Score extérieur" :error="form.errors.outside_team_score">
                    <Input v-model="form.outside_team_score" type="number" min="0" />
                </FormField>
            </div>
            <FormField label="Nom de l'arbitre" :error="form.errors.referee_name" hint="Optionnel, texte libre">
                <Input v-model="form.referee_name" />
            </FormField>
            <FormField label="Statut" :error="form.errors.status" required>
                <NativeSelect v-model="form.status" class="w-full">
                    <NativeSelectOption v-for="s in statuses" :key="s.value" :value="s.value">
                        {{ s.label }}
                    </NativeSelectOption>
                </NativeSelect>
            </FormField>
            <LoadingButton type="submit" :loading="form.processing" class="w-full">
                {{ editing ? 'Enregistrer' : 'Créer' }}
            </LoadingButton>
        </form>
    </Drawer>
</template>
