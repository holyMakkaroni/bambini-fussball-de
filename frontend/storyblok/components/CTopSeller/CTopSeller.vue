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

const { apiClient } = useShopwareContext()
const { locale } = useI18n()

const { data: products } = await useAsyncData(
  `products-by-topseller-${props.blok?.uuid}-${locale.value}`,
  async () => {
    const { data } = await apiClient.invoke('readProduct post /product', {
      headers: {
        'sw-include-seo-urls': true
      },
      body: {
        limit: props.blok.limit,
        includes: {
          product: ['name', 'id', 'translated', 'cover', 'calculatedPrice', 'calculatedPrices', 'ratingAverage', 'seoUrls', 'parentId', 'sales', 'deliveryTime']
        },
        filter: [
          {
            type: 'equals',
            field: 'parentId',
            value: null
          }
        ],
        sort: [
          {
            field: 'sales',
            order: 'DESC',
            naturalSorting: true
          }
        ]
      }
    })

    return data.elements || []
  })
</script>

<template>
  <BaseSectionWrapper
    v-if="products?.length"
    v-editable="blok"
    identifier="c-base-top-seller"
    :title="blok.title"
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
        v-for="product in products"
        :key="product.id"
        class="transition-default flex-shrink-0 bg-secondary-light">
        <BaseProductBox
          :product="product"
          :lazy="true" />
      </div>
    </BaseScrollbar>
  </BaseSectionWrapper>
</template>
