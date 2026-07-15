<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import { Loader2Icon } from 'lucide-vue-next';
import type { ButtonHTMLAttributes } from 'vue';

withDefaults(
    defineProps<{
        loading?: boolean;
        disabled?: boolean;
        variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
        size?: 'default' | 'sm' | 'lg' | 'icon';
        type?: ButtonHTMLAttributes['type'];
        class?: string;
    }>(),
    {
        loading: false,
        disabled: false,
        variant: 'default',
        size: 'default',
        type: 'button',
        class: undefined,
    },
);
</script>

<template>
    <Button
        :variant="variant"
        :size="size"
        :type="type"
        :disabled="disabled || loading"
        :class="$props.class"
        v-bind="$attrs"
    >
        <Loader2Icon v-if="loading" class="mr-2 size-4 animate-spin" />
        <slot />
    </Button>
</template>
