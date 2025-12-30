<script lang="ts" setup>
defineProps({
  headline: {
    type: String,
    required: true
  },
  showDivider: {
    type: Boolean,
    default: true
  }
})
</script>

<template>
  <div class="c-app-search-group">
    <div class="text-sm font-light mb-1">
      {{ headline }}
    </div>
    <ul>
      <slot />
    </ul>
    <hr
      v-if="showDivider"
      class="my-6">
  </div>
</template>

<style scoped>

</style>
