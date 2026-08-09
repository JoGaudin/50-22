<script setup>
import AdminSubnav from '@/Components/AdminSubnav.vue';
import AddUserDrawer from '@/Components/AddUserDrawer.vue';
import ConfirmActionDialog from '@/Components/ConfirmActionDialog.vue';
import DataTable from '@/Components/DataTable.vue';
import GlobalSearch from '@/Components/GlobalSearch.vue';
import PageHeader from '@/Components/PageHeader.vue';
import UserDataDrawer from '@/Components/UserDataDrawer.vue';
import { Button } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref, watch } from 'vue';

const props = defineProps({
    usersData: {
        type: Object,
        required: true,
    },
    allRoles: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);
const currentUserId = computed(() => Number(page.props.auth?.user?.id ?? 0));

const inviteForm = useForm({
    name: '',
    email: '',
});

const users = ref([]);
const roleSelections = reactive({});
const tableMeta = reactive({
    current_page: 1,
    last_page: 1,
    per_page: 20,
    total: 0,
    has_more_pages: false,
});
const tableSort = reactive({
    by: 'name',
    direction: 'asc',
});
const tableFilters = reactive({
    limit: 20,
    search: '',
});
const loading = ref(false);
const loadingMore = ref(false);
const loadMoreBlocked = ref(false);
const inviteDrawerOpen = ref(false);
const userDrawerOpen = ref(false);
const selectedUserId = ref(null);

const userColumns = [
    { key: 'name', label: 'Nom', sortable: true },
    { key: 'email', label: 'E-mail', sortable: true },
    { key: 'created_at', label: 'Créé le', sortable: true },
    { key: 'roles', label: 'Rôles' },
    { key: 'password_set', label: 'Mot de passe' },
    {
        key: 'actions',
        label: 'Actions',
        headerClass: 'text-end min-w-[12rem]',
        cellClass: 'text-end',
    },
];

const hasMoreUsers = computed(
    () => tableMeta.current_page < tableMeta.last_page,
);
const selectedUser = computed(() =>
    users.value.find((u) => u.id === selectedUserId.value) ?? null,
);
const selectedUserRoleIds = computed(() =>
    selectedUser.value ? (roleSelections[selectedUser.value.id] ?? []) : [],
);

function resetRoleSelections() {
    Object.keys(roleSelections).forEach((key) => {
        delete roleSelections[key];
    });
}

function syncRoleSelectionsFromUsers(usersList, { replace = false } = {}) {
    if (replace) {
        resetRoleSelections();
    }

    usersList.forEach((u) => {
        roleSelections[u.id] = u.role_ids.map((id) => Number(id));
    });
}

function applyUsersData(payload, { append = false } = {}) {
    const incomingRows = payload?.data ?? [];

    users.value = append ? [...users.value, ...incomingRows] : incomingRows;
    syncRoleSelectionsFromUsers(incomingRows, { replace: !append });

    Object.assign(tableMeta, payload?.meta ?? {});
    Object.assign(tableSort, {
        by: payload?.sort?.by ?? tableSort.by,
        direction: payload?.sort?.direction ?? tableSort.direction,
    });
    Object.assign(tableFilters, {
        limit: payload?.filters?.limit ?? tableFilters.limit,
        search: payload?.filters?.search ?? tableFilters.search,
    });
}

function normalizeUsersPayload(rawPayload) {
    if (rawPayload?.data && rawPayload?.meta) {
        return rawPayload;
    }

    if (rawPayload?.props?.usersData?.data && rawPayload?.props?.usersData?.meta) {
        return rawPayload.props.usersData;
    }

    return null;
}

watch(
    () => props.usersData,
    (usersData) => applyUsersData(usersData, { append: false }),
    { immediate: true },
);

function submitInviteForm() {
    inviteForm.post(route('admin.users.store'), {
        preserveScroll: true,
        onSuccess: () => {
            inviteForm.reset();
            inviteDrawerOpen.value = false;
        },
    });
}

function openUserDrawer(user) {
    selectedUserId.value = user.id;

    if (!roleSelections[user.id]) {
        roleSelections[user.id] = [...user.role_ids];
    }

    userDrawerOpen.value = true;
}

function sendInvitation(user) {
    router.post(route('admin.users.invite', user.id), {}, { preserveScroll: true });
}

function deleteUser(user) {
    if (!canDeleteUser(user)) {
        return;
    }

    router.delete(route('admin.users.destroy', user.id), {
        preserveScroll: true,
        onSuccess: () => {
            if (selectedUserId.value === user.id) {
                selectedUserId.value = null;
                userDrawerOpen.value = false;
            }

            delete roleSelections[user.id];
            loadMoreBlocked.value = false;
            fetchUsersPage(1);
        },
    });
}

function canDeleteUser(user) {
    return Number(user.id) !== currentUserId.value;
}

function handleSelectedUserRoleIdsChange(roleIds) {
    if (!selectedUser.value) {
        return;
    }

    roleSelections[selectedUser.value.id] = roleIds.map((id) => Number(id));
}

function saveSelectedUserRoles() {
    if (!selectedUser.value) {
        return;
    }

    saveUserRoles(selectedUser.value.id);
}

function syncSearchParamInUrl() {
    if (typeof window === 'undefined') {
        return;
    }

    const url = new URL(window.location.href);
    const normalizedSearch = tableFilters.search.trim();

    if (normalizedSearch.length > 0) {
        url.searchParams.set('search', normalizedSearch);
    } else {
        url.searchParams.delete('search');
    }

    url.searchParams.delete('page');
    window.history.replaceState(window.history.state, '', `${url.pathname}${url.search}${url.hash}`);
}

async function fetchUsersPage(pageNumber, { append = false } = {}) {
    if (append && (loading.value || loadingMore.value)) {
        return;
    }

    if (!append && loading.value) {
        return;
    }

    if (append) {
        loadingMore.value = true;
    } else {
        loading.value = true;
    }

    try {
        if (!append) {
            syncSearchParamInUrl();
        }

        const { data } = await axios.get(route('admin.users.index'), {
            params: {
                page: pageNumber,
                limit: tableFilters.limit,
                search: tableFilters.search || undefined,
                sort_by: tableSort.by,
                sort_dir: tableSort.direction,
            },
            headers: {
                Accept: 'application/json',
            },
        });

        const normalizedPayload = normalizeUsersPayload(data);
        if (normalizedPayload === null) {
            throw new Error('Unexpected users payload shape.');
        }

        applyUsersData(normalizedPayload, { append });
        loadMoreBlocked.value = false;
    } catch (error) {
        // Keep the current dataset untouched if the request fails.
        if (append) {
            loadMoreBlocked.value = true;
        }
        console.error(error);
    } finally {
        loading.value = false;
        loadingMore.value = false;
    }
}

function onSortChange({ sortBy, sortDirection }) {
    tableSort.by = sortBy;
    tableSort.direction = sortDirection;
    loadMoreBlocked.value = false;
    fetchUsersPage(1);
}

function onSearchChange(value) {
    tableFilters.search = value;
    loadMoreBlocked.value = false;
    fetchUsersPage(1);
}

function loadMoreUsers() {
    if (!hasMoreUsers.value || loadMoreBlocked.value || loading.value || loadingMore.value) {
        return;
    }

    fetchUsersPage(tableMeta.current_page + 1, { append: true });
}

function mapRoleIdsToRoleObjects(roleIds) {
    const selected = new Set(roleIds);

    return props.allRoles
        .filter((role) => selected.has(role.id))
        .map((role) => ({
            id: role.id,
            name: role.name,
            slug: role.slug,
        }));
}

function applySavedRolesToUser(userId, roleIds) {
    const normalizedRoleIds = [...roleIds].sort((a, b) => a - b);
    const normalizedRoles = mapRoleIdsToRoleObjects(normalizedRoleIds);

    users.value = users.value.map((user) => {
        if (user.id !== userId) {
            return user;
        }

        return {
            ...user,
            role_ids: normalizedRoleIds,
            roles: normalizedRoles,
        };
    });

    roleSelections[userId] = [...normalizedRoleIds];
}

function saveUserRoles(userId) {
    if (!roleSelections[userId]) {
        return;
    }

    const nextRoleIds = [...roleSelections[userId]];

    router.patch(
        route('admin.users.roles.sync', userId),
        { role_ids: nextRoleIds },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                applySavedRolesToUser(userId, nextRoleIds);
            },
        },
    );
}

