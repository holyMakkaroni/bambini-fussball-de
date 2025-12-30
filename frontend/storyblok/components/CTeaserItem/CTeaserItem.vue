<script setup lang="ts">
defineProps({
  blok: {
    type: Object,
    default: null
  }
})

const { t } = useI18n()
const { storyblokUrl } = useStoryblokHelper()
</script>

<template>
  <div class="c-teaser-item group">
    <NuxtLink
      v-if="storyblokUrl(blok.link)"
      :to="storyblokUrl(blok.link)"
      :title="blok.headline"
      draggable="false"
      :target="blok?.link.target"
      class="absolute z-20 top-0 left-0 right-0 bottom-0" />
    <div class="absolute z-10 left-0 top-0 w-full h-full bg-[#24272C]/50 transition-default opacity-0 group-hover:opacity-100" />
    <div class="absolute bottom-7 left-7 right-7 xs:bottom-14 xs:left-14 xs:right-14 z-10 text-white">
      <div class="text-xl md:text-2xl lg:text-3xl font-semibold line-clamp-1 text-ellipsis">
        {{ blok.headline }}
      </div>
      <div
        v-if="blok.description"
        class="text-lg line-clamp-2 text-wrap md:text-xl lg:text-2xl">
        {{ blok.description }}
      </div>
      <div class="c-base-button normal bg-none border-2 border-white group-hover:border-primary group-hover:bg-primary mt-6">
        {{ blok?.label ? blok?.label : t('components.app.general.labels.discoverNow') }}
      </div>
    </div>
    <BaseImage
      :width="1920"
      :height="1080"
      :lazy="true"
      sizes="350px sm:640px md:1024px"
      object-fit="cover"
      provider="storyblok"
      :image="blok?.image" />
  </div>
</template>
