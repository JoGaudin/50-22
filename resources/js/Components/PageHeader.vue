<script setup lang="ts">
import { Separator } from '@/Components/ui/separator';

withDefaults(
    defineProps<{
        title: string;
        description?: string;
        separator?: boolean;
    }>(),
    { description: undefined, separator: true },
);
</script>

<template>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-xl font-semibold tracking-tight">{{ title }}</h2>
                <p v-if="description" class="mt-0.5 text-sm text-muted-foreground">
                    {{ description }}
                </p>
            </div>

            <div v-if="$slots.actions" class="flex shrink-0 items-center gap-2">
                <slot name="actions" />
            </div>
        </div>

        <Separator v-if="separator" />
    </div>
</template>
