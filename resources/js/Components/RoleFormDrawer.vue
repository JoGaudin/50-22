<script setup>
import Drawer from '@/Components/Drawer.vue';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
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
    rights: {
        type: Array,
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

function toggleRight(rightId, checked) {
    const ids = props.form.right_ids;
    if (checked && !ids.includes(rightId)) {
        ids.push(rightId);
    }
    if (!checked) {
        const i = ids.indexOf(rightId);
        if (i >= 0) {
            ids.splice(i, 1);
        }
    }
}
</script>

<template>
    <Drawer v-model:open="drawerOpen" title="Nouveau rôle">
        <form id="create-role-form" class="space-y-4" @submit.prevent="submitForm">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-foreground" for="create-name">Nom</label>
                    <Input id="create-name" v-model="form.name" type="text" required />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-foreground" for="create-slug">Identifiant (slug)</label>
                    <Input
                        id="create-slug"
                        v-model="form.slug"
                        type="text"
                        required
                        class="font-mono text-sm"
                        placeholder="ex. moderator"
                    />
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-foreground" for="create-desc">Description</label>
                <Textarea
                    id="create-desc"
                    v-model="form.description"
                    rows="2"
                    class="min-h-[4rem] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                />
            </div>
            <fieldset class="space-y-2">
                <legend class="text-sm font-medium text-foreground">Droits</legend>
                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                    <label v-for="r in rights" :key="r.id" class="flex cursor-pointer items-center gap-2 text-sm">
                        <Checkbox
                            :model-value="form.right_ids.includes(r.id)"
                            @update:model-value="(v) => toggleRight(r.id, !!v)"
                        />
                        <span>{{ r.name }}</span>
                        <span class="font-mono text-xs text-muted-foreground">({{ r.slug }})</span>
                    </label>
                </div>
            </fieldset>
        </form>
        <template #actions>
            <div class="flex w-full items-center justify-end gap-2">
                <Button type="button" variant="outline" @click="closeDrawer">Annuler</Button>
                <Button type="submit" form="create-role-form" :disabled="form.processing">Créer le rôle</Button>
            </div>
        </template>
    </Drawer>
</template>
