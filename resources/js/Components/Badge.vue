<script setup lang="ts">
import { cn } from '@/lib/utils';
import { cva } from 'class-variance-authority';

const badgeVariants = cva(
    'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors',
    {
        variants: {
            variant: {
                default: 'border-transparent bg-primary text-primary-foreground',
                secondary: 'border-transparent bg-secondary text-secondary-foreground',
                destructive: 'border-transparent bg-destructive text-destructive-foreground',
                outline: 'text-foreground',
                success: 'border-transparent bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                warning: 'border-transparent bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            },
        },
        defaultVariants: { variant: 'default' },
    },
);

const props = withDefaults(
    defineProps<{
        variant?: 'default' | 'secondary' | 'destructive' | 'outline' | 'success' | 'warning';
        class?: string;
    }>(),
    { variant: 'default', class: undefined },
);
</script>

<template>
    <span :class="cn(badgeVariants({ variant }), props.class)">
        <slot />
    </span>
</template>
