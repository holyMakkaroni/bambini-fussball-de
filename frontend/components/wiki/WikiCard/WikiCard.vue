<script setup lang="ts">
import type { PropType } from 'vue'
import type { ISbStoryData } from 'storyblok-js-client'
import type { Schemas } from '#shopware'

const props = defineProps({
  story: {
    type: Object as PropType<ISbStoryData>,
    default: null
  },
  showProducts: {
    type: Boolean,
    default: true
  },
  columnTitle: {
    type: String,
    default: null
  },
  columnText: {
    type: String,
    default: null
  }
})

const testProductIds = ['0195fc1d86577b40a5c6989369ea68ea', 'bf08f5bb783af849043e00000000000c']
const { apiClient } = useShopwareContext()
const { locale } = useI18n()
const products = ref<Schemas['Product'][]>([])

if (props.showProducts) {
  const { data } = await useAsyncData(
    `products-by-ids-${props.story?.uuid}-${locale.value}`,
    async () => {
      const { data } = await apiClient.invoke('readProduct post /product', {
        headers: {
          'sw-include-seo-urls': true
        },
        body: {
          ids: testProductIds,
          includes: {
            product: ['name', 'id', 'translated', 'cover', 'calculatedPrice', 'calculatedPrices', 'ratingAverage', 'seoUrls']
          }
        }
      })

      return data.elements || []
    },
    {
      // TODO: Remove if bug https://github.com/nuxt/nuxt/issues/18405 is fixed in Vue 3.5
      getCachedData (key, nuxt) {
        return nuxt.payload.data[key] || nuxt.static.data[key]
      }
    })

  products.value = data.value
}
</script>

<template>
  <div>
    <div class="c-wiki-card px-0">
      <div class="flex flex-col">
        <BaseHeadline
          tag="div"
          :custom-class="`h2-styling ${props.columnTitle}`"
          :title="story?.content.title" />
        <div class="flex flex-col md:flex-row">
          <div class="flex w-full md:max-w-[300px] lg:max-w-[415px] sm:mx-[5%] md:mx-[9%]">
            <BaseBorderedCard class="w-full">
              <BaseImage
                :width="200"
                :height="200"
                sizes="200"
                :lazy="true"
                :image="story?.content.image" />
            </BaseBorderedCard>
          </div>
          <div
            class="flex-1"
            :class="props.columnText">
            <div class="leading-[30px]">
              {{ story?.content.description }}
            </div>
            <a
              v-if="story?.content.datasheet?.filename"
              target="_blank"
              :href="story?.content.datasheet.filename"
              :alt="story?.content.datasheet.meta_data.alt"
              :title="story?.content.datasheet.meta_data.title"
              class="inline-flex mt-6">
              <BaseIcon
                name="download"
                class="w-6 h-6" />
              <div class="flex-1 pl-3 pt-1">{{ story?.content.datasheet.title }}</div>
            </a>
          </div>
        </div>
        <div v-if="props.showProducts && products.length">
          <div class="mt-12">
            <BaseHeadline
              v-if="story?.content.products_headline"
              tag="div"
              custom-class="h3-styling !mb-6"
              :title="story?.content.products_headline" />
            <BaseCarousel
              :options="{
                slidesToScroll: 1,
                align: 'start'
              }"
              navigation
              navigation-class="absolute w-full top-1/2 left-0"
              items-class="ml-[calc(1rem*-1)]">
              <BaseCarouselItem
                v-for="product in products"
                :key="product.id"
                item-class="flex-[0_0_100%] xs:flex-[0_0_50%] md:flex-[0_0_33.33%] lg:flex-[0_0_25%] pl-[1rem]">
                <BaseProductBox :product="product" />
              </BaseCarouselItem>
            </BaseCarousel>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
