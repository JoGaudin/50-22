<script setup lang="ts">
import FormField from '@/Components/FormField.vue';
import { Textarea } from '@/Components/ui/textarea';

defineProps<{
    paramDescriptions: Array<{ id: string; name: string }>;
    errors?: Record<string, string>;
}>();

const values = defineModel<Record<string, string>>({ default: () => ({}) });
</script>

<template>
    <div class="space-y-4">
        <FormField v-for="param in paramDescriptions" :key="param.id" :label="param.name" :error="errors?.[param.id]">
            <Textarea v-model="values[param.id]" rows="3" />
        </FormField>
    </div>
</template>
