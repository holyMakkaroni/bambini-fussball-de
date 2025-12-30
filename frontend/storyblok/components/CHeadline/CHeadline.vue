<script setup lang="ts">
defineProps({
  blok: {
    type: Object,
    default: null
  },
  container: {
    type: Boolean,
    default: false
  },
  anchor: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <div
    v-if="blok?.title"
    v-editable="blok"
    class="c-headline margin-components"
    :class="{ 'container': container }">
    <BaseHeadline
      :tag="blok?.headline_type"
      :primary="blok?.primary"
      :anchor="anchor"
      :title="blok?.title" />
  </div>
</template>
