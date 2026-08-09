<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/Components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/Components/ui/popover';
import { cn } from '@/lib/utils';
import { CheckIcon, ChevronsUpDownIcon } from 'lucide-vue-next';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        options: Array<{ value: string; label: string }>;
        placeholder?: string;
    }>(),
    { placeholder: 'Sélectionner…' },
);

const modelValue = defineModel<string[]>({ default: () => [] });

const selectedLabels = computed(() =>
    props.options
        .filter((o) => modelValue.value.includes(o.value))
        .map((o) => o.label)
        .join(', '),
);
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button variant="outline" type="button" class="w-full justify-between font-normal">
                <span class="truncate">{{ selectedLabels || placeholder }}</span>
                <ChevronsUpDownIcon class="size-4 shrink-0 opacity-50" />
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-72 p-0" align="start">
            <Command v-model="modelValue" multiple>
                <CommandInput placeholder="Rechercher…" />
                <CommandList>
                    <CommandEmpty>Aucun résultat.</CommandEmpty>
                    <CommandGroup>
                        <CommandItem v-for="option in options" :key="option.value" :value="option.value">
                            <CheckIcon
                                :class="cn('size-4', modelValue.includes(option.value) ? 'opacity-100' : 'opacity-0')"
                            />
                            {{ option.label }}
                        </CommandItem>
                    </CommandGroup>
                </CommandList>
            </Command>
        </PopoverContent>
    </Popover>
</template>
