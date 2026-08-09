<script setup lang="ts">
import Drawer from '@/Components/Drawer.vue';
import FormField from '@/Components/FormField.vue';
import LoadingButton from '@/Components/LoadingButton.vue';
import { Input } from '@/Components/ui/input';
import { computed } from 'vue';

const props = defineProps<{
    open: boolean;
    editing: boolean;
    form: {
        name: string;
        order: number;
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
    <Drawer v-model:open="drawerOpen" :title="editing ? 'Modifier le paramètre' : 'Créer un paramètre'">
        <form class="space-y-4" @submit.prevent="emit('submit')">
            <FormField label="Nom" :error="form.errors.name" required>
                <Input v-model="form.name" />
            </FormField>
            <FormField label="Ordre" :error="form.errors.order">
                <Input v-model.number="form.order" type="number" />
            </FormField>
            <LoadingButton type="submit" :loading="form.processing" class="w-full">
                {{ editing ? 'Enregistrer' : 'Créer' }}
            </LoadingButton>
        </form>
    </Drawer>
</template>
