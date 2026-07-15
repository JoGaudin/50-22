<script setup lang="ts">
import { Label } from '@/Components/ui/label';
import { cn } from '@/lib/utils';

withDefaults(
    defineProps<{
        label?: string;
        error?: string;
        hint?: string;
        required?: boolean;
        class?: string;
    }>(),
    { label: undefined, error: undefined, hint: undefined, required: false, class: undefined },
);
</script>

<template>
    <div :class="cn('flex flex-col gap-1.5', $props.class)">
        <Label v-if="label" class="text-sm font-medium">
            {{ label }}
            <span v-if="required" class="ml-0.5 text-destructive">*</span>
        </Label>

        <slot />

        <p v-if="hint && !error" class="text-xs text-muted-foreground">{{ hint }}</p>
        <p v-if="error" class="text-xs text-destructive">{{ error }}</p>
    </div>
</template>
