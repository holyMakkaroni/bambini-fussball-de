<script setup lang="ts">
defineProps({
  padding: {
    type: String,
    default: 'px-12 py-8'
  }
})
</script>

<template>
  <div
    class="border border-secondary-light flex flex-col justify-center items-center transition-default"
    :class="[padding]">
    <slot />
  </div>
</template>

<style scoped>

</style>
