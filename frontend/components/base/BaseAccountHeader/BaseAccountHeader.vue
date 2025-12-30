<script setup lang="ts">
defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: null
  }
})
</script>

<template>
  <div class="c-base-account-header flex flex-col">
    <BaseHeadline
      tag="h1"
      :title="title" />
    <div
      v-if="description"
      class="nl2br leading-8">
      {{ description }}
    </div>
  </div>
</template>

<style scoped>

</style>
