<script setup lang="ts">
import type { PropType } from 'vue'
import type { ShopwareAsset } from '~/types/shopware'

defineProps({
  images: {
    type: Array as PropType<ShopwareAsset[]>,
    required: true
  },
  showThumbnails: {
    type: Boolean,
    default: false
  }
})

const currentSlide = ref<number>(0)
const showGallery = ref<boolean>(false)

const openGallery = (initIndex: number = 0) => {
  showGallery.value = true
  currentSlide.value = initIndex
}

const closeGallery = () => {
  showGallery.value = false
}

const onSelect = (index: number) => {
  currentSlide.value = index
}
</script>

<template>
  <div
    v-if="images"
    class="c-base-image-gallery">
    <BaseCarousel
      thumbnails
      @select="onSelect">
      <template #default>
        <BaseCarouselItem
          v-for="(image, index) in images"
          :key="image.id"
          class="cursor-pointer"
          @click="openGallery(index)">
          <BaseImage
            v-if="image.filename"
            provider="gumlet"
            :width="585"
            :height="585"
            object-fit="contain"
            fit="contain"
            sizes="585"
            :lazy="index !== 0"
            :decoding="index === 0 ? 'sync' : 'async'"
            :fetch-priority="index === 0 ? 'high' : 'low'"
            :image="image" />
        </BaseCarouselItem>
      </template>
      <template
        v-if="showThumbnails"
        #thumbnails="{ scrollTo }">
        <div class="mt-5 grid grid-cols-[repeat(auto-fill,_minmax(90px,_1fr))] gap-4">
          <BaseBorderedCard
            v-for="(image, index) in images.slice(0,5)"
            :key="image.id"
            class="cursor-pointer min-w-[90px] !min-h-0 !p-1 transition-default hover:border-primary"
            :class="{ '!border-primary': currentSlide === index }"
            @click="scrollTo(index)">
            <BaseImage
              v-if="image.filename"
              provider="gumlet"
              :width="80"
              :height="80"
              object-fit="contain"
              fit="contain"
              sizes="80"
              :lazy="false"
              decoding="sync"
              fetch-priority="high"
              :image="image" />
          </BaseBorderedCard>
          <BaseBorderedCard
            v-if="images.length > 5"
            class="cursor-pointer h-full min-w-[90px] !min-h-0 !p-1 text-center leading-4 transition-default hover:border-primary hover:bg-primary hover:text-white"
            @click="openGallery(5)">
            +{{ images.length - 5 }}<br> weitere
          </BaseBorderedCard>
        </div>
      </template>
    </BaseCarousel>

    <Teleport to="#teleports">
      <Transition name="fade">
        <div>
          <BaseOverlay
            v-if="showGallery"
            :show-loading-indicator="false" />
          <div
            v-if="showGallery"
            class="bg-white fixed flex flex-col top-0 left-0 w-full h-full z-[80]">
            <div class="flex justify-end items-center p-4 z-[70]">
              <button
                type="button"
                class="transition-default hover:text-primary">
                <BaseIcon
                  name="close"
                  class="size-8"
                  @click="closeGallery" />
              </button>
            </div>

            <div class="absolute w-full h-full flex flex-col justify-center items-center z-[60] p-4">
              <div class="w-full h-full flex justify-center items-center">
                <BaseCarousel
                  :init-slide-index="currentSlide"
                  thumbnails
                  navigation
                  navigation-class="absolute top-[47%] left-8 right-8"
                  @select="onSelect">
                  <template #default>
                    <BaseCarouselItem
                      v-for="(image, index) in images"
                      :key="image.id"
                      class="flex justify-center items-center">
                      <BaseImage
                        v-if="image.filename"
                        class="h-full [&>figure]:flex [&>figure]:items-center"
                        provider="gumlet"
                        :width="1024"
                        :height="1024"
                        object-fit="contain"
                        fit="contain"
                        sizes="1024"
                        :lazy="index !== 0"
                        :decoding="index === 0 ? 'sync' : 'async'"
                        :fetch-priority="index === 0 ? 'high' : 'low'"
                        :image="image"
                        img-class="max-h-full" />
                    </BaseCarouselItem>
                  </template>
                  <template
                    v-if="showThumbnails"
                    #thumbnails="{ scrollTo }">
                    <div class="max-w-[100vw]">
                      <div class="mt-5 flex lg:justify-center gap-4 overflow-x-scroll">
                        <BaseBorderedCard
                          v-for="(image, index) in images"
                          :key="image.id"
                          class="cursor-pointer min-w-[90px] !min-h-0 !p-1 transition-default hover:border-primary"
                          :class="{ '!border-primary': currentSlide === index }"
                          @click="scrollTo(index)">
                          <BaseImage
                            v-if="image.filename"
                            provider="gumlet"
                            :width="80"
                            :height="80"
                            object-fit="contain"
                            fit="contain"
                            sizes="80"
                            :lazy="false"
                            decoding="sync"
                            fetch-priority="high"
                            :image="image" />
                        </BaseBorderedCard>
                      </div>
                    </div>
                  </template>
                </BaseCarousel>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<style scoped>

</style>
