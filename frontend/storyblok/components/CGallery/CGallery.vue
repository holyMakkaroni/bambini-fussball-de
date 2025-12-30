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

const gridClass = ref<String>('grid-cols-1 sm:grid-cols-2')

switch (props.blok.columns) {
  case '3':
    gridClass.value = 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3'
    break
  case '4':
    gridClass.value = 'grid-cols-1 sm:grid-cols-2 md:grid-cols-4'
    break
}
</script>

<template>
  <div
    v-if="blok.images"
    v-editable="blok"
    class="c-gallery margin-components"
    :class="{'container': container}">
    <div
      class="grid gap-5"
      :class="gridClass">
      <BaseImage
        v-for="image in blok?.images"
        :key="image.id"
        :width="650"
        sizes="320px sm:640px"
        :lazy="true"
        provider="storyblok"
        :image="image"
        object-fit="cover"
        :show-caption="true" />
    </div>
  </div>
</template>
