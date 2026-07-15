import { useForm } from '@inertiajs/vue3';
import type { Ref } from 'vue';
import { ref } from 'vue';

/**
 * Thin wrapper around Inertia's `useForm` that exposes a `drawerOpen` ref
 * and a `submitIn` helper for the common create/edit drawer pattern.
 *
 * Usage:
 *   const { form, drawerOpen, openCreate, openEdit, submit } = useInertiaForm({ name: '', slug: '' })
 */
export function useInertiaForm<T extends Record<string, unknown>>(defaults: T) {
    const form = useForm<T>({ ...defaults });
    const drawerOpen = ref(false);
    const editingId: Ref<number | null> = ref(null);

    function openCreate() {
        form.reset();
        form.clearErrors();
        editingId.value = null;
        drawerOpen.value = true;
    }

    function openEdit(id: number, data: Partial<T>) {
        form.reset();
        form.clearErrors();
        Object.assign(form, data);
        editingId.value = id;
        drawerOpen.value = true;
    }

    function submit(url: string, method: 'post' | 'put' | 'patch' = 'post', options: object = {}) {
        form[method](url, {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                drawerOpen.value = false;
                editingId.value = null;
            },
            ...options,
        });
    }

    return { form, drawerOpen, editingId, openCreate, openEdit, submit };
}
