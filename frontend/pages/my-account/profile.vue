<script setup lang="ts">
import BaseChangeEmailForm from '~/components/base/BaseChangeEmailForm/BaseChangeEmailForm.vue'

const { apiClient } = useShopwareContext()
const { t } = useI18n()
const { user, updatePersonalInfo, refreshUser } = useUser()

const firstName = computed(() => user.value?.firstName)
const lastName = computed(() => user.value?.lastName)
const email = computed(() => user.value?.email)
const salutationNotSpecified = computed(() => user.value?.salutation?.salutationKey === 'not_specified')
const salutation = computed(() => user.value?.salutation?.translated?.displayName)

const { data: salutationOptions } = await useAsyncData(
  'load-shopware-salutations', async () => {
    const { data } = await apiClient.invoke('readSalutation post /salutation')
    return data.elements || []
  }, {
    // TODO: Remove if bug https://github.com/nuxt/nuxt/issues/18405 is fixed in Vue 3.5
    getCachedData (key, nuxt) {
      return nuxt.payload.data[key] || nuxt.static.data[key]
    },
    transform: (salutationOptions) => {
      return salutationOptions.map((salutation) => {
        return {
          value: salutation.id,
          label: salutation.translated ? salutation.translated.displayName : salutation.displayName
        }
      })
    }
  }
)

const invokeUpdatePersonalInfo = async () => {
  await updatePersonalInfo({
    firstName: user.value?.firstName ?? '',
    lastName: user.value?.lastName ?? '',
    title: user.value?.title ?? '',
    salutationId: user.value?.salutation?.id ?? ''
  })

  await refreshUser({
    associations: {
      salutation: {}
    }
  })
}
</script>

<template>
  <div class="type-my-account-profile">
    <BaseAccountHeader
      :title="t('pages.myAccount.profile.headline')"
      :description="t('pages.myAccount.profile.description')" />
    <ul class="flex flex-col leading-8">
      <li class="flex">
        <span class="font-bold">
          {{ t('pages.myAccount.profile.personalData') }}:
          <span class="flex-1 font-normal ml-0.5">
            <span v-if="!salutationNotSpecified">{{ salutation }}</span>
            {{ firstName }} {{ lastName }}
          </span>
        </span>
      </li>
      <li class="flex">
        <i18n-t
          tag="span"
          scope="global"
          class="font-bold"
          keypath="pages.myAccount.profile.yourEmail">
          <template #email>
            <span class="flex-1 font-normal ml-0.5">{{ email }}</span>
          </template>
        </i18n-t>
      </li>
    </ul>

    <div class="flex flex-col">
      <BaseAccountSubHeader :title="t('pages.myAccount.profile.personalData')" />
      <form @submit.prevent="invokeUpdatePersonalInfo">
        <BaseAccountWrapper>
          <div class="w-full max-w-[200px]">
            <FormSelect
              v-if="user?.salutation"
              v-model="user.salutation.id"
              name="salutation"
              :options="salutationOptions"
              required
              :label="t('components.app.general.labels.salutation')" />
          </div>

          <div class="flex flex-col sm:flex-row gap-5 mt-5 mb-10">
            <div class="flex-1">
              <FormInput
                v-if="user?.firstName"
                id="firstName"
                v-model="user.firstName"
                name="firstName"
                type="text"
                :label="t('components.app.general.labels.firstName')"
                required />
            </div>
            <div class="flex-1">
              <FormInput
                v-if="user?.lastName"
                id="lastName"
                v-model="user.lastName"
                name="lastName"
                type="text"
                :label="t('components.app.general.labels.lastName')"
                required />
            </div>
          </div>

          <BaseButton
            outline
            variant="primary"
            type="submit">
            {{ t('components.app.general.labels.saveChange') }}
          </BaseButton>
        </BaseAccountWrapper>
      </form>
    </div>

    <div class="flex flex-col">
      <BaseAccountSubHeader :title="t('pages.myAccount.profile.accessData')" />
      <i18n-t
        tag="div"
        scope="global"
        class="font-bold"
        keypath="pages.myAccount.profile.yourEmail">
        <template #email>
          <span class="flex-1 font-normal ml-0.5">{{ email }}</span>
        </template>
      </i18n-t>

      <BaseTabs
        :tabs="[
          {
            label: 'E-Mail-Adresse ändern',
            name: 'email'
          },
          {
            label: 'Passwort ändern',
            name: 'password'
          },
        ]"
        class="flex-1 flex flex-col mt-9"
        tab-head-class="flex-col gap-y-5"
        content-class="mt-8 sm:mt-12"
        active-init-tab="null">
        <template #tabHead-email="{ setActiveTab, activeTab, tab }">
          <BaseButton
            variant="primary"
            outline
            :custom-class="activeTab === tab.name ? 'active' : ''"
            @click="setActiveTab(tab.name)">
            {{ tab.label }}
          </BaseButton>
        </template>
        <template #tabHead-password="{ setActiveTab, activeTab, tab }">
          <BaseButton
            variant="primary"
            outline
            :custom-class="activeTab === tab.name ? 'active' : ''"
            @click="setActiveTab(tab.name)">
            {{ tab.label }}
          </BaseButton>
        </template>
        <template #email>
          <BaseAccountSubHeader :title="t('components.app.general.labels.email')" />
          <BaseAccountWrapper>
            <BaseChangeEmailForm @email-changed-success="refreshUser" />
          </BaseAccountWrapper>
        </template>
        <template #password>
          <BaseAccountSubHeader :title="t('components.app.general.labels.password')" />
          <BaseAccountWrapper>
            <BaseChangePasswordForm @password-changed-success="refreshUser" />
          </BaseAccountWrapper>
        </template>
      </BaseTabs>
    </div>
  </div>
</template>

<style scoped>

</style>
