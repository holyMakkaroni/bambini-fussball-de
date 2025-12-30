<script setup lang="ts">
import type { BlogHero } from '~/types/components/blog'

defineProps<BlogHero>()
</script>

<template>
  <div class="c-blog-hero relative overflow-hidden">
    <BaseImage
      v-if="image?.filename"
      :lazy="true"
      :width="1920"
      :height="1400"
      sizes="400px sm:520px md:768px lg:1920px"
      object-fit="cover"
      provider="storyblok"
      img-class="max-h-[576px]"
      :image="image" />
    <div class="absolute top-1/2 left-1/2 z-10 -translate-x-1/2 -translate-y-1/2 w-full px-4 xl:px-0 xl:max-w-screen-xl ">
      <BaseHeadline
        v-if="additional_title"
        tag="div"
        custom-class="text-white font-light text-3xl xl:text-7xl"
        :title="additional_title" />
      <BaseHeadline
        v-if="title"
        :tag="headline_type || 'h1'"
        custom-class="text-white xl:text-7xl"
        :title="title" />
    </div>
  </div>
</template>

<style scoped>

</style>
