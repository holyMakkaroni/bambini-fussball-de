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
    v-editable="blok"
    class="c-teaser margin-default"
    :class="{'container': container, 'full': container && blok.full_width }">
    <div
      v-if="blok.headline || blok.description"
      class="max-w-4xl mx-auto">
      <div class="flex flex-col justify-center text-center mb-6">
        <BaseHeadline
          v-if="blok.headline"
          tag="div"
          :title="blok.headline"
          custom-class="text-2xl md:text-3xl !mb-0" />
        <div
          v-if="blok.description"
          class="text-xl md:text-2xl">
          {{ blok.description }}
        </div>
      </div>
    </div>
    <div
      class="grid grid-cols-1 md:grid-cols-2"
      :class="{'gap-4': blok.gap}">
      <div
        v-for="teaser in blok.teaser"
        :key="teaser.uid"
        class="flex-1 relative">
        <StoryblokComponent :blok="teaser" />
      </div>
    </div>
  </div>
</template>
