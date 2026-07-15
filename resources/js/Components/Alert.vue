<script setup lang="ts">
import { cn } from '@/lib/utils';
import { AlertCircleIcon, CheckCircle2Icon, InfoIcon, TriangleAlertIcon, XIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const props = withDefaults(
    defineProps<{
        variant?: 'default' | 'success' | 'warning' | 'destructive';
        title?: string;
        dismissible?: boolean;
        class?: string;
    }>(),
    { variant: 'default', title: undefined, dismissible: false, class: undefined },
);

const dismissed = ref(false);

const icons = {
    default: InfoIcon,
    success: CheckCircle2Icon,
    warning: TriangleAlertIcon,
    destructive: AlertCircleIcon,
};

const styles = {
    default: 'border-primary/30 bg-primary/5 text-primary',
    success: 'border-emerald-300 bg-emerald-50 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300',
    warning: 'border-amber-300 bg-amber-50 text-amber-800 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-300',
    destructive: 'border-destructive/30 bg-destructive/5 text-destructive',
};
</script>

<template>
    <div
        v-if="!dismissed"
        :class="cn('relative flex gap-3 rounded-lg border p-4 text-sm', styles[variant], props.class)"
        role="alert"
    >
        <component :is="icons[variant]" class="mt-0.5 size-4 shrink-0" />
        <div class="flex-1">
            <p v-if="title" class="mb-1 font-medium">{{ title }}</p>
            <slot />
        </div>
        <button
            v-if="dismissible"
            type="button"
            class="absolute right-3 top-3 opacity-60 hover:opacity-100"
            @click="dismissed = true"
        >
            <XIcon class="size-4" />
        </button>
    </div>
</template>
