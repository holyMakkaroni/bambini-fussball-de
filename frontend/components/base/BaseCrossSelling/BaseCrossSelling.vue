<script setup lang="ts">
import type { BaseCrossSelling } from '~/types/components/base'

defineProps<BaseCrossSelling>()
</script>

<template>
  <div
    v-if="element"
    class="c-base-cross-selling">
    <BaseHeadline
      tag="div"
      custom-class="text-xs border-t border-secondary-light mt-4 pt-4"
      :title="element.crossSelling.translated?.name ?? ''" />
    <BaseCarousel
      :options="{
        slidesToScroll: 1,
        align: 'start'
      }"
      navigation
      navigation-class="absolute w-full top-1/2 left-0"
      items-class="ml-[calc(1rem*-1)]">
      <BaseCarouselItem
        v-for="product in element.products"
        :key="product.id"
        item-class="flex-[0_0_100%] xs:flex-[0_0_50%] lg:flex-[0_0_33.33%] pl-[1rem] overflow-visible">
        <BaseProductBox
          :product="product"
          variant="small" />
      </BaseCarouselItem>
    </BaseCarousel>
  </div>
</template>

<style scoped>

</style>
