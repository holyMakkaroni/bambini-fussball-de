<script setup lang="ts">
const props = defineProps({
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
  priceClass: {
    type: String,
    default: 'text-lg md:text-xl leading-5 md:leading-5'
  }
})

const { getFormattedPrice } = usePrice()

const formatedPrice = computed(() => getFormattedPrice(props.price))
const formatedListPrice = computed(() => getFormattedPrice(props.listPrice))
const percentDiscount = computed(() => props.percentage.toFixed(0))
const isSale = computed(() => props.percentage > 0)
</script>

<template>
  <div
    v-if="price"
    class="c-base-price">
    <div
      v-if="!isSale"
      class="font-bold"
      :class="[priceClass]">
      {{ formatedPrice }}
    </div>
    <div
      v-if="isSale"
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
