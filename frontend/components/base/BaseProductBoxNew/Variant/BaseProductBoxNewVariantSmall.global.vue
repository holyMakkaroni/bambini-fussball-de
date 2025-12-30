<script setup lang="ts">
import type { PropType } from 'vue'
import type { Schemas } from '#shopware'

const props = defineProps({
  product: {
    type: Object as PropType<Schemas['Product']>,
    required: true
  }
})

const title = computed(() => props.product?.translated?.name)
const productImage = computed(() => {
  return {
    name: title.value,
    title: title.value,
    alt: title.value,
    filename: props.product.cover?.media.path
  }
})

const { addToCart } = useAddToCart(ref(props.product))
</script>

<template>
  <div class="c-base-product-box-small relative bg-secondary-light p-5 flex flex-col h-full">
    <div class="flex justify-center items-center mb-2">
      <BaseImage
        v-if="productImage.filename"
        provider="gumlet"
        :width="170"
        :height="170"
        object-fit="contain"
        fit="contain"
        sizes="125px sm:170px"
        :lazy="true"
        img-class="mix-blend-multiply"
        :image="productImage" />
    </div>
    <div
      v-if="props.product?.translated?.name"
      class="text-xs font-semibold leading-tight w-full h-8 my-2 line-clamp-2">
      {{ props.product.translated.name }}
    </div>
    <BasePrice
      :product="props.product"
      class="mb-4"
      price-class="text-sm">
      <template #badge="{ percentDiscount }">
        <BasePill
          size="compact"
          color="red">
          {{ percentDiscount }} %
        </BasePill>
      </template>
    </BasePrice>
    <BaseDivider
      variant="solid"
      class="border-white mt-auto mb-4" />
    <div class="flex justify-center mt-auto mb-0">
      <BaseButton
        variant="none"
        size="small"
        icon="cart"
        icon-class="w-4 h-4 !mr-3"
        :title="$t('components.shopware.general.labels.cart')"
        @click="addToCart()">
        {{ $t('components.shopware.general.labels.cart') }}
      </BaseButton>
    </div>
  </div>
</template>

<style scoped>

</style>
