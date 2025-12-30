<script setup lang="ts">
import { useConfigStore } from '~/stores/config'
import type { StoryblokDefaultConfig } from '~/types/storyblok.config'

const { storyblokUrl } = useStoryblokHelper()
const configStore = useConfigStore()
const configDefault: StoryblokDefaultConfig = configStore.defaultConfig

const topNavigationItems = computed(() => {
  return configDefault?.content?.topnavigation
})
</script>

<template>
  <ul
    role="list"
    class="flex gap-10 text-sm lg:text-base lg:gap-16 text-nowrap bg-white">
    <li
      v-for="item in topNavigationItems"
      :key="item._uid"
      v-editable="item"
      role="listitem">
      <NuxtLink
        :to="storyblokUrl(item.link)"
        :target="item.link.target">
        {{ item.title }}
      </NuxtLink>
    </li>
  </ul>
</template>

<style scoped>

</style>
