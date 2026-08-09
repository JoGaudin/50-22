<script setup lang="ts">
import EmptyState from '@/Components/EmptyState.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ShieldIcon, TrophyIcon } from 'lucide-vue-next';

defineProps<{
    leagues: Array<{ id: string; name: string; logo: string | null }>;
}>();
</script>

<template>
    <Head title="Mes championnats" />
    <AuthenticatedLayout>
        <template #header>
            <PageHeader
                title="Mes championnats"
                description="Les championnats sur lesquels vous êtes rattaché en tant qu'arbitre."
                :separator="false"
            />
        </template>

        <div class="p-6">
            <EmptyState
                v-if="leagues.length === 0"
                title="Aucun championnat rattaché"
                description="Contactez un administrateur pour être rattaché à un championnat."
                :icon="ShieldIcon"
            />

            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link v-for="league in leagues" :key="league.id" :href="route('referee.leagues.show', league.id)">
                    <Card class="h-full transition-colors hover:border-primary">
                        <CardHeader class="flex flex-row items-center gap-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary/10">
                                <TrophyIcon class="size-5 text-primary" />
                            </div>
                            <CardTitle>{{ league.name }}</CardTitle>
                        </CardHeader>
                        <CardContent class="text-sm text-muted-foreground"> Voir les équipes et les matchs </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
