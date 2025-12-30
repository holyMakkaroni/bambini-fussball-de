<script setup lang="ts">
import type { Schemas } from '#shopware'

const props = defineProps({
  address: {
    type: Object as PropType<Schemas['CustomerAddress'] | null>,
    required: true
  },
  canSetDefault: {
    type: Boolean,
    default: true
  },
  canEdit: {
    type: Boolean,
    default: true
  },
  canDelete: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['setDefault-address', 'edited-address', 'deleted-address'])

const { t } = useI18n()
const { defaultBillingAddressId, defaultShippingAddressId } = useUser()
const { setDefaultCustomerShippingAddress, setDefaultCustomerBillingAddress, deleteCustomerAddress, loadCustomerAddresses } = useAddress()
const { refreshSessionContext } = useSessionContext()

const company = computed(() => props.address?.company)
const firstName = computed(() => props.address?.firstName)
const lastName = computed(() => props.address?.lastName)
const street = computed(() => props.address?.street)
const zipcode = computed(() => props.address?.zipcode)
const city = computed(() => props.address?.city)
const country = computed(() => props.address?.country)

const canBeDeleted = computed(() => {
  return props.canDelete && defaultShippingAddressId.value !== props.address?.id && defaultBillingAddressId.value !== props.address?.id
})

const setDefaultShippingAddress = async () => {
  try {
    if (props.address) {
      await setDefaultCustomerShippingAddress(props.address.id)
      await refreshSessionContext()

      emit('setDefault-address')
    }
  } catch (error) {
    // eslint-disable-next-line no-console
    console.log(error)
  }
}

const setDefaultBillingAddress = async () => {
  try {
    if (props.address) {
      await setDefaultCustomerBillingAddress(props.address.id)
      await refreshSessionContext()

      emit('setDefault-address')
    }
  } catch (error) {
    // eslint-disable-next-line no-console
    console.log(error)
  }
}

const deleteAddress = async (addressId: string) => {
  try {
    await deleteCustomerAddress(addressId)
    emit('deleted-address')
  } catch (error) {
    // eslint-disable-next-line no-console
    console.log(error)
  } finally {
    await loadCustomerAddresses()
  }
}
</script>

<template>
  <div
    v-if="props.address"
    class="c-base-address-card">
    <ul class="leading-8">
      <li
        v-if="company"
        class="font-semibold line-clamp-1">
        {{ company }}
      </li>
      <li v-if="firstName || lastName">
        {{ firstName }} {{ lastName }}
      </li>
      <li v-if="street">
        {{ street }}
      </li>
      <li v-if="zipcode || city">
        {{ zipcode }} {{ city }}
      </li>
      <li v-if="country">
        {{ country.translated?.name }}
      </li>
    </ul>
    <div
      v-if="props.canSetDefault"
      class="mt-5">
      <ul class="flex flex-col space-y-3">
        <li
          class="flex text-xs cursor-pointer"
          @click="setDefaultBillingAddress()">
          <div class="flex items-center justify-center border border-secondary-light size-4">
            <BaseIcon
              v-if="props.address.id === defaultBillingAddressId"
              name="check"
              class="size-2" />
          </div>
          <div class="flex-1 ml-2">
            {{ t('pages.myAccount.addresses.defaultBillingAddressHeadline') }}
          </div>
        </li>
        <li
          class="flex text-xs cursor-pointer"
          @click="setDefaultShippingAddress()">
          <div class="flex items-center justify-center border border-secondary-light size-4">
            <BaseIcon
              v-if="props.address.id === defaultShippingAddressId"
              name="check"
              class="size-2" />
          </div>
          <div class="flex-1 ml-2">
            {{ t('pages.myAccount.addresses.defaultShippingAddressHeadline') }}
          </div>
        </li>
      </ul>
    </div>
    <div class="flex flex-col space-y-5 mt-10 mb-0 max-w-[220px]">
      <BaseButton
        v-if="props.canEdit"
        class="!w-full"
        outline
        @click="emit('edited-address')">
        {{ t('components.base.addressCard.editAddress') }}
      </BaseButton>
      <BaseButton
        v-if="canBeDeleted"
        class="!w-full"
        outline
        @click="deleteAddress(props.address.id)">
        {{ t('components.base.addressCard.deleteAddress') }}
      </BaseButton>
    </div>
  </div>
</template>

<style scoped>

</style>
