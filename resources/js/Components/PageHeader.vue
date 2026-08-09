<script setup lang="ts">
withDefaults(
    defineProps<{
        title: string;
        description?: string;
        eyebrow?: string;
        separator?: boolean;
    }>(),
    { description: undefined, eyebrow: undefined, separator: true },
);
</script>

<template>
    <div class="flex flex-col gap-4" :class="separator ? 'border-y border-secondary/40 py-3' : ''">
        <div class="flex items-baseline justify-between gap-4">
            <div class="min-w-0 flex-1">
                <p v-if="eyebrow" class="text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">{{ eyebrow }}</p>
                <h2 class="mt-1 truncate font-display text-3xl font-bold uppercase tracking-tight first:mt-0">{{ title }}</h2>
                <p v-if="description" class="mt-1 text-sm text-muted-foreground">
                    {{ description }}
                </p>
            </div>

            <div v-if="$slots.actions" class="flex shrink-0 items-center gap-2">
                <slot name="actions" />
            </div>
        </div>
    </div>
</template>
