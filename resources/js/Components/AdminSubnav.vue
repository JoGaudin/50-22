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
        has('admin.settings'),
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
    </nav>
</template>
