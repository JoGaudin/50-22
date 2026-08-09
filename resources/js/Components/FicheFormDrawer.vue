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
    teams: Array<{ id: string; name: string }>;
    form: {
        name: string;
        team_id: string;
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
    <Drawer v-model:open="drawerOpen" :title="editing ? 'Modifier la fiche' : 'Créer une fiche'">
        <form class="space-y-4" @submit.prevent="emit('submit')">
            <FormField label="Nom" :error="form.errors.name" required>
                <Input v-model="form.name" />
            </FormField>
            <FormField label="Équipe" :error="form.errors.team_id" required>
                <NativeSelect v-model="form.team_id" class="w-full">
                    <NativeSelectOption value="" disabled>Sélectionner…</NativeSelectOption>
                    <NativeSelectOption v-for="t in teams" :key="t.id" :value="t.id">
                        {{ t.name }}
                    </NativeSelectOption>
                </NativeSelect>
            </FormField>
            <LoadingButton type="submit" :loading="form.processing" class="w-full">
                {{ editing ? 'Enregistrer' : 'Créer' }}
            </LoadingButton>
        </form>
    </Drawer>
</template>
