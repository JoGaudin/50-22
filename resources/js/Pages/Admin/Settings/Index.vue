<script setup>
import AdminSubnav from '@/Components/AdminSubnav.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    two_fa_mode: props.settings['2fa_mode'] ?? 'none',
});

function updateMode(value) {
    form.two_fa_mode = value;
    form.patch(route('admin.settings.update'), {
        preserveScroll: true,
        onSuccess: () => toast.success('Paramètres mis à jour.'),
        onError: () => toast.error('Une erreur est survenue.'),
    });
}

const modes = [
    {
        value: 'none',
        label: 'Désactivée',
        description: 'Aucune vérification supplémentaire à la connexion.',
    },
    {
        value: 'email',
        label: 'Par e-mail',
        description: 'Un code à 6 chiffres est envoyé par e-mail à chaque connexion.',
    },
];
</script>

<template>
    <Head title="Administration — Paramètres" />

    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-4">
                <AdminSubnav />
                <h2 class="text-xl font-semibold leading-tight text-foreground">
                    Paramètres
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <section class="overflow-hidden bg-card shadow-sm sm:rounded-lg border border-border">
                    <div class="border-b border-border px-6 py-4">
                        <h3 class="text-lg font-medium text-card-foreground">
                            Sécurité
                        </h3>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Paramètres de sécurité globaux de l'application.
                        </p>
                    </div>

                    <div class="px-6 py-5">
                        <p class="text-sm font-medium text-foreground mb-1">
                            Authentification à deux facteurs (2FA)
                        </p>
                        <p class="text-sm text-muted-foreground mb-4">
                            Choisissez la méthode de vérification supplémentaire appliquée à tous les utilisateurs lors de la connexion.
                        </p>

                        <div class="space-y-3">
                            <label
                                v-for="mode in modes"
                                :key="mode.value"
                                :class="[
                                    'flex cursor-pointer items-start gap-3 rounded-lg border p-4 transition-colors',
                                    form.two_fa_mode === mode.value
                                        ? 'border-primary bg-primary/5'
                                        : 'border-border hover:bg-muted/40',
                                ]"
                            >
                                <input
                                    type="radio"
                                    :value="mode.value"
                                    :checked="form.two_fa_mode === mode.value"
                                    :disabled="form.processing"
                                    class="mt-0.5 accent-primary"
                                    @change="updateMode(mode.value)"
                                />
                                <div>
                                    <p class="text-sm font-medium text-foreground">
                                        {{ mode.label }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ mode.description }}
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
