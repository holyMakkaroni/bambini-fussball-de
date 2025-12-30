<script setup lang="ts">
import { pascalCase } from 'scule'

defineProps({
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
  variant: {
    type: String,
    default: 'normal',
    validation: (value: string) => {
      return ['small', 'normal'].includes(value)
    }
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

const loadComponent = (type: String) => {
  return resolveComponent(pascalCase(`BaseProductBoxNewVariant_${type}`))
}
</script>

<template>
  <component
    :is="loadComponent(variant)"
    :product-id="productId"
    :lazy="lazy"
    :decoding="decoding"
    :title
    :rating-average
    :url
    :price
    :list-price
    :percentage
    :regulation-price
    :delivery-time
    :image-path />
</template>

<style scoped>

</style>
