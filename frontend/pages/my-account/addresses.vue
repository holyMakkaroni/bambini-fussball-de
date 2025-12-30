<script setup lang="ts">
import type { Schemas } from '#shopware'

const { t } = useI18n()
const { user, userDefaultBillingAddress, userDefaultShippingAddress, refreshUser } = useUser()
const { apiClient } = useShopwareContext()
const modal = ref(null)
const selectedAddress = ref<Schemas['CustomerAddress'] | null>(null)

const { data: customerAddresses, refresh } = await useAsyncData(
  `load-customer-addresses-${user.value?.id}`, async () => {
    const { data } = await apiClient.invoke('listAddress post /account/list-address')
    return data.elements || []
  }, {
    // TODO: Remove if bug https://github.com/nuxt/nuxt/issues/18405 is fixed in Vue 3.5
    getCachedData (key, nuxt) {
      return nuxt.payload.data[key] || nuxt.static.data[key]
    }
  }
)

const openModal = (address: Schemas['CustomerAddress'] | null) => {
  selectedAddress.value = address
  modal.value?.open()
}

const closeModal = () => {
  selectedAddress.value = null
}

const onSuccess = async () => {
  await refreshUser({
    associations: {
      defaultBillingAddress: {},
      defaultShippingAddress: {}
    }
  })
  await refresh()
  modal.value?.close()
}
</script>

<template>
  <div class="type-my-account-addresses">
    <BaseAccountHeader
      :title="t('pages.myAccount.addresses.headline')"
      :description="t('pages.myAccount.addresses.description')" />

    <div class="grid grid-cols-1 sm:grid-cols-2">
      <div>
        <BaseAccountSubHeader :title="t('pages.myAccount.addresses.defaultBillingAddressHeadline')" />
        <BaseAddressCard
          :address="userDefaultBillingAddress"
          :can-set-default="false"
          :can-edit="false" />
      </div>
      <div>
        <BaseAccountSubHeader :title="t('pages.myAccount.addresses.defaultShippingAddressHeadline')" />
        <BaseAddressCard
          :address="userDefaultShippingAddress"
          :can-set-default="false"
          :can-edit="false" />
      </div>
    </div>

    <div class="flex flex-col">
      <BaseAccountSubHeader :title="t('pages.myAccount.addresses.addressesHeadline')" />
      <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-5">
        <BaseAddressCard
          v-for="address in customerAddresses"
          :key="address.id"
          :address="address"
          can-delete
          @edited-address="openModal(address)"
          @deleted-address="refresh" />
      </div>
    </div>

    <div class="flex flex-col">
      <BaseAccountSubHeader
        :title="t('pages.myAccount.addresses.createAnotherAddressHeadline')"
        :description="t('pages.myAccount.addresses.createAnotherAddressDescription')" />
      <BaseButton
        outline
        @click="openModal(null)">
        {{ t('pages.myAccount.addresses.addNewAddress') }}
      </BaseButton>
    </div>

    <BaseModal
      ref="modal"
      @close-modal="closeModal">
      <BaseAddressForm
        :address="selectedAddress"
        @success="onSuccess" />
    </BaseModal>
  </div>
</template>

<style scoped>

</style>
