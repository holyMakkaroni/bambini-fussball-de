<script setup lang="ts">
import { getProductRoute } from '@shopware-pwa/helpers-next'
import type { Schemas } from '#shopware'
import type { StoryblokShopwareIntegrationItem } from '~/types/storyblok.config'

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

const { apiClient } = useShopwareContext()
const { locale, t } = useI18n()
const { stripHtml } = useUtilities()

const products = ref<Schemas['Product'][]>([])
const productCriteria = ref<Schemas['Criteria']>({
  includes: {
    product: ['name', 'description', 'id', 'translated', 'cover', 'calculatedPrice', 'calculatedPrices', 'ratingAverage', 'seoUrls', 'parentId', 'sortedProperties', 'productReviews']
  },
  associations: {
    properties: {
      associations: {
        group: {}
      }
    },
    productReviews: {},
    media: {}
  }
})

const productObject = computed(() => props.blok.products.items?.filter((item: { type: string, id: string }) => item.type === 'product') || null)

const productIds = computed(() => {
  return productObject.value?.map((product: StoryblokShopwareIntegrationItem) => {
    return product.id
  })
})

if (productIds.value?.length) {
  const { data } = await useAsyncData(
    `single-product-${props.blok?.uuid}-${productIds.value?.join('-')}-${locale.value}`,
    async () => {
      const { data } = await apiClient.invoke('readProduct post /product', {
        headers: {
          'sw-include-seo-urls': true
        },
        body: {
          ids: productIds.value,
          ...productCriteria.value
        }
      })

      return data.elements || []
    },
    {
      getCachedData (key, nuxt) {
        return nuxt.payload.data[key] || nuxt.static.data[key]
      }
    }
  )

  if (data.value) {
    products.value = data.value
  }
}
</script>

<template>
  <div
    v-if="products?.length"
    v-editable="blok"
    class="c-single-product margin-components space-y-10"
    :class="{'container': container}">
    <div
      v-for="product in products"
      :key="product.id">
      <div class="bg-secondary-light p-5 md:p-8">
        <div class="flex flex-col md:flex-row gap-5">
          <div class="w-full flex-1 max-w-[420px] flex justify-center items-center">
            <BaseImage
              v-if="product?.cover?.media.path"
              provider="gumlet"
              :width="420"
              :height="420"
              object-fit="contain"
              fit="contain"
              sizes="150px sm:175px"
              lazy
              img-class="mix-blend-multiply"
              :image="{
                name: product?.translated?.name,
                title: product?.translated?.name,
                alt: product?.translated?.name,
                filename: product?.cover?.media.path
              }" />
          </div>
          <div class="flex flex-col justify-center flex-1 space-y-6">
            <div class="flex flex-col">
              <div class="inline-flex">
                <BaseReviewStars
                  :average="product?.ratingAverage ?? 0"
                  suffix-class="text-xs pt-0.5 ml-1.5"
                  icon-class="w-4">
                  {{ t('components.app.topbar.review.label', {
                    reviewCount: product?.productReviews?.length ?? 0
                  }) }}
                </BaseReviewStars>
              </div>
              <BaseHeadline
                v-if="product?.translated?.name"
                tag="div"
                custom-class="!font-medium text-xl"
                :title="product?.translated?.name" />
            </div>
            <div
              v-if="product?.translated?.description"
              class="leading-8 line-clamp-3">
              {{ stripHtml(product?.translated?.description) }}
            </div>
            <div v-if="product?.sortedProperties?.length">
              <ul class="flex flex-col flex-wrap md:flex-row">
                <li
                  v-for="property in product?.sortedProperties"
                  :key="property.id"
                  class="flex mr-5 mb-1">
                  <span class="font-semibold mr-1">{{ property.translated.name }}:</span>
                  <div
                    v-if="property.options.length"
                    class="flex space-x-1">
                    <span
                      v-for="(option) in property.options"
                      :key="option.id"
                      class="after:content-[',_'] last:after:content-['']">
                      {{ option.translated.name }}
                    </span>
                  </div>
                </li>
              </ul>
            </div>
            <BasePrice :product="product" />
            <BaseButton
              :href="getProductRoute(product).path"
              :title="product?.translated?.name"
              outline>
              Zum Produkt
            </BaseButton>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
