<script setup lang="ts">
import Drawer from '@/Components/Drawer.vue';
import FormField from '@/Components/FormField.vue';
import LoadingButton from '@/Components/LoadingButton.vue';
import MultiCombobox from '@/Components/MultiCombobox.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Input } from '@/Components/ui/input';
import { computed } from 'vue';

const props = defineProps<{
    open: boolean;
    editing: boolean;
    editingLeague: { name: string; logo_url: string | null } | null;
    userOptions: Array<{ value: string; label: string }>;
    form: {
        name: string;
        logo: File | null;
        referee_user_ids: string[];
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
    <Drawer v-model:open="drawerOpen" :title="editing ? 'Modifier la ligue' : 'Créer une ligue'">
        <form class="space-y-4" @submit.prevent="emit('submit')">
            <FormField label="Nom" :error="form.errors.name" required>
                <Input v-model="form.name" />
            </FormField>
            <FormField label="Logo" :error="form.errors.logo" hint="Image (max 4 Mo)">
                <div class="flex items-center gap-3">
                    <Avatar v-if="editingLeague" class="size-10">
                        <AvatarImage :src="editingLeague.logo_url ?? undefined" />
                        <AvatarFallback>{{ editingLeague.name[0] }}</AvatarFallback>
                    </Avatar>
                    <input type="file" accept="image/*" @change="onLogoChange" />
                </div>
            </FormField>
            <FormField
                label="Arbitres rattachés"
                :error="form.errors.referee_user_ids"
                hint="Ces utilisateurs pourront accéder à l'espace arbitrage pour ce championnat."
            >
                <MultiCombobox v-model="form.referee_user_ids" :options="userOptions" placeholder="Aucun arbitre" />
            </FormField>
            <LoadingButton type="submit" :loading="form.processing" class="w-full">
                {{ editing ? 'Enregistrer' : 'Créer' }}
            </LoadingButton>
        </form>
    </Drawer>
</template>
