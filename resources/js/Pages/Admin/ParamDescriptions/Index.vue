<script setup lang="ts">
import AdminSubnav from '@/Components/AdminSubnav.vue';
import ConfirmActionDialog from '@/Components/ConfirmActionDialog.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ParamDescriptionFormDrawer from '@/Components/ParamDescriptionFormDrawer.vue';
import { Button } from '@/Components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { useInertiaForm } from '@/composables/useInertiaForm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

defineProps<{ paramDescriptions: Array<{ id: string; name: string; order: number }> }>();

const { form, drawerOpen, editingId, openCreate, openEdit, submit } = useInertiaForm({
    name: '',
    order: 0,
});

function submitCreate() {
    submit(route('admin.param-descriptions.store'), 'post');
}

function submitEdit() {
    if (!editingId.value) return;
    submit(route('admin.param-descriptions.update', editingId.value), 'put');
}

function destroyParamDescription(paramDescription: { id: string }) {
    router.delete(route('admin.param-descriptions.destroy', paramDescription.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Administration — Paramètres de fiche" />
    <AuthenticatedLayout>
        <template #header>
            <AdminSubnav />
        </template>

        <div class="space-y-6 p-6">
            <FlashMessages />

            <PageHeader title="Paramètres de fiche" description="Gérez les paramètres utilisables dans les fiches.">
                <template #actions>
                    <Button @click="openCreate">Nouveau paramètre</Button>
                </template>
            </PageHeader>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Nom</TableHead>
                        <TableHead>Ordre</TableHead>
                        <TableHead class="w-24">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="paramDescription in paramDescriptions" :key="paramDescription.id">
                        <TableCell>{{ paramDescription.name }}</TableCell>
                        <TableCell>{{ paramDescription.order }}</TableCell>
                        <TableCell class="flex gap-2">
                            <Button variant="outline" size="sm" @click="openEdit(paramDescription.id, paramDescription)">
                                Éditer
                            </Button>
                            <ConfirmActionDialog
                                title="Confirmer la suppression"
                                :description="`Supprimer le paramètre « ${paramDescription.name} » ?`"
                                @confirm="destroyParamDescription(paramDescription)"
                            >
                                <template #trigger>
                                    <Button variant="destructive" size="sm">Supprimer</Button>
                                </template>
                            </ConfirmActionDialog>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <ParamDescriptionFormDrawer
            v-model:open="drawerOpen"
            :editing="!!editingId"
            :form="form"
            @submit="editingId ? submitEdit() : submitCreate()"
        />
    </AuthenticatedLayout>
</template>
