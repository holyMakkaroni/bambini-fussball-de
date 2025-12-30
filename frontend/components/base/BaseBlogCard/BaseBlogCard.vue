<script setup lang="ts">
import { useUtilities } from '~/composables/useUtilities'
import type { BaseBlogCard } from '~/types/components/base'

defineProps<BaseBlogCard>()

const { formatDate } = useUtilities()
</script>

<template>
  <NuxtLink
    :to="url"
    :title="title"
    draggable="false"
    class="c-base-blog-card transition-default hover:box-shadow hover:scale-default relative bg-secondary-light flex flex-col h-full hover:bg-white hover:text-secondary hover:no-underline">
    <BaseImage
      v-if="image?.filename"
      :width="420"
      :height="236"
      :lazy="true"
      sizes="420"
      object-fit="cover"
      provider="storyblok"
      :image="image" />
    <div class="px-5 py-4 flex flex-col mt-auto mb-0">
      <div class="font-light text-xl md:text-[28px] h-[64px] line-clamp-2 text-ellipsis leading-7 md:leading-8 mb-4 text-wrap">
        {{ title }}
      </div>
      <div class="mt-auto mb-0 font-light text-secondary/50 leading-3 flex divide-x divide-secondary/50">
        <div class="pr-2">
          {{ formatDate(createdDate) }}
        </div>
        <div
          v-if="author"
          class="pl-2">
          {{ author.name }}
        </div>
      </div>
    </div>
  </NuxtLink>
</template>

<style scoped>

</style>
