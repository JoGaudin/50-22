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
    seasons: Array<{ id: string; name: string }>;
    form: {
        number: number;
        start_date: string;
        end_date: string;
        season_id: string;
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
    <Drawer v-model:open="drawerOpen" :title="editing ? 'Modifier la journée' : 'Créer une journée'">
        <form class="space-y-4" @submit.prevent="emit('submit')">
            <FormField label="Numéro" :error="form.errors.number" required>
                <Input v-model.number="form.number" type="number" min="1" />
            </FormField>
            <FormField label="Saison" :error="form.errors.season_id" required>
                <NativeSelect v-model="form.season_id" class="w-full">
                    <NativeSelectOption value="" disabled>Sélectionner…</NativeSelectOption>
                    <NativeSelectOption v-for="s in seasons" :key="s.id" :value="s.id">
                        {{ s.name }}
                    </NativeSelectOption>
                </NativeSelect>
            </FormField>
            <FormField label="Début" :error="form.errors.start_date" required>
                <Input v-model="form.start_date" type="date" />
            </FormField>
            <FormField label="Fin" :error="form.errors.end_date" required>
                <Input v-model="form.end_date" type="date" />
            </FormField>
            <LoadingButton type="submit" :loading="form.processing" class="w-full">
                {{ editing ? 'Enregistrer' : 'Créer' }}
            </LoadingButton>
        </form>
    </Drawer>
</template>
