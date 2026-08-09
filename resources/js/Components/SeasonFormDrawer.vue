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
    leagues: Array<{ id: string; name: string }>;
    form: {
        name: string;
        start: string;
        end: string;
        league_id: string;
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
    <Drawer v-model:open="drawerOpen" :title="editing ? 'Modifier la saison' : 'Créer une saison'">
        <form class="space-y-4" @submit.prevent="emit('submit')">
            <FormField label="Nom" :error="form.errors.name" required>
                <Input v-model="form.name" />
            </FormField>
            <FormField label="Ligue" :error="form.errors.league_id" required>
                <NativeSelect v-model="form.league_id" class="w-full">
                    <NativeSelectOption value="" disabled>Sélectionner…</NativeSelectOption>
                    <NativeSelectOption v-for="l in leagues" :key="l.id" :value="l.id">
                        {{ l.name }}
                    </NativeSelectOption>
                </NativeSelect>
            </FormField>
            <FormField label="Début" :error="form.errors.start" required>
                <Input v-model="form.start" type="date" />
            </FormField>
            <FormField label="Fin" :error="form.errors.end" required>
                <Input v-model="form.end" type="date" />
            </FormField>
            <LoadingButton type="submit" :loading="form.processing" class="w-full">
                {{ editing ? 'Enregistrer' : 'Créer' }}
            </LoadingButton>
        </form>
    </Drawer>
</template>
