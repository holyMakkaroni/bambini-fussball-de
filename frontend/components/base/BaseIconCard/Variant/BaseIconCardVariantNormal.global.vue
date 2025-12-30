<script setup lang="ts">

import type { PropType } from 'vue'
import type { StoryblokLink } from '~/types/storyblok.config'

defineProps({
  url: {
    type: Object as PropType<StoryblokLink>,
    default: null
  },
  icon: {
    type: String,
    required: true
  },
  headline: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: null
  }
})

const { storyblokUrl } = useStoryblokHelper()
</script>

<template>
  <div class="c-base-icon-card-normal relative bg-white flex flex-col items-center p-6 md:p-8 transition-default hover:box-shadow hover:scale-default">
    <NuxtLink
      v-if="url.id"
      class="absolute top-0 left-0 w-full h-full z-10"
      :to="storyblokUrl(url)"
      draggable="false"
      :title="headline" />
    <div class="mb-7 size-20">
      <BaseIcon
        v-if="icon"
        :name="icon"
        class="w-full text-primary" />
    </div>
    <div class="flex flex-col mt-auto mb-0">
      <div
        v-if="headline"
        class="text-xl text-primary font-semibold text-center">
        {{ headline }}
      </div>
      <div
        v-if="description"
        class="text-center">
        {{ description }}
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
