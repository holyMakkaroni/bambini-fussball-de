<script setup lang="ts">
import type { PropType } from 'vue'
import type { StoryblokLink } from '~/types/storyblok.config'

const props = defineProps({
  headline: {
    type: String,
    default: null
  },
  link: {
    type: Object as PropType<StoryblokLink>,
    default: null
  },
  linkLabel: {
    type: String,
    default: null
  }
})

const { t } = useI18n()
const { storyblokUrl, removeTrailingSlash } = useStoryblokHelper()

const link = computed(() => {
  if (!props.link) {
    return false
  }

  return storyblokUrl(props.link)
})
</script>

<template>
  <div class="w-full flex items-center mb-6 md:mb-8">
    <div
      v-if="props.headline"
      class="font-semibold text-xl">
      {{ props.headline }}
    </div>
    <div
      v-if="link"
      class="flex items-center text-nowrap ml-5">
      <BaseIcon
        name="chevron-double"
        class="size-1.5" />
      <NuxtLink
        class="flex-1 text-xs underline decoration-black ml-1"
        :to="removeTrailingSlash(link)"
        :title="linkLabel ?? t('components.base.sectionHeadline.viewAll')"
        :target="props.link.target">
        {{ linkLabel ?? t('components.base.sectionHeadline.viewAll') }}
      </NuxtLink>
    </div>
  </div>
</template>

<style scoped>

</style>
