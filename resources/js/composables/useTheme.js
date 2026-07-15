import { useColorMode } from '@vueuse/core';
import { createSharedComposable } from '@vueuse/shared';
import { computed } from 'vue';
import { colorModeOptions } from '@/theme/config';

/**
 * État de thème partagé (une seule instance d’effet de bord sur `document.documentElement`).
 * Pour modifier le comportement, éditer `resources/js/theme/config.js`.
 */
function useThemeInternal() {
    const colorMode = useColorMode(colorModeOptions);

    const isDark = computed(() => colorMode.state.value === 'dark');

    function cycle() {
        const order = ['light', 'dark', 'auto'];
        const current = colorMode.store.value;
        const i = order.indexOf(current);
        colorMode.store.value = order[(i + 1) % order.length];
    }

    return {
        colorMode,
        isDark,
        cycle,
    };
}

export const useTheme = createSharedComposable(useThemeInternal);
