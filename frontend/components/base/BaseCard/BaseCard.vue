<script setup lang="ts">
defineProps({
  title: {
    type: String,
    default: null
  },
  description: {
    type: String,
    default: null
  }
})
</script>

<template>
  <div class="c-base-card bg-secondary-light p-5 flex flex-col">
    <div
      v-if="title"
      class="text-xl font-semibold">
      {{ title }}
    </div>
    <div
      v-if="description"
      class="leading-6 mt-2 mb-4 sm:mb-6">
      {{ description }}
    </div>
    <div class="mt-auto mb-0">
      <slot />
    </div>
  </div>
</template>

<style scoped>

</style>
