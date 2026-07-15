<script setup lang="ts">
import Alert from '@/Components/Alert.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const success = computed(() => (page.props.flash as Record<string, string | undefined>)?.success);
const error = computed(() => (page.props.flash as Record<string, string | undefined>)?.error);
</script>

<template>
    <div class="space-y-2">
        <Alert v-if="success" variant="success" dismissible>{{ success }}</Alert>
        <Alert v-if="error" variant="destructive" dismissible>{{ error }}</Alert>
    </div>
</template>
