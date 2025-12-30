<script setup lang="ts">
import type { PropType } from 'vue'
import type { StoryblokAsset } from '~/types/storyblok.config'

defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: Object,
    default: null
  },
  image: {
    type: Object as PropType<StoryblokAsset>,
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
    class="c-wiki-head flex flex-col md:flex-row margin-default"
    :class="{ 'container': container }">
    <div class="flex-1">
      <BaseHeadline
        tag="h1"
        :title="title" />
      <BaseBorderedCard
        v-if="image?.filename"
        class="mb-7 md:mb-0 md:hidden w-full">
        <NuxtImg
          preload
          :width="150"
          :title="image.title"
          :alt="image.alt"
          :src="image.filename" />
      </BaseBorderedCard>
      <BaseText
        v-if="description"
        :text="description" />
    </div>
    <div
      v-if="image?.filename"
      class="flex items-center flex-1 px-0 md:px-24">
      <BaseBorderedCard class="hidden md:flex w-full">
        <NuxtImg
          :width="150"
          :title="image.title"
          :alt="image.alt"
          :src="image.filename" />
      </BaseBorderedCard>
    </div>
  </div>
</template>

<style scoped>

</style>
