<script setup lang="ts">
const { t } = useI18n()
const { cartItems, totalPrice, isEmpty, count } = useCart()
const { sessionContext } = useSessionContext()
const { getCheckoutUrl } = useShopwareHelper()

const items = computed(() => [...cartItems.value].reverse())

const handleCheckout = () => {
  const checkoutUrl = getCheckoutUrl(sessionContext.value)

  if (!checkoutUrl) {
    return
  }

  location.href = checkoutUrl
}
</script>

<template>
  <div class="c-base-sidebar-cart flex flex-col h-full">
    <AppSidebarPartialsHeader
      icon="cart"
      :icon-badge="count"
      :show-badge="!isEmpty"
      :headline="!isEmpty ? t('components.base.sidebarCart.headline', { count }) : t('components.base.sidebarCart.headlineEmpty')" />
    <BaseSidebarCartWrapper
      :total-price="totalPrice"
      :is-empty="isEmpty"
      :action-button-label="t('components.base.sidebarCart.checkout')"
      @action-button="handleCheckout">
      <template #default>
        <ul
          v-if="!isEmpty"
          class="flex flex-col divide-y divide-solid divide-secondary-light">
          <li
            v-for="item in items"
            :key="item.id"
            class="flex py-5 px-10">
            <BaseSidebarCartItem :cart-item="item" />
          </li>
        </ul>
        <BaseIcon
          v-else
          name="cart"
          class="size-64 text-secondary-light" />
      </template>
    </BaseSidebarCartWrapper>
  </div>
</template>

<style scoped>

</style>
