<script setup lang="ts">
import type { PropType } from 'vue'
import { pascalCase } from 'scule'
import type { Schemas } from '#shopware'

const props = defineProps({
  product: {
    type: Object as PropType<Schemas['Product']>,
    required: true
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
  return resolveComponent(pascalCase(`BaseProductBoxVariant_${type}`))
}
</script>

<template>
  <component
    :is="loadComponent(props.variant)"
    :lazy="props.lazy"
    :decoding="props.decoding"
    :product="props.product" />
</template>

<style scoped>

</style>
