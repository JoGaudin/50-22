<script setup lang="ts">
interface ParamValue {
    id: string;
    name: string;
    pivot: { description: string };
}

withDefaults(
    defineProps<{
        fiche: {
            name: string;
            creator?: { name: string } | null;
            param_descriptions: ParamValue[];
        };
        showHeader?: boolean;
    }>(),
    { showHeader: true },
);
</script>

<template>
    <div class="space-y-4">
        <div v-if="showHeader">
            <h3 class="text-lg font-semibold">{{ fiche.name }}</h3>
            <p v-if="fiche.creator" class="text-xs text-muted-foreground">Par {{ fiche.creator.name }}</p>
        </div>

        <dl class="space-y-3">
            <div v-for="param in fiche.param_descriptions" :key="param.id" class="rounded-md border border-border p-3">
                <dt class="text-sm font-medium text-primary">{{ param.name }}</dt>
                <dd class="mt-1 whitespace-pre-wrap text-sm text-muted-foreground">
                    {{ param.pivot.description || '—' }}
                </dd>
            </div>
        </dl>
    </div>
</template>
