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

const { storyblokUrl } = useStoryblokHelper()
</script>

<template>
  <BaseSectionWrapper
    v-if="blok.teaser"
    v-editable="blok"
    identifier="c-portrait-teaser"
    :title="blok.title"
    draggable="false"
    :link="blok.link"
    :label="blok.label"
    :container="container">
    <BaseScrollbar
      class="flex"
      padding="normal"
      :items="2"
      :items-to-slide="2"
      :gap="20"
      :responsive="{
        640: {
          items: 3,
          itemsToSlide: 3,
        },
        991: {
          items: 4,
          itemsToSlide: 4,
        }
      }"
      arrows>
      <div
        v-for="teaser in blok.teaser"
        :key="teaser.uid"
        class="flex-shrink-0 relative transition-default group/teaser hover:box-shadow hover:scale-default">
        <NuxtLink
          v-if="storyblokUrl(blok.link)"
          :to="storyblokUrl(blok.link)"
          :title="teaser.headline"
          :target="blok?.link.target"
          class="absolute z-20 top-0 left-0 right-0 bottom-0" />
        <div class="absolute z-10 left-0 top-0 w-full h-full bg-[#24272C]/50 transition-default opacity-0 group-hover/teaser:opacity-100" />
        <div class="absolute bottom-6 left-5 right-5 z-10 text-white text-wrap line-clamp-2 text-2xl font-medium">
          {{ teaser.headline }}
        </div>
        <BaseImage
          :width="305"
          :height="485"
          :lazy="true"
          sizes="305"
          object-fit="cover"
          provider="storyblok"
          :image="teaser?.image" />
      </div>
    </BaseScrollbar>
  </BaseSectionWrapper>
</template>

<style scoped>

</style>
