<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import type { LucideIcon } from 'lucide-vue-next';
import { InboxIcon } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        title?: string;
        description?: string;
        icon?: LucideIcon;
        actionLabel?: string;
    }>(),
    {
        title: 'Aucun résultat',
        description: undefined,
        icon: undefined,
        actionLabel: undefined,
    },
);

defineEmits<{ action: [] }>();
</script>

<template>
    <div class="flex flex-col items-center justify-center gap-3 py-16 text-center">
        <div class="flex size-12 items-center justify-center rounded-full bg-muted">
            <component :is="icon ?? InboxIcon" class="size-6 text-muted-foreground" />
        </div>
        <div class="space-y-1">
            <p class="text-sm font-medium">{{ title }}</p>
            <p v-if="description" class="text-sm text-muted-foreground">{{ description }}</p>
        </div>
        <slot>
            <Button v-if="actionLabel" variant="outline" size="sm" @click="$emit('action')">
                {{ actionLabel }}
            </Button>
        </slot>
    </div>
</template>
