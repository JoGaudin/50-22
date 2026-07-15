<script setup>
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Search, X } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Rechercher...',
    },
});

const emit = defineEmits(['update:modelValue']);

const searchValue = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', String(value ?? '')),
});

function clearSearch() {
    emit('update:modelValue', '');
}
</script>

<template>
    <div class="relative w-full max-w-md">
        <Search
            class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
            aria-hidden="true"
        />
        <Input
            v-model="searchValue"
            type="search"
            :placeholder="placeholder"
            class="pl-9 pr-9"
        />
        <Button
            v-if="modelValue"
            type="button"
            variant="ghost"
            size="icon"
            class="absolute right-1 top-1/2 size-7 -translate-y-1/2"
            @click="clearSearch"
        >
            <X class="size-4" aria-hidden="true" />
            <span class="sr-only">Effacer la recherche</span>
        </Button>
    </div>
</template>
