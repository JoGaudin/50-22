<script setup>
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    submitUrl: {
        type: String,
        required: true,
    },
});

const form = useForm({
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(props.submitUrl, {
        preserveScroll: true,
    });
}
</script>

<template>
    <GuestLayout>
        <Head title="Définir le mot de passe" />

        <div class="mb-4 text-sm text-muted-foreground">
            Compte : <span class="font-medium text-foreground">{{ email }}</span>
        </div>

        <form
            class="space-y-4"
            @submit.prevent="submit"
        >
            <div>
                <label
                    class="mb-1 block text-sm font-medium text-foreground"
                    for="password"
                >Mot de passe</label>
                <Input
                    id="password"
                    v-model="form.password"
                    type="password"
                    required
                    autocomplete="new-password"
                />
            </div>
            <div>
                <label
                    class="mb-1 block text-sm font-medium text-foreground"
                    for="password_confirmation"
                >Confirmation</label>
                <Input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                />
            </div>
            <p
                v-if="form.errors.password"
                class="text-sm text-destructive"
            >
                {{ form.errors.password }}
            </p>
            <Button
                type="submit"
                class="w-full"
                :disabled="form.processing"
            >
                Enregistrer
            </Button>
        </form>
    </GuestLayout>
</template>
