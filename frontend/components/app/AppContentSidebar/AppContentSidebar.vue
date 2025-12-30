<script setup lang="ts">
const slots = useSlots()
</script>

<template>
  <div class="app-content-sidebar container big flex">
    <div class="hidden md:block flex-1 w-full min-w-[200px] max-w-[320px] pr-5">
      <BaseDivider
        v-if="!!slots.sidebar"
        variant="solid"
        class="border-secondary-light mb-3 md:mb-5" />
      <div class="sticky top-5">
        <slot name="sidebar" />
      </div>
    </div>

    <div class="w-full max-w-screen-xl [&>div:first-child]:mt-0 overflow-x-hidden">
      <slot />
    </div>
  </div>
</template>

<style scoped>

</style>
