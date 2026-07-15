<script setup lang="ts">
import { Input } from '@/Components/ui/input';
import { useDebounceFn } from '@vueuse/core';
import { SearchIcon, XIcon } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue?: string;
        placeholder?: string;
        debounce?: number;
    }>(),
    { modelValue: '', placeholder: 'Rechercher…', debounce: 350 },
);

const emit = defineEmits<{
    'update:modelValue': [value: string];
    search: [value: string];
}>();

const localValue = ref(props.modelValue);

watch(
    () => props.modelValue,
    (v) => {
        localValue.value = v;
    },
);

const onSearch = useDebounceFn((value: string) => {
    emit('search', value);
}, props.debounce);

function handleInput(e: Event) {
    const value = (e.target as HTMLInputElement).value;
    localValue.value = value;
    emit('update:modelValue', value);
    onSearch(value);
}

function clear() {
    localValue.value = '';
    emit('update:modelValue', '');
    emit('search', '');
}
</script>

<template>
    <div class="relative">
        <SearchIcon class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
        <Input
            :value="localValue"
            :placeholder="placeholder"
            class="pl-9"
            :class="localValue ? 'pr-9' : ''"
            v-bind="$attrs"
            @input="handleInput"
        />
        <button
            v-if="localValue"
            type="button"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
            @click="clear"
        >
            <XIcon class="size-4" />
        </button>
    </div>
</template>
