<script setup lang="ts">
import type { PropType } from 'vue'
import type { StoryblokLink } from '~/types/storyblok.config'

defineProps({
  title: {
    type: String,
    default: null
  },
  link: {
    type: Object as PropType<StoryblokLink>,
    default: null
  },
  label: {
    type: String,
    default: null
  },
  identifier: {
    type: String,
    required: true
  },
  contentClass: {
    type: String,
    default: null
  },
  container: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <div class="c-base-section-wrapper margin-default">
    <div :class="[{'container': container}, identifier]">
      <div
        v-if="title"
        class="flex flex-col md:flex-row">
        <BaseSectionHeadline
          :headline="title"
          :link="link"
          :link-label="label" />
      </div>
      <div :class="[contentClass]">
        <slot />
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
