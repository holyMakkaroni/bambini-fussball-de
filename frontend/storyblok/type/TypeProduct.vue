<script setup lang="ts">
defineProps({
  blok: {
    type: Object,
    default: null
  }
})
</script>

<template>
  <div
    v-editable="blok"
    class="type-product">
    <component
      :is="body.component"
      v-for="body in blok?.body"
      :key="body._uid"
      container
      :blok="body" />
  </div>
</template>
