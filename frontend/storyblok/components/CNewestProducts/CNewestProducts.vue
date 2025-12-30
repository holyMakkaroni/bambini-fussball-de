<script setup lang="ts">
import type { StoryblokShopwareIntegrationItem } from '~/types/storyblok.config'
import type { Schemas } from '#shopware'

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
const { locale } = useI18n()

const productCriteria = ref<Schemas['Criteria']>({
  limit: props.blok.limit,
  includes: {
    product: ['name', 'id', 'translated', 'cover', 'calculatedPrice', 'calculatedPrices', 'ratingAverage', 'seoUrls', 'parentId']
  },
  filter: [
    {
      type: 'equals',
      field: 'parentId',
      value: null
    }
  ]
})

const productIds = computed(() => props.blok.products.items
  .filter((item: StoryblokShopwareIntegrationItem) => item.type === 'product')
  .map((item: StoryblokShopwareIntegrationItem) => item.id))

const productStreamIds = computed(() => props.blok.products.items
  .filter((item: StoryblokShopwareIntegrationItem) => item.type === 'productStream')
  .map((item: StoryblokShopwareIntegrationItem) => item.id))

const { data: products } = await useLazyAsyncData(
  `newest-products-${props.blok?.uuid}-${[...productIds.value, ...productStreamIds.value].join('-')}-${locale.value}`,
  async () => {
    if (productIds.value.length || productStreamIds.value.length) {
      productCriteria.value.filter = [
        {
          type: 'multi',
          operator: 'or',
          queries: [
            productIds.value.length
              ? {
                  type: 'equalsAny',
                  field: 'id',
                  value: productIds.value
                }
              : null,
            productStreamIds.value.length
              ? {
                  type: 'equalsAny',
                  field: 'streamIds',
                  value: productStreamIds.value
                }
              : null
          ].filter(Boolean)
        }
      ]
    }

    const { data } = await apiClient.invoke('readProduct post /product', {
      headers: {
        'sw-include-seo-urls': true
      },
      body: {
        ...productCriteria.value
      }
    })

    return data.elements || []
  })
</script>

<template>
  <BaseSectionWrapper
    v-if="products?.length"
    v-editable="blok"
    identifier="c-base-newest-products"
    :title="blok.title"
    :link="blok.link"
    :label="blok.label"
    :container="container"
    :content-class="container ? 'escape-container' : ''">
    <BaseScrollbar
      padding="normal"
      full-width>
      <div class="flex gap-4 xl:gap-5">
        <div
          v-for="product in products"
          :key="product.id"
          class="w-full max-w-[305px] flex-shrink-0 bg-secondary-light">
          <BaseProductBox
            :product="product"
            :lazy="true" />
        </div>
      </div>
    </BaseScrollbar>
  </BaseSectionWrapper>
</template>
