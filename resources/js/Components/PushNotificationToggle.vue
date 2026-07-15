<script setup>
import { Button } from '@/Components/ui/button';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const page = usePage();

const supported = ref(false);
const permission = ref(Notification.permission ?? 'default');
const subscribed = ref(false);
const loading = ref(false);
const error = ref(null);

onMounted(async () => {
    supported.value =
        'serviceWorker' in navigator &&
        'PushManager' in window &&
        'Notification' in window;

    if (!supported.value) return;

    try {
        const registration = await navigator.serviceWorker.register('/push-sw.js');
        const existing = await registration.pushManager.getSubscription();
        subscribed.value = existing !== null;
    } catch {
        supported.value = false;
    }
});

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = atob(base64);
    return Uint8Array.from([...rawData].map((c) => c.charCodeAt(0)));
}

async function subscribe() {
    error.value = null;
    loading.value = true;

    try {
        const result = await Notification.requestPermission();
        permission.value = result;

        if (result !== 'granted') {
            loading.value = false;
            return;
        }

        const registration = await navigator.serviceWorker.ready;
        const vapidPublicKey = page.props.vapidPublicKey;

        const pushSubscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
        });

        const json = pushSubscription.toJSON();

        await axios.post(route('push-subscription.store'), {
            endpoint: json.endpoint,
            keys: json.keys,
        });

        subscribed.value = true;
    } catch (e) {
        error.value = 'Impossible d\'activer les notifications. Veuillez réessayer.';
        console.error(e);
    } finally {
        loading.value = false;
    }
}

async function unsubscribe() {
    error.value = null;
    loading.value = true;

    try {
        const registration = await navigator.serviceWorker.ready;
        const pushSubscription = await registration.pushManager.getSubscription();

        if (pushSubscription) {
            await axios.delete(route('push-subscription.destroy'), {
                data: { endpoint: pushSubscription.endpoint },
            });
            await pushSubscription.unsubscribe();
        }

        subscribed.value = false;
    } catch (e) {
        error.value = 'Impossible de désactiver les notifications. Veuillez réessayer.';
        console.error(e);
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-foreground">
                Notifications navigateur
            </h2>
            <p class="mt-1 text-sm text-muted-foreground">
                Recevez des notifications directement dans votre navigateur,
                même lorsque vous n'êtes pas sur le site.
            </p>
        </header>

        <div v-if="!supported" class="mt-6 text-sm text-muted-foreground">
            Votre navigateur ne prend pas en charge les notifications Web Push.
        </div>

        <div v-else class="mt-6 space-y-4">
            <div
                v-if="permission === 'denied'"
                class="rounded-md border border-destructive/50 bg-destructive/10 p-3 text-sm text-destructive"
            >
                Les notifications sont bloquées dans les paramètres de votre
                navigateur. Veuillez les autoriser manuellement.
            </div>

            <div
                v-if="error"
                class="rounded-md border border-destructive/50 bg-destructive/10 p-3 text-sm text-destructive"
            >
                {{ error }}
            </div>

            <div class="flex items-center gap-4">
                <Button
                    v-if="!subscribed"
                    :disabled="loading || permission === 'denied'"
                    @click="subscribe"
                >
                    {{ loading ? 'Activation…' : 'Activer les notifications' }}
                </Button>

                <Button
                    v-else
                    variant="outline"
                    :disabled="loading"
                    @click="unsubscribe"
                >
                    {{ loading ? 'Désactivation…' : 'Désactiver les notifications' }}
                </Button>

                <span
                    v-if="subscribed && !loading"
                    class="text-sm text-muted-foreground"
                >
                    Notifications activées sur cet appareil.
                </span>
            </div>
        </div>
    </section>
</template>
