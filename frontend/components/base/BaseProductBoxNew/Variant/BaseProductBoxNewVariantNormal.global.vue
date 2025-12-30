<script setup lang="ts">

const props = defineProps({
  productId: {
    type: String,
    required: true
  },
  title: {
    type: String,
    default: null
  },
  ratingAverage: {
    type: Number,
    default: 0
  },
  url: {
    type: String,
    default: null
  },
  imagePath: {
    type: String,
    default: null
  },
  price: {
    type: Number,
    required: true
  },
  listPrice: {
    type: Number,
    default: null
  },
  percentage: {
    type: Number,
    default: null
  },
  regulationPrice: {
    type: Number,
    default: null
  },
  deliveryTime: {
    type: Object,
    default: null
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

const productImage = computed(() => {
  return {
    name: props.title,
    title: props.title,
    alt: props.title,
    filename: props.imagePath
  }
})
</script>

<template>
  <div class="c-base-product-box-normal text-base transition-default hover:box-shadow hover:scale-default relative bg-secondary-light p-2.5 sm:p-5 flex flex-col h-full hover:text-secondary hover:no-underline hover:bg-white">
    <NuxtLink
      :to="url"
      :title="title"
      draggable="false"
      class="absolute w-full h-full z-10 left-0 top-0" />
    <BaseWishlist
      :product-id="productId"
      class="border-white bg-white absolute top-5 right-5 z-20" />
    <div class="flex justify-center items-center">
      <BaseImage
        provider="gumlet"
        :width="248"
        :height="248"
        object-fit="contain"
        fit="contain"
        sizes="150px sm:248px"
        :lazy="lazy"
        :decoding="decoding"
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
      v-if="title"
      class="font-semibold leading-tight w-full h-10 mb-3 text-wrap line-clamp-2">
      {{ title }}
    </div>
    <BasePriceNew
      :price
      :list-price
      :percentage
      :regulation-price
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
    </BasePriceNew>
    <div
      v-if="deliveryTime"
      class="flex items-center text-xs mt-auto"
      :class="deliveryTime.max - deliveryTime.min < 3 ? 'text-success' : 'text-amber-500'">
      <BaseIcon
        name="truck"
        class="size-5" />
      <div class="pt-0.5 ml-2">
        <i18n-t
          v-if="deliveryTime.unit === 'day'"
          scope="global"
          tag="div"
          keypath="components.base.productBox.deliveryLabelDay">
          <template #deliveryTime>
            {{ deliveryTime.min }} - {{ deliveryTime.max }}
          </template>
        </i18n-t>
        <i18n-t
          v-if="deliveryTime.unit === 'week'"
          scope="global"
          tag="div"
          keypath="components.base.productBox.deliveryLabelWeek">
          <template #deliveryTime>
            {{ deliveryTime.min }} - {{ deliveryTime.max }}
          </template>
        </i18n-t>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
