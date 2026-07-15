<script setup>
import { Button } from '@/Components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { ArrowDown, ArrowUp, ArrowUpDown } from 'lucide-vue-next';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
    columns: {
        type: Array,
        required: true,
    },
    rows: {
        type: Array,
        required: true,
    },
    rowKey: {
        type: [String, Function],
        default: 'id',
    },
    sortBy: {
        type: String,
        default: '',
    },
    sortDirection: {
        type: String,
        default: 'asc',
    },
    loading: {
        type: Boolean,
        default: false,
    },
    loadingMore: {
        type: Boolean,
        default: false,
    },
    hasMore: {
        type: Boolean,
        default: false,
    },
    emptyLabel: {
        type: String,
        default: 'Aucune ligne.',
    },
});

const emit = defineEmits(['sort-change', 'load-more', 'row-click']);
const loadMoreTrigger = ref(null);
let observer = null;

const colCount = computed(() => props.columns.length);

function getRowKey(row, index) {
    if (typeof props.rowKey === 'function') {
        return props.rowKey(row, index);
    }

    return row?.[props.rowKey] ?? index;
}

function nextSortDirection(column) {
    if (props.sortBy !== column.key) {
        return 'asc';
    }

    return props.sortDirection === 'asc' ? 'desc' : 'asc';
}

function requestSort(column) {
    if (!column.sortable || props.loading) {
        return;
    }

    emit('sort-change', {
        sortBy: column.key,
        sortDirection: nextSortDirection(column),
    });
}

function isInteractiveTarget(target) {
    if (!(target instanceof Element)) {
        return false;
    }

    return target.closest('button, a, input, textarea, select, [role="button"], [data-stop-row-click]') !== null;
}

function onRowClick(row, event) {
    if (isInteractiveTarget(event.target)) {
        return;
    }

    emit('row-click', row);
}

function sortIcon(column) {
    if (props.sortBy !== column.key) {
        return ArrowUpDown;
    }

    return props.sortDirection === 'asc' ? ArrowUp : ArrowDown;
}

function getObserverTarget() {
    const target = loadMoreTrigger.value;
    if (!target) {
        return null;
    }

    const element = target.$el ?? target;
    return element instanceof Element ? element : null;
}

function observeLoadMore() {
    if (observer !== null) {
        observer.disconnect();
    }

    const target = getObserverTarget();
    if (!target) {
        return;
    }

    observer = new IntersectionObserver((entries) => {
        const [entry] = entries;
        if (
            entry?.isIntersecting &&
            props.hasMore &&
            !props.loading &&
            !props.loadingMore
        ) {
            emit('load-more');
        }
    }, {
        threshold: 0,
        rootMargin: '200px 0px',
    });

    observer.observe(target);
}

onMounted(() => {
    observeLoadMore();
});

onBeforeUnmount(() => {
    if (observer !== null) {
        observer.disconnect();
    }
});

watch(
    () => [props.rows.length, props.hasMore, props.loading, props.loadingMore],
    async () => {
        await nextTick();
        observeLoadMore();
    },
);
</script>

<template>
    <Table>
        <TableHeader>
            <TableRow>
                <TableHead
                    v-for="column in columns"
                    :key="column.key"
                    :class="column.headerClass"
                >
                    <template v-if="column.sortable">
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            class="h-auto px-0 font-medium"
                            @click="requestSort(column)"
                        >
                            {{ column.label }}
                            <span
                                class="ml-1 text-muted-foreground"
                            >
                                <component
                                    :is="sortIcon(column)"
                                    class="size-3.5"
                                    aria-hidden="true"
                                />
                            </span>
                        </Button>
                    </template>
                    <template v-else>
                        {{ column.label }}
                    </template>
                </TableHead>
            </TableRow>
        </TableHeader>

        <TableBody>
            <TableRow
                v-for="(row, rowIndex) in rows"
                :key="getRowKey(row, rowIndex)"
                class="cursor-pointer hover:bg-muted/40"
                @click="onRowClick(row, $event)"
            >
                <TableCell
                    v-for="column in columns"
                    :key="`${getRowKey(row, rowIndex)}-${column.key}`"
                    :class="column.cellClass"
                >
                    <slot
                        :name="`cell-${column.key}`"
                        :row="row"
                        :value="row[column.key]"
                    >
                        {{ row[column.key] }}
                    </slot>
                </TableCell>
            </TableRow>

            <TableRow v-if="!rows.length && !loading">
                <TableCell
                    :colspan="colCount"
                    class="py-6 text-center text-sm text-muted-foreground"
                >
                    {{ emptyLabel }}
                </TableCell>
            </TableRow>

            <TableRow
                v-if="rows.length"
                ref="loadMoreTrigger"
            >
                <TableCell
                    :colspan="colCount"
                    class="py-4 text-center text-sm text-muted-foreground"
                >
                    <template v-if="loading || loadingMore">
                        Chargement...
                    </template>
                    <template v-else-if="hasMore">
                        Faites défiler pour charger plus
                    </template>
                    <template v-else>
                        Fin de la liste
                    </template>
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
