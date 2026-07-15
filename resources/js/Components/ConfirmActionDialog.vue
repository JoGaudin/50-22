<script setup>
import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/Components/ui/dialog';
import { computed, ref } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    confirmLabel: {
        type: String,
        default: 'Confirmer',
    },
    cancelLabel: {
        type: String,
        default: 'Annuler',
    },
    confirmVariant: {
        type: String,
        default: 'destructive',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    closeOnConfirm: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['confirm']);
const open = ref(false);
const canOpen = computed(() => !props.disabled);

function onOpenChange(nextOpen) {
    if (nextOpen && !canOpen.value) {
        return;
    }

    open.value = nextOpen;
}

function closeDialog() {
    open.value = false;
}

function confirmAction() {
    emit('confirm');

    if (props.closeOnConfirm) {
        closeDialog();
    }
}
</script>

<template>
    <Dialog
        :open="open"
        @update:open="onOpenChange"
    >
        <DialogTrigger as-child>
            <slot name="trigger" />
        </DialogTrigger>

        <DialogContent
            class="sm:max-w-md"
            :show-close-button="false"
            data-stop-row-click
            @click.stop
        >
            <DialogHeader>
                <DialogTitle>
                    {{ title }}
                </DialogTitle>
                <DialogDescription v-if="description">
                    {{ description }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2 sm:justify-end">
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    data-stop-row-click
                    @click.stop="closeDialog"
                >
                    {{ cancelLabel }}
                </Button>
                <Button
                    type="button"
                    :variant="confirmVariant"
                    size="sm"
                    data-stop-row-click
                    @click.stop="confirmAction"
                >
                    {{ confirmLabel }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
