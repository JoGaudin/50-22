import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { toast } from 'vue-sonner';

export function useFlashToast() {
    const page = usePage();

    const flash = () => page.props.flash as { success?: string | null; error?: string | null } | undefined;

    watch(
        () => flash()?.success,
        (val) => { if (val) toast.success(val); },
    );

    watch(
        () => flash()?.error,
        (val) => { if (val) toast.error(val); },
    );
}
