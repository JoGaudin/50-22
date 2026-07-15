<script lang="ts" setup>
import type { ToasterProps } from "vue-sonner"
import { CircleCheckIcon, InfoIcon, Loader2Icon, OctagonXIcon, TriangleAlertIcon, XIcon } from "lucide-vue-next"
import { Toaster as Sonner } from "vue-sonner"
import { computed } from "vue"
import { useTheme } from "@/composables/useTheme"
import { cn } from "@/lib/utils"

const { isDark } = useTheme()

const props = defineProps<ToasterProps>()

const resolvedProps = computed(() => ({
  ...props,
  theme: props.theme ?? (isDark.value ? 'dark' : 'light'),
}))
</script>

<template>
  <Sonner
    :class="cn('toaster group', props.class)"
    :style="{
      '--normal-bg': 'var(--popover)',
      '--normal-text': 'var(--popover-foreground)',
      '--normal-border': 'var(--border)',
      '--border-radius': 'var(--radius)',
      '--success-bg': '#16a34a',
      '--success-text': '#ffffff',
      '--success-border': '#15803d',
      '--error-bg': '#dc2626',
      '--error-text': '#ffffff',
      '--error-border': '#b91c1c',
      '--z-index': '9999',
    }"
    v-bind="resolvedProps"
  >
    <template #success-icon>
      <CircleCheckIcon class="size-4" />
    </template>
    <template #info-icon>
      <InfoIcon class="size-4" />
    </template>
    <template #warning-icon>
      <TriangleAlertIcon class="size-4" />
    </template>
    <template #error-icon>
      <OctagonXIcon class="size-4" />
    </template>
    <template #loading-icon>
      <div>
        <Loader2Icon class="size-4 animate-spin" />
      </div>
    </template>
    <template #close-icon>
      <XIcon class="size-4" />
    </template>
  </Sonner>
</template>
