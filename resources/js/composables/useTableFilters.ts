import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

export interface TableFilters {
    search?: string;
    sort_by?: string;
    sort_dir?: 'asc' | 'desc';
    limit?: number;
    page?: number;
    [key: string]: unknown;
}

/**
 * Manages URL-driven table filters with debounced search and sort toggling.
 *
 * Usage:
 *   const { filters, search, setSort, setPage, setLimit } = useTableFilters(route('admin.users.index'), props.usersData.filters)
 */
export function useTableFilters(routeName: string, initial: TableFilters = {}) {
    const filters = ref<TableFilters>({
        search: '',
        sort_by: 'name',
        sort_dir: 'asc',
        limit: 20,
        page: 1,
        ...initial,
    });

    let searchTimer: ReturnType<typeof setTimeout> | null = null;

    function push(overrides: Partial<TableFilters> = {}) {
        const params = { ...filters.value, ...overrides };
        router.get(routeName, params as Record<string, unknown>, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }

    function setSearch(value: string) {
        filters.value.search = value;
        filters.value.page = 1;

        if (searchTimer) clearTimeout(searchTimer);
        searchTimer = setTimeout(() => push(), 350);
    }

    function setSort(column: string) {
        if (filters.value.sort_by === column) {
            filters.value.sort_dir = filters.value.sort_dir === 'asc' ? 'desc' : 'asc';
        } else {
            filters.value.sort_by = column;
            filters.value.sort_dir = 'asc';
        }
        filters.value.page = 1;
        push();
    }

    function setPage(page: number) {
        filters.value.page = page;
        push();
    }

    function setLimit(limit: number) {
        filters.value.limit = limit;
        filters.value.page = 1;
        push();
    }

    return { filters, setSearch, setSort, setPage, setLimit };
}
