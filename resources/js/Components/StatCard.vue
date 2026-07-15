<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import type { LucideIcon } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        title: string;
        value: string | number;
        description?: string;
        icon?: LucideIcon;
        trend?: { value: number; label: string };
    }>(),
    { description: undefined, icon: undefined, trend: undefined },
);
</script>

<template>
    <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium text-muted-foreground">{{ title }}</CardTitle>
            <component :is="icon" v-if="icon" class="size-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
            <div class="text-2xl font-bold">{{ value }}</div>
            <p v-if="description" class="mt-1 text-xs text-muted-foreground">
                {{ description }}
            </p>
            <p v-if="trend" class="mt-1 flex items-center gap-1 text-xs">
                <span
                    :class="[
                        'font-medium',
                        trend.value >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-destructive',
                    ]"
                >
                    {{ trend.value >= 0 ? '+' : '' }}{{ trend.value }}%
                </span>
                <span class="text-muted-foreground">{{ trend.label }}</span>
            </p>
        </CardContent>
    </Card>
</template>
