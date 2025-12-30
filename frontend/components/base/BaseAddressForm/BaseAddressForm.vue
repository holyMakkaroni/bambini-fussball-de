<script setup lang="ts">
import type { Schemas } from '#shopware'

const props = defineProps({
  address: {
    type: Object as PropType<Schemas['CustomerAddress'] | null>,
    default: null
  }
})

const { createCustomerAddress, updateCustomerAddress } = useAddress()
const { getCountries } = useCountries()
const { getSalutations } = useSalutations()
const { t } = useI18n()
const emit = defineEmits(['success'])

const formData = reactive({
  company: props.address?.company ?? '',
  countryId: props.address?.countryId ?? '',
  countryStateId: props.address?.countryStateId ?? '',
  salutationId: props.address?.salutationId ?? '',
  firstName: props.address?.firstName ?? '',
  lastName: props.address?.lastName ?? '',
  zipcode: props.address?.zipcode ?? '',
  city: props.address?.city ?? '',
  street: props.address?.street ?? '',
  id: props.address?.id ?? ''
})

const isNewAddress = computed(() => !formData.id)

const invokeSave = async (): Promise<void> => {
  try {
    isNewAddress.value ? await createCustomerAddress(formData) : await updateCustomerAddress(formData)
    emit('success')
  } catch (errors) {
    // eslint-disable-next-line no-console
    console.log(errors)
  }
}
</script>

<template>
  <form
    class="c-base-address-form space-y-8"
    @submit.prevent="invokeSave">
    <FormSelect
      v-model="formData.salutationId"
      name="salutationId"
      class="max-w-[220px]"
      :options="getSalutations.map((salutation) => {
        return {
          label: salutation.displayName ?? '',
          value: salutation.id
        }
      })"
      :label="t('components.app.general.labels.salutation')" />
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <FormInput
        id="firstName"
        v-model="formData.firstName"
        required
        type="text"
        name="firstName"
        :placeholder="t('components.app.general.labels.firstName')"
        :label="t('components.app.general.labels.firstName')" />
      <FormInput
        id="lastName"
        v-model="formData.lastName"
        required
        type="text"
        name="lastName"
        :placeholder="t('components.app.general.labels.lastName')"
        :label="t('components.app.general.labels.lastName')" />
      <FormInput
        id="company"
        v-model="formData.company"
        type="text"
        name="company"
        :placeholder="t('components.app.general.labels.company')"
        :label="t('components.app.general.labels.company')" />
      <FormInput
        id="street"
        v-model="formData.street"
        type="text"
        name="street"
        required
        :placeholder="t('components.app.general.labels.street')"
        :label="t('components.app.general.labels.street')" />
      <div class="flex gap-5">
        <FormInput
          id="zipcode"
          v-model="formData.zipcode"
          class="max-w-[125px]"
          type="text"
          name="zipcode"
          required
          :placeholder="t('components.app.general.labels.zipcode')"
          :label="t('components.app.general.labels.zipcode')" />
        <FormInput
          id="city"
          v-model="formData.city"
          class="flex-1"
          type="text"
          name="city"
          required
          :placeholder="t('components.app.general.labels.city')"
          :label="t('components.app.general.labels.city')" />
      </div>
      <FormSelect
        v-model="formData.countryId"
        name="countryId"
        :options="getCountries.map((country) => {
          return {
            label: country.translated?.name ?? '',
            value: country.id
          }
        })"
        :label="t('components.app.general.labels.country')" />
    </div>
    <BaseButton
      outline
      type="submit">
      {{ isNewAddress ? t('pages.myAccount.addresses.addNewAddress') : t('components.base.addressCard.editAddress') }}
    </BaseButton>
  </form>
</template>

<style scoped>

</style>
