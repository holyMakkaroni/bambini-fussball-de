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

const gridClass = ref<String>('')

switch (props.blok?.columnCount) {
  case '2':
    gridClass.value = 'grid gap-4 sm:grid-cols-2'
    break
  case '3':
    gridClass.value = 'grid gap-4 sm:grid-cols-2 md:grid-cols-3'
    break
  case '4':
    gridClass.value = 'grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4'
    break
  case '5':
    gridClass.value = 'grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5'
    break
  case '6':
    gridClass.value = 'grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6'
    break
  default:
    gridClass.value = ''
}
</script>

<template>
  <div
    v-editable="blok"
    class="c-grid margin-default"
    :class="{'container': container}">
    <div
      class="grid-wrapper"
      :class="[gridClass]">
      <StoryblokComponent
        v-for="column in blok?.columns"
        :key="column._uid"
        full-width
        :blok="column" />
    </div>
  </div>
</template>
