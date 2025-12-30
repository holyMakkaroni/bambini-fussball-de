<script setup lang="ts">
defineProps({
  width: {
    type: String,
    default: 'w-full'
  },
  height: {
    type: String,
    default: 'h-2'
  }
})
</script>

<template>
  <div
    class="c-base-skeleton"
    :class="[width, height]" />
</template>
