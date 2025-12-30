<script setup lang="ts">
import { syncRefs } from '@vueuse/core'
import type { Schemas } from '#shopware'
import BaseSidebarProductItem from '~/components/base/BaseSidebarProductItem/BaseSidebarProductItem.vue'

const props = defineProps({
  cartItem: {
    type: Object as PropType<Schemas['LineItem']>,
    required: true
  }
})

const { cartItem } = toRefs(props)

const { t } = useI18n()
const { addNotification } = useNotification()
const { refreshCart } = useCart()
const {
  itemOptions,
  removeItem,
  itemTotalPrice,
  itemQuantity,
  isRemovable,
  changeItemQuantity
} = useCartItem(cartItem)

const quantity = ref()
syncRefs(itemQuantity, quantity)

const updateQuantity = async (quantityInput: number | undefined) => {
  if (quantityInput === itemQuantity.value) { return }

  try {
    const response = await changeItemQuantity(Number(quantityInput))
    await refreshCart(response)
  } catch (error) {
    console.log(error)
  }
}

const removeCartItem = async () => {
  await removeItem()
  addNotification(t('notifications.title.success'), t('notifications.cart.remove.success', {
    productName: cartItem.value.label
  }), 'success')
}

watch(quantity, () => updateQuantity(quantity.value))
</script>

<template>
  <BaseSidebarProductItem
    v-model="quantity"
    :product-id="cartItem.id"
    show-quantity-input
    :item-total-price="itemTotalPrice"
    :label="cartItem.label"
    :image-path="cartItem.cover?.path"
    :item-options="itemOptions"
    :delivery-time-unit="cartItem.deliveryInformation.deliveryTime?.unit"
    :delivery-time-min="cartItem.deliveryInformation.deliveryTime?.min"
    :delivery-time-max="cartItem.deliveryInformation.deliveryTime?.max"
    :is-removable="isRemovable"
    @remove-item="removeCartItem" />
</template>

<style scoped>

</style>
