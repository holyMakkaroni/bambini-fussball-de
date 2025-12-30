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
  <div class="c-base-account-sub-header flex flex-col">
    <BaseHeadline
      tag="div"
      :title="title"
      custom-class="text-2xl font-semibold" />
    <BaseDivider
      variant="solid"
      class="my-4 sm:my-8" />
    <div
      v-if="description"
      class="nl2br leading-8 mb-8">
      {{ description }}
    </div>
  </div>
</template>

<style scoped>

</style>
