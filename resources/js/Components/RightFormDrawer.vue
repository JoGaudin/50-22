<script setup>
import Drawer from '@/Components/Drawer.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Textarea } from '@/Components/ui/textarea';
import { computed } from 'vue';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    form: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update:open', 'submit']);

const drawerOpen = computed({
    get: () => props.open,
    set: (value) => emit('update:open', value),
});

function closeDrawer() {
    emit('update:open', false);
}

function submitForm() {
    emit('submit');
}
</script>

<template>
    <Drawer
        v-model:open="drawerOpen"
        title="Nouveau droit"
        description="Les nouveaux droits sont automatiquement associés au rôle administrateur."
    >
        <form id="create-right-form" class="space-y-4" @submit.prevent="submitForm">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-foreground" for="cr-name">Nom</label>
                    <Input id="cr-name" v-model="form.name" type="text" required />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-foreground" for="cr-slug">Identifiant (slug)</label>
                    <Input
                        id="cr-slug"
                        v-model="form.slug"
                        type="text"
                        required
                        class="font-mono text-sm"
                        placeholder="ex. reports.view"
                    />
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-foreground" for="cr-desc">Description</label>
                <Textarea
                    id="cr-desc"
                    v-model="form.description"
                    rows="2"
                    class="min-h-[4rem] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                />
            </div>
        </form>
        <template #actions>
            <div class="flex w-full items-center justify-end gap-2">
                <Button type="button" variant="outline" @click="closeDrawer">Annuler</Button>
                <Button type="submit" form="create-right-form" :disabled="form.processing">Créer le droit</Button>
            </div>
        </template>
    </Drawer>
</template>
