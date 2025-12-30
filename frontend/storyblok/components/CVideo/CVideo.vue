<script setup lang="ts">
defineProps({
  blok: {
    type: Object,
    default: null
  },
  container: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <div
    v-if="blok.youtube_id"
    v-editable="blok"
    class="c-video margin-default"
    :class="{ 'container': container }">
    Youtube Video
  </div>
</template>
