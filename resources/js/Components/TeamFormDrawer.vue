<script setup lang="ts">
import Drawer from '@/Components/Drawer.vue';
import FormField from '@/Components/FormField.vue';
import LoadingButton from '@/Components/LoadingButton.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Input } from '@/Components/ui/input';
import { computed } from 'vue';

const props = defineProps<{
    open: boolean;
    editing: boolean;
    editingTeam: { name: string; logo_url: string | null } | null;
    form: {
        name: string;
        slug: string;
        stade: string;
        ville: string;
        logo: File | null;
        processing: boolean;
        errors: Record<string, string>;
    };
}>();

const emit = defineEmits<{
    'update:open': [boolean];
    submit: [];
}>();

const drawerOpen = computed({
    get: () => props.open,
    set: (value: boolean) => emit('update:open', value),
});

function onLogoChange(event: Event) {
    props.form.logo = (event.target as HTMLInputElement).files?.[0] ?? null;
}
</script>

<template>
    <Drawer
        v-model:open="drawerOpen"
        :title="editing ? 'Modifier l\'équipe' : 'Créer une équipe'"
    >
        <form class="space-y-4" @submit.prevent="emit('submit')">
            <FormField label="Nom" :error="form.errors.name" required>
                <Input v-model="form.name" />
            </FormField>
            <FormField label="Slug" :error="form.errors.slug" hint="Identifiant unique, ex. stade-montois" required>
                <Input v-model="form.slug" class="font-mono text-sm" />
            </FormField>
            <FormField label="Ville" :error="form.errors.ville" required>
                <Input v-model="form.ville" />
            </FormField>
            <FormField label="Stade" :error="form.errors.stade" required>
                <Input v-model="form.stade" />
            </FormField>
            <FormField label="Logo" :error="form.errors.logo" hint="Image (max 4 Mo)">
                <div class="flex items-center gap-3">
                    <Avatar v-if="editingTeam" class="size-10">
                        <AvatarImage :src="editingTeam.logo_url ?? undefined" />
                        <AvatarFallback>{{ editingTeam.name[0] }}</AvatarFallback>
                    </Avatar>
                    <input type="file" accept="image/*" @change="onLogoChange" />
                </div>
            </FormField>
            <LoadingButton type="submit" :loading="form.processing" class="w-full">
                {{ editing ? 'Enregistrer' : 'Créer' }}
            </LoadingButton>
        </form>
    </Drawer>
</template>
