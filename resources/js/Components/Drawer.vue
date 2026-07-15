<script setup>
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/Components/ui/sheet';
import { computed, useSlots } from 'vue';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:open']);
const slots = useSlots();

const drawerOpen = computed({
    get: () => props.open,
    set: (value) => emit('update:open', value),
});
</script>

<template>
    <Sheet v-model:open="drawerOpen">
        <SheetContent
            side="right"
            class="w-full p-0 sm:max-w-xl"
        >
            <SheetHeader class="border-b border-border p-6 pr-12">
                <SheetTitle>{{ title }}</SheetTitle>
                <SheetDescription v-if="description">
                    {{ description }}
                </SheetDescription>
            </SheetHeader>

            <div class="min-h-0 flex-1 overflow-y-auto p-6">
                <slot />
            </div>

            <SheetFooter
                v-if="slots.actions"
                class="border-t border-border px-6 py-4"
            >
                <slot name="actions" />
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>
