<script setup lang="ts">
import type { Schemas } from '#shopware'

const props = defineProps({
  cartItem: {
    type: Object as PropType<Schemas['LineItem']>,
    required: true
  }
})

const { cartItem } = toRefs(props)
const cartType = computed(() => {
  switch (cartItem.value?.type) {
    case 'promotion':
      return defineAsyncComponent(() => import('./Type/BaseSidebarCartTypePromotionItem.vue'))
    default:
      return defineAsyncComponent(() => import('./Type/BaseSidebarCartTypeProductItem.vue'))
  }
})
</script>

<template>
  <component
    :is="cartType"
    :cart-item="cartItem" />
</template>
