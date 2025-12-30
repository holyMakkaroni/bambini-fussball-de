<script setup lang="ts">
import type { PropType } from 'vue'
import { getProductRoute } from '@shopware-pwa/helpers-next'
import type { Schemas } from '#shopware'
import BaseWishlist from '~/components/base/BaseWishlist/BaseWishlist.vue'

const props = defineProps({
  product: {
    type: Object as PropType<Schemas['Product']>,
    required: true
  },
  lazy: {
    type: Boolean,
    default: false
  },
  decoding: {
    type: String,
    default: 'async'
  }
})

const { t } = useI18n()

const title = computed(() => props.product?.translated?.name)
const ratingAverage = computed(() => props.product?.ratingAverage ?? 0)
const productImage = computed(() => {
  return {
    name: title.value,
    title: title.value,
    alt: title.value,
    filename: props.product.cover?.media.path
  }
})
</script>

<template>
  <div class="c-base-product-box-normal text-base transition-default hover:box-shadow hover:scale-default relative bg-secondary-light p-2.5 sm:p-5 flex flex-col h-full hover:text-secondary hover:no-underline hover:bg-white">
    <NuxtLink
      :to="getProductRoute(props.product)"
      :title="title"
      draggable="false"
      class="absolute w-full h-full z-10 left-0 top-0" />
    <BaseWishlist
      :product-id="product.id"
      class="border-white bg-white absolute top-5 right-5 z-20" />
    <div class="flex justify-center items-center">
      <BaseImage
        provider="gumlet"
        :width="248"
        :height="248"
        object-fit="contain"
        fit="contain"
        sizes="150px sm:175px"
        :lazy="props.lazy"
        :decoding="props.decoding"
        img-class="mix-blend-multiply"
        :image="productImage" />
    </div>
    <div class="flex justify-center items-center mt-1 mb-2 h-[30px]">
      Swatches
    </div>
    <div class="min-h-4 mb-2">
      <BaseReviewStars
        icon-class="w-3.5"
        :average="ratingAverage" />
    </div>
    <div
      v-if="props.product?.translated?.name"
      class="font-semibold leading-tight w-full h-10 mb-3 text-wrap line-clamp-2">
      {{ props.product.translated.name }}
    </div>
    <BasePrice
      :product="props.product"
      class="mb-3.5">
      <template #badge="{ percentDiscount }">
        <BasePill
          size="compact"
          color="red">
          -{{ percentDiscount }} %
        </BasePill>
        <BasePill
          size="compact"
          color="black">
          {{ t('components.base.productBox.campaignLabel') }}
        </BasePill>
      </template>
    </BasePrice>
    <div
      v-if="props.product?.deliveryTime"
      class="flex items-center text-xs mt-auto"
      :class="props.product.deliveryTime.max - props.product.deliveryTime.min < 3 ? 'text-success' : 'text-amber-500'">
      <BaseIcon
        name="truck"
        class="size-5" />
      <div class="pt-0.5 ml-2">
        <i18n-t
          v-if="props.product.deliveryTime.unit === 'day'"
          scope="global"
          tag="div"
          keypath="components.base.productBox.deliveryLabelDay">
          <template #deliveryTime>
            {{ props.product.deliveryTime.min }} - {{ props.product.deliveryTime.max }}
          </template>
        </i18n-t>
        <i18n-t
          v-if="props.product.deliveryTime.unit === 'week'"
          scope="global"
          tag="div"
          keypath="components.base.productBox.deliveryLabelWeek">
          <template #deliveryTime>
            {{ props.product.deliveryTime.min }} - {{ props.product.deliveryTime.max }}
          </template>
        </i18n-t>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
