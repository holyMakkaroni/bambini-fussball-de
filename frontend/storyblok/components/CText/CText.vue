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
  fullWidth: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <div
    v-editable="blok"
    class="c-text margin-components"
    :class="{ 'container': container }">
    <BaseText
      :class="{'md:max-w-[70%]': !fullWidth}"
      :text="blok?.text" />
  </div>
</template>