function rolesChanged(user) {
    const a = [...(roleSelections[user.id] ?? [])].sort((x, y) => x - y);
    const b = [...user.role_ids].sort((x, y) => x - y);
    if (a.length !== b.length) {
        return true;
    }
    return a.some((id, i) => id !== b[i]);
}

const createdAtFormatter = new Intl.DateTimeFormat('fr-FR', {
    dateStyle: 'medium',
    timeStyle: 'short',
});

function formatCreatedAt(value) {
    if (!value) {
        return '—';
    }

    return createdAtFormatter.format(new Date(value));
}
</script>

<template>
    <Head title="Administration — Utilisateurs" />

    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-4">
                <AdminSubnav />
                <PageHeader title="Gestion des utilisateurs" :separator="false" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <div
                    v-if="flashSuccess"
                    class="rounded-md border border-primary/30 bg-primary/5 px-4 py-3 text-sm text-foreground"
                >
                    {{ flashSuccess }}
                </div>
                <div
                    v-if="flashError"
                    class="rounded-md border border-destructive/30 bg-destructive/5 px-4 py-3 text-sm text-destructive"
                >
                    {{ flashError }}
                </div>

                <AddUserDrawer
                    v-model:open="inviteDrawerOpen"
                    :form="inviteForm"
                    @submit="submitInviteForm"
                />

                <UserDataDrawer
                    v-model:open="userDrawerOpen"
                    :user="selectedUser"
                    :all-roles="allRoles"
                    :selected-role-ids="selectedUserRoleIds"
                    :has-role-changes="selectedUser ? rolesChanged(selectedUser) : false"
                    :format-created-at="formatCreatedAt"
                    @set-role-ids="handleSelectedUserRoleIdsChange"
                    @save-roles="saveSelectedUserRoles"
                    @send-invitation="sendInvitation"
                />

                <section
                    class="overflow-hidden bg-card shadow-sm sm:rounded-lg border border-border"
                >
                    <div class="border-b border-border px-6 py-4">
                        <div
                            class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                        >
                            <div>
                                <h3 class="text-lg font-medium text-card-foreground">
                                    Utilisateurs
                                </h3>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    Tri et pagination chargés depuis le serveur.
                                </p>
                            </div>
                            <Button
                                type="button"
                                @click="inviteDrawerOpen = true"
                            >
                                Inviter un utilisateur
                            </Button>
                        </div>
                    </div>
                    <div class="px-6 pt-4">
                        <GlobalSearch
                            :model-value="tableFilters.search"
                            placeholder="Rechercher par nom ou e-mail..."
                            @update:model-value="onSearchChange"
                        />
                    </div>
                    <div class="overflow-x-auto p-6">
                        <DataTable
                            :columns="userColumns"
                            :rows="users"
                            :sort-by="tableSort.by"
                            :sort-direction="tableSort.direction"
                            :loading="loading"
                            :loading-more="loadingMore"
                            :has-more="hasMoreUsers"
                            empty-label="Aucun utilisateur"
                            @sort-change="onSortChange"
                            @load-more="loadMoreUsers"
                            @row-click="openUserDrawer"
                        >
                            <template #cell-name="{ row }">
                                <span class="font-medium">{{ row.name }}</span>
                            </template>

                            <template #cell-created_at="{ row }">
                                <span class="text-sm text-muted-foreground">
                                    {{ formatCreatedAt(row.created_at) }}
                                </span>
                            </template>

                            <template #cell-roles="{ row }">
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="r in row.roles"
                                        :key="r.id"
                                        class="rounded-full bg-primary/10 px-2 py-0.5 text-xs text-primary"
                                    >{{ r.name }}</span>
                                    <span
                                        v-if="!row.roles.length"
                                        class="text-sm text-muted-foreground"
                                    >Aucun</span>
                                </div>
                            </template>

                            <template #cell-password_set="{ row }">
                                <span
                                    v-if="row.password_set"
                                    class="text-sm text-muted-foreground"
                                >Défini</span>
                                <span
                                    v-else
                                    class="text-sm text-amber-700 dark:text-amber-400"
                                >En attente</span>
                            </template>

                            <template #cell-actions="{ row }">
                                <div class="flex items-center justify-end gap-2">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        :disabled="row.password_set"
                                        data-stop-row-click
                                        @click="sendInvitation(row)"
                                    >
                                        Envoyer l’invitation
                                    </Button>
                                    <ConfirmActionDialog
                                        :disabled="!canDeleteUser(row)"
                                        title="Confirmer la suppression"
                                        :description="`L'utilisateur ${row.name} sera supprimé logiquement (champ deleted_at).`"
                                        confirm-label="Supprimer"
                                        cancel-label="Annuler"
                                        @confirm="deleteUser(row)"
                                    >
                                        <template #trigger>
                                            <Button
                                                type="button"
                                                variant="destructive"
                                                size="sm"
                                                :disabled="!canDeleteUser(row)"
                                                data-stop-row-click
                                            >
                                                Supprimer
                                            </Button>
                                        </template>
                                    </ConfirmActionDialog>
                                </div>
                            </template>
                        </DataTable>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
