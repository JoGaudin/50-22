<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { ChevronLeftIcon, ChevronRightIcon, ChevronsLeftIcon, ChevronsRightIcon } from 'lucide-vue-next';

const props = withDefaults(
    defineProps<{
        currentPage: number;
        lastPage: number;
        perPage?: number;
        total?: number;
        pageSizeOptions?: number[];
    }>(),
    { perPage: undefined, total: undefined, pageSizeOptions: () => [10, 20, 50, 100] },
);

const emit = defineEmits<{
    'update:page': [page: number];
    'update:perPage': [perPage: number];
}>();
</script>

<template>
    <div class="flex items-center justify-between gap-4">
        <p v-if="total !== undefined" class="text-sm text-muted-foreground">
            {{ total }} résultat{{ total !== 1 ? 's' : '' }}
        </p>

        <div class="flex items-center gap-2">
            <div v-if="perPage !== undefined" class="flex items-center gap-2">
                <span class="text-sm text-muted-foreground">Lignes&nbsp;:</span>
                <Select
                    :model-value="String(perPage)"
                    @update:model-value="emit('update:perPage', Number($event))"
                >
                    <SelectTrigger class="h-8 w-[70px]">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="size in pageSizeOptions"
                            :key="size"
                            :value="String(size)"
                        >
                            {{ size }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <span class="text-sm text-muted-foreground">
                Page {{ currentPage }} / {{ lastPage }}
            </span>

            <div class="flex items-center gap-1">
                <Button
                    variant="outline"
                    size="icon"
                    class="size-8"
                    :disabled="currentPage <= 1"
                    @click="emit('update:page', 1)"
                >
                    <ChevronsLeftIcon class="size-4" />
                </Button>
                <Button
                    variant="outline"
                    size="icon"
                    class="size-8"
                    :disabled="currentPage <= 1"
                    @click="emit('update:page', currentPage - 1)"
                >
                    <ChevronLeftIcon class="size-4" />
                </Button>
                <Button
                    variant="outline"
                    size="icon"
                    class="size-8"
                    :disabled="currentPage >= lastPage"
                    @click="emit('update:page', currentPage + 1)"
                >
                    <ChevronRightIcon class="size-4" />
                </Button>
                <Button
                    variant="outline"
                    size="icon"
                    class="size-8"
                    :disabled="currentPage >= lastPage"
                    @click="emit('update:page', lastPage)"
                >
                    <ChevronsRightIcon class="size-4" />
                </Button>
            </div>
        </div>
    </div>
</template>
