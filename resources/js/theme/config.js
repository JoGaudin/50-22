/**
 * Configuration du thème (mode clair / sombre / système).
 *
 * Pour étendre (ex. thème « sepia », contraste élevé) :
 * 1. Ajouter les variables CSS dans `resources/css/app.css` (ex. `.sepia { --background: … }`).
 * 2. Étendre `modes` ci‑dessous et passer la nouvelle clé à `useColorMode` via `useTheme`.
 * 3. Si besoin d’utilitaires Tailwind, ajouter un variant dans `tailwind.config.js`
 *    (ex. `data-theme="sepia"` ou classe dédiée).
 *
 * La clé localStorage doit rester alignée avec le script inline dans `resources/views/app.blade.php`.
 */
export const COLOR_MODE_STORAGE_KEY = 'app-color-mode';

/** @type {import('@vueuse/core').UseColorModeOptions} */
export const colorModeOptions = {
    selector: 'html',
    attribute: 'class',
    storageKey: COLOR_MODE_STORAGE_KEY,
    initialValue: 'auto',
    /** Tailwind n’utilise que la classe `dark` ; le mode clair ne pose pas de classe. */
    modes: {
        auto: '',
        light: '',
        dark: 'dark',
    },
};
