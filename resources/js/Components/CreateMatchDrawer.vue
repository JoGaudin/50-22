<script setup lang="ts">
import Drawer from '@/Components/Drawer.vue';
import FormField from '@/Components/FormField.vue';
import LoadingButton from '@/Components/LoadingButton.vue';
import { Checkbox } from '@/Components/ui/checkbox';
import { Input } from '@/Components/ui/input';
import { NativeSelect, NativeSelectOption } from '@/Components/ui/native-select';
import { computed } from 'vue';

const props = defineProps<{
    open: boolean;
    teams: Array<{ id: string; name: string }>;
    journees: Array<{ id: string; number: number }>;
    leagues?: Array<{ id: string; name: string }>;
    leagueId?: string;
    form: {
        date: string;
        journee_id: string;
        home_team_id: string;
        outside_team_id: string;
        referee_name: string;
        as_referee: boolean;
        processing: boolean;
        errors: Record<string, string>;
    };
}>();

const emit = defineEmits<{
    'update:open': [boolean];
    'update:leagueId': [string];
    submit: [];
}>();

const drawerOpen = computed({
    get: () => props.open,
    set: (value: boolean) => emit('update:open', value),
});

const leagueIdModel = computed({
    get: () => props.leagueId ?? '',
    set: (value: string) => emit('update:leagueId', value),
});
</script>

<template>
    <Drawer v-model:open="drawerOpen" title="Créer un match">
        <form class="space-y-4" @submit.prevent="emit('submit')">
            <FormField v-if="leagues" label="Championnat" required>
                <NativeSelect v-model="leagueIdModel" class="w-full">
                    <NativeSelectOption value="" disabled>Sélectionner…</NativeSelectOption>
                    <NativeSelectOption v-for="l in leagues" :key="l.id" :value="l.id">{{ l.name }}</NativeSelectOption>
                </NativeSelect>
            </FormField>
            <fieldset :disabled="leagues && !leagueIdModel" class="space-y-4 disabled:opacity-50">
                <FormField label="Date" :error="form.errors.date" required>
                    <Input v-model="form.date" type="date" />
                </FormField>
                <FormField label="Journée" :error="form.errors.journee_id" required>
                    <NativeSelect v-model="form.journee_id" class="w-full">
                        <NativeSelectOption value="" disabled>Sélectionner…</NativeSelectOption>
                        <NativeSelectOption v-for="j in journees" :key="j.id" :value="j.id">J{{ j.number }}</NativeSelectOption>
                    </NativeSelect>
                </FormField>
                <FormField label="Équipe à domicile" :error="form.errors.home_team_id" required>
                    <NativeSelect v-model="form.home_team_id" class="w-full">
                        <NativeSelectOption value="" disabled>Sélectionner…</NativeSelectOption>
                        <NativeSelectOption v-for="t in teams" :key="t.id" :value="t.id">{{ t.name }}</NativeSelectOption>
                    </NativeSelect>
                </FormField>
                <FormField label="Équipe à l'extérieur" :error="form.errors.outside_team_id" required>
                    <NativeSelect v-model="form.outside_team_id" class="w-full">
                        <NativeSelectOption value="" disabled>Sélectionner…</NativeSelectOption>
                        <NativeSelectOption v-for="t in teams" :key="t.id" :value="t.id">{{ t.name }}</NativeSelectOption>
                    </NativeSelect>
                </FormField>
                <label class="flex cursor-pointer items-center gap-2 text-sm">
                    <Checkbox :model-value="form.as_referee" @update:model-value="(v) => (form.as_referee = !!v)" />
                    <span>Je suis l'arbitre</span>
                </label>
                <FormField
                    label="Nom de l'arbitre"
                    :error="form.errors.referee_name"
                    hint="Optionnel, texte libre"
                >
                    <Input v-model="form.referee_name" :disabled="form.as_referee" />
                </FormField>
            </fieldset>
            <LoadingButton type="submit" :loading="form.processing" class="w-full">Créer</LoadingButton>
        </form>
    </Drawer>
</template>
