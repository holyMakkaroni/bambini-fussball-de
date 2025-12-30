<script setup lang="ts">
import type { PropType } from 'vue'
import type { Schemas } from '#shopware'

defineProps({
  products: {
    type: Array as PropType<Schemas['Product'][]|null>,
    required: true
  }
})

const { t } = useI18n()
</script>

<template>
  <div
    v-if="products?.length"
    class="c-base-related-products mt-8 md:mt-12">
    <div class="container">
      <BaseHeadline
        tag="h3"
        custom-class="h3-styling"
        :title="t('components.base.relatedProducts.headline')" />
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
          <BaseProductBox
            :product="product"
            :lazy="true" />
        </BaseCarouselItem>
      </BaseCarousel>
    </div>
  </div>
</template>

<style scoped>

</style>
