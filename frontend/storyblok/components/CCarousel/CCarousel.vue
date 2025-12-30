<script setup lang="ts">
const props = defineProps({
  blok: {
    type: Object,
    default: null
  },
  container: {
    type: Boolean,
    default: false
  }
})
const { t } = useI18n()
const { storyblokUrl } = useStoryblokHelper()

</script>

<template>
  <div
    v-editable="blok"
    class="c-carousel margin-default"
    :class="{'container': container}">
    <BaseCarousel
      pagination
      pagination-class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex gap-x-2">
      <BaseCarouselItem
        v-for="slide in blok?.slides"
        :key="slide._uid"
        v-editable="slide">
        <div class="pb-8 flex flex-col md:flex-row gap-x-4 xl:gap-x-5">
          <div class="flex-1 relative">
            <div
              v-if="slide?.background_image.filename"
              class="pr-10 xl:pr-0">
              <BaseLogomarkClip :id="`clip-${slide._uid}`" />
              <BaseImage
                :width="500"
                :height="400"
                :lazy="true"
                sizes="500"
                provider="storyblok"
                :image="slide?.background_image"
                :clip-path="slide._uid" />
            </div>
            <BaseImage
              v-if="slide?.image.filename"
              :class="{'absolute z-10 -bottom-8 right-0 pl-10 xl:pl-0': slide?.background_image.filename}"
              :width="slide?.background_image.filename ? 470 : 600"
              :height="slide?.background_image.filename ? 370 : 400"
              :lazy="true"
              object-fit="contain"
              sizes="200px sm:350px"
              provider="storyblok"
              :img-class="slide?.background_image.filename ? 'ml-auto mr-0' : ''"
              :image="slide?.image" />
          </div>
          <div
            class="flex-1 flex flex-col justify-center"
            :class="{'mt-8 md:mt-0': slide?.background_image.filename}">
            <BaseHeadline
              :title="slide?.headline"
              custom-class="text-2xl md:text-3xl xl:text-4xl md:mb-0 xl:mb-0"
              tag="div" />
            <div
              v-if="slide?.description"
              class="text-xl md:text-2xl mb-6 nl2br">
              {{ slide?.description }}
            </div>
            <BaseButton
              v-if="storyblokUrl(slide.link)"
              outline
              size="big"
              :href="storyblokUrl(slide.link)"
              :title="slide.label ? slide.label : t('components.app.general.labels.discoverNow')">
              {{ slide.label ? slide.label : t('components.app.general.labels.discoverNow') }}
            </BaseButton>
          </div>
        </div>
      </BaseCarouselItem>
    </BaseCarousel>
  </div>
</template>
