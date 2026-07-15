<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Toaster } from '@/Components/ui/sonner';
import { useFlashToast } from '@/composables/useFlashToast';
import { Link } from '@inertiajs/vue3';

useFlashToast();
</script>

<template>
    <Toaster rich-colors position="top-right" />
    <div
        class="relative flex min-h-screen flex-col items-center bg-background pt-6 sm:justify-center sm:pt-0"
    >
        <div class="absolute end-4 top-4 sm:end-6 sm:top-6">
            <ThemeToggle />
        </div>
        <div>
            <Link href="/">
                <ApplicationLogo
                    class="h-20 w-20 fill-current text-muted-foreground"
                />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden border border-border bg-card px-6 py-4 text-card-foreground shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
