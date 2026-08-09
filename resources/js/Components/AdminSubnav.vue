<script setup>
import NavLink from '@/Components/NavLink.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const rights = computed(() => page.props.auth.user?.rights ?? []);

function has(slug) {
    return rights.value.includes(slug);
}

const show = computed(
    () =>
        has('admin.users') ||
        has('admin.roles') ||
        has('admin.rights') ||
        has('admin.settings') ||
        has('admin.leagues') ||
        has('admin.seasons') ||
        has('admin.teams') ||
        has('admin.journees') ||
        has('admin.matches') ||
        has('admin.param-descriptions') ||
        has('admin.fiches'),
);
</script>

<template>
    <nav
        v-if="show"
        class="flex flex-wrap gap-1 border-b border-border pb-3"
        aria-label="Administration"
    >
        <NavLink
            v-if="has('admin.users')"
            :href="route('admin.users.index')"
            :active="route().current('admin.users.*')"
        >
            Utilisateurs
        </NavLink>
        <NavLink
            v-if="has('admin.roles')"
            :href="route('admin.roles.index')"
            :active="route().current('admin.roles.*')"
        >
            Rôles
        </NavLink>
        <NavLink
            v-if="has('admin.rights')"
            :href="route('admin.rights.index')"
            :active="route().current('admin.rights.*')"
        >
            Droits
        </NavLink>
        <NavLink
            v-if="has('admin.settings')"
            :href="route('admin.settings.index')"
            :active="route().current('admin.settings.*')"
        >
            Paramètres
        </NavLink>
        <NavLink
            v-if="has('admin.leagues')"
            :href="route('admin.leagues.index')"
            :active="route().current('admin.leagues.*')"
        >
            Ligues
        </NavLink>
        <NavLink
            v-if="has('admin.seasons')"
            :href="route('admin.seasons.index')"
            :active="route().current('admin.seasons.*')"
        >
            Saisons
        </NavLink>
        <NavLink
            v-if="has('admin.teams')"
            :href="route('admin.teams.index')"
            :active="route().current('admin.teams.*')"
        >
            Équipes
        </NavLink>
        <NavLink
            v-if="has('admin.journees')"
            :href="route('admin.journees.index')"
            :active="route().current('admin.journees.*')"
        >
            Journées
        </NavLink>
        <NavLink
            v-if="has('admin.matches')"
            :href="route('admin.matches.index')"
            :active="route().current('admin.matches.*')"
        >
            Matchs
        </NavLink>
        <NavLink
            v-if="has('admin.param-descriptions')"
            :href="route('admin.param-descriptions.index')"
            :active="route().current('admin.param-descriptions.*')"
        >
            Paramètres de fiche
        </NavLink>
        <NavLink
            v-if="has('admin.fiches')"
            :href="route('admin.fiches.index')"
            :active="route().current('admin.fiches.*')"
        >
            Fiches
        </NavLink>
    </nav>
</template>
