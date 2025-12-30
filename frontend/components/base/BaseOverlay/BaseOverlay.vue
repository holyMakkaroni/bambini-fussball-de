<script setup lang="ts">
import type { BaseOverlay } from '~/types/components/base'

withDefaults(defineProps<BaseOverlay>(), {
  showLoadingIndicator: true
})

onMounted(() => {
  document.body.style.overflow = 'hidden'
})

onUnmounted(() => {
  document.body.style.overflow = ''
})
</script>

<template>
  <div class="c-base-overlay fixed w-screen h-screen top-0 left-0 bg-secondary/25 z-50 flex justify-center items-center">
    <div
      v-if="showLoadingIndicator"
      class="flex flex-col items-center">
      <BaseLogomark class="size-12 text-primary" />
      <svg
        class="animate-spin size-4 text-base mt-2"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24">
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4" />
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
      </svg>
    </div>
  </div>
</template>

<style scoped>

</style>
