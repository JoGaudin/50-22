<script setup lang="ts">
import ConfirmActionDialog from '@/Components/ConfirmActionDialog.vue';
import { Button } from '@/Components/ui/button';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    league: { id: string; name: string };
}>();

function leave() {
    router.delete(route('referee.leagues.leave', props.league.id));
}
</script>

<template>
    <ConfirmActionDialog
        title="Retirer ce championnat"
        :description="`Vous ne serez plus rattaché à « ${league.name} ».`"
        confirm-label="Retirer"
        @confirm="leave"
    >
        <template #trigger>
            <Button variant="outline" size="sm">Retirer</Button>
        </template>
    </ConfirmActionDialog>
</template>
