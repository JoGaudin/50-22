<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    code: '',
});

const submit = () => {
    form.post(route('two-factor.challenge.store'), {
        onFinish: () => form.reset('code'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Vérification par e-mail" />

        <div class="mb-4 text-sm text-muted-foreground">
            Un code de vérification à 6 chiffres vient d'être envoyé à votre adresse e-mail.
            Saisissez-le ci-dessous pour finaliser votre connexion.
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="code" value="Code de vérification" />

                <TextInput
                    id="code"
                    v-model="form.code"
                    type="text"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    class="mt-1 block w-full"
                    placeholder="000000"
                    autofocus
                    required
                />

                <InputError class="mt-2" :message="form.errors.code" />
            </div>

            <div class="mt-4 flex items-center justify-between">
                <p class="text-xs text-muted-foreground">
                    Ce code expire dans 5 minutes.
                </p>

                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Vérifier
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a
                :href="route('login')"
                class="text-sm text-muted-foreground underline hover:text-foreground"
            >
                Retour à la connexion
            </a>
        </div>
    </GuestLayout>
</template>
