<script setup lang="ts">
import type { TocStoryblok } from '~/types/storyblok'

defineProps({
  blok: {
    type: Object as PropType<TocStoryblok>,
    default: null
  },
  container: {
    type: Boolean,
    default: false
  }
})

const { t } = useI18n()
</script>

<template>
  <div
    class="c-toc margin-components"
    :class="{ 'container': container }">
    <BaseDivider variant="solid" />

    <div class="flex flex-col my-15">
      <BaseHeadline
        tag="div"
        :title="t('storyblok.components.toc.headline')"
        custom-class="text-xl font-semibold mb-7" />
      <ul class="max-w-[650px] space-y-2.5">
        <li
          v-for="(toc, index) in blok.data"
          :key="index">
          <NuxtLink
            class="flex items-center underline decoration-black"
            :to="toc.anchor">
            <BaseIcon
              name="chevron-double"
              class="size-1.5 group-hover/toc:text-primary" />
            <span class="ml-1.5">
              {{ toc.name }}
            </span>
          </NuxtLink>
        </li>
      </ul>
    </div>

    <BaseDivider variant="solid" />
  </div>
</template>

<style scoped>

</style>
