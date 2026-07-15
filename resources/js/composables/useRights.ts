import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface SharedProps {
    auth?: {
        user?: {
            rights?: string[];
        };
    };
}

/**
 * Returns a `has(slug)` helper to check the current user's rights on the frontend.
 *
 * Usage:
 *   const { has } = useRights()
 *   if (has('admin.users')) { ... }
 */
export function useRights() {
    const page = usePage<SharedProps>();

    const rights = computed<string[]>(() => page.props.auth?.user?.rights ?? []);

    function has(slug: string): boolean {
        return rights.value.includes(slug);
    }

    function hasAny(...slugs: string[]): boolean {
        return slugs.some((s) => has(s));
    }

    function hasAll(...slugs: string[]): boolean {
        return slugs.every((s) => has(s));
    }

    return { rights, has, hasAny, hasAll };
}
