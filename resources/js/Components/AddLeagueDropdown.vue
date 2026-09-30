<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/Components/ui/dropdown-menu';
import { router } from '@inertiajs/vue3';
import { PlusIcon } from 'lucide-vue-next';

defineProps<{
    leagues: Array<{ id: string; name: string }>;
}>();

function join(league: { id: string }) {
    router.post(route('referee.leagues.join', league.id), {}, { preserveScroll: true });
}
</script>

<template>
    <DropdownMenu v-if="leagues.length > 0">
        <DropdownMenuTrigger as-child>
            <Button size="sm"><PlusIcon class="mr-2 size-4" />Ajouter</Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem v-for="league in leagues" :key="league.id" @click="join(league)">
                {{ league.name }}
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
