<script setup lang="ts">
const props = defineProps({
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
    v-if="blok.image.filename"
    v-editable="blok"
    class="c-image mt-12"
    :class="{'container': container}">
    <div :class="{'max-w-[50%]': props.blok?.half_width, 'md:max-w-[75%]': !props.blok?.half_width}">
      <BaseImage
        :lazy="true"
        provider="storyblok"
        :image="blok?.image"
        object-fit="cover"
        :show-caption="true" />
    </div>
  </div>
</template>
