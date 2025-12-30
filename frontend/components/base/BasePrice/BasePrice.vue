<script setup lang="ts">
import type { PropType } from 'vue'
import type { Schemas } from '#shopware'

const props = defineProps({
  product: {
    type: Object as PropType<Schemas['Product']>,
    required: true
  },
  priceClass: {
    type: String,
    default: 'text-lg md:text-xl leading-5 md:leading-5'
  }
})

const { getFormattedPrice } = usePrice()
const { isListPrice, price } = useProductPrice(ref(props.product))

const formatedPrice = computed(() => getFormattedPrice(price.value?.unitPrice))
const formatedListPrice = computed(() => getFormattedPrice(price.value?.listPrice?.price))
const percentDiscount = computed(() => price.value?.listPrice?.percentage?.toFixed(0))
</script>

<template>
  <div
    v-if="price"
    class="c-base-price">
    <div
      v-if="!isListPrice"
      class="font-bold"
      :class="[priceClass]">
      {{ formatedPrice }}
    </div>
    <div
      v-if="isListPrice"
      class="flex flex-col">
      <div class="absolute -top-[4px] left-5">
        <div class="flex gap-0.5">
          <slot
            name="badge"
            :percent-discount="percentDiscount" />
        </div>
      </div>
      <div class="flex items-center flex-wrap gap-x-2">
        <span
          class="text-primary font-bold"
          :class="[priceClass]">{{ formatedPrice }} *</span>
        <span class="flex items-center text-sm line-through leading-5">{{ formatedListPrice }}</span>
      </div>
      <i18n-t
        scope="global"
        keypath="components.base.price.reductionsLabel"
        tag="div"
        class="text-xxs leading-tight opacity-50 text-wrap">
        <template #formatedPrice>
          {{ formatedPrice }}
        </template>
        <template #percentDiscount>
          {{ percentDiscount }}
        </template>
      </i18n-t>
    </div>
  </div>
</template>

<style scoped>

</style>
