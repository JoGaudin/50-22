import { ref } from 'vue';

export interface ConfirmOptions {
    title?: string;
    description?: string;
    confirmLabel?: string;
    cancelLabel?: string;
    variant?: 'default' | 'destructive';
}

interface ConfirmState extends ConfirmOptions {
    open: boolean;
    resolve: ((confirmed: boolean) => void) | null;
}

const state = ref<ConfirmState>({
    open: false,
    resolve: null,
    title: 'Confirmation',
    description: 'Êtes-vous sûr de vouloir continuer ?',
    confirmLabel: 'Confirmer',
    cancelLabel: 'Annuler',
    variant: 'default',
});

/**
 * Programmatic confirm dialog — works with <ConfirmActionDialog> or any dialog
 * that listens to the shared state via `useConfirmState()`.
 *
 * Usage:
 *   const { confirm } = useConfirm()
 *   const ok = await confirm({ title: 'Supprimer ?', variant: 'destructive' })
 *   if (ok) { ... }
 */
export function useConfirm() {
    function confirm(options: ConfirmOptions = {}): Promise<boolean> {
        return new Promise((resolve) => {
            state.value = {
                open: true,
                resolve,
                title: options.title ?? 'Confirmation',
                description: options.description ?? 'Êtes-vous sûr de vouloir continuer ?',
                confirmLabel: options.confirmLabel ?? 'Confirmer',
                cancelLabel: options.cancelLabel ?? 'Annuler',
                variant: options.variant ?? 'default',
            };
        });
    }

    return { confirm };
}

export function useConfirmState() {
    function respond(confirmed: boolean) {
        state.value.resolve?.(confirmed);
        state.value.open = false;
    }

    return { state, respond };
}
