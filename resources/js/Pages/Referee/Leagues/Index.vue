<script setup lang="ts">
import AddLeagueDropdown from '@/Components/AddLeagueDropdown.vue';
import EmptyState from '@/Components/EmptyState.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import LeaveLeagueButton from '@/Components/LeaveLeagueButton.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ShieldIcon, TrophyIcon } from 'lucide-vue-next';

defineProps<{
    leagues: Array<{ id: string; name: string; logo: string | null; logo_url: string | null }>;
    availableLeagues: Array<{ id: string; name: string; logo: string | null; logo_url: string | null }>;
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
            >
                <template #actions>
                    <AddLeagueDropdown :leagues="availableLeagues" />
                </template>
            </PageHeader>
        </template>

        <div class="p-6">
            <FlashMessages />

            <EmptyState
                v-if="leagues.length === 0"
                title="Aucun championnat rattaché"
                :description="availableLeagues.length > 0 ? 'Ajoutez-en un via le bouton ci-dessus.' : 'Contactez un administrateur pour créer un championnat.'"
                :icon="ShieldIcon"
            />

            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Card v-for="league in leagues" :key="league.id" class="flex h-full flex-col transition-colors hover:border-primary">
                    <Link :href="route('referee.leagues.show', league.id)" class="flex-1">
                        <CardHeader class="flex flex-row items-center gap-3">
                            <Avatar class="size-10">
                                <AvatarImage :src="league.logo_url ?? undefined" />
                                <AvatarFallback class="bg-primary/10">
                                    <TrophyIcon class="size-5 text-primary" />
                                </AvatarFallback>
                            </Avatar>
                            <CardTitle>{{ league.name }}</CardTitle>
                        </CardHeader>
                        <CardContent class="text-sm text-muted-foreground"> Voir les équipes et les matchs </CardContent>
                    </Link>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
