<script setup lang="ts">
import type { ApiError } from '@shopware/api-client'
import AppSidebarPartialsHeader from '~/components/app/AppSidebar/Partials/AppSidebarPartialsHeader.vue'

const { login, isLoggedIn } = useUser()
const { t } = useI18n()

const credentials = ref({
  username: '',
  password: ''
})

const invokeLogin = async () => {
  try {
    await login(credentials.value)
    credentials.value = {
      username: '',
      password: ''
    }
  } catch (error) {
    if (error.details) {
      error.details.errors.forEach((error: ApiError) => {
        console.log(error)
      })
    }
  }
}
</script>

<template>
  <div
    v-if="!isLoggedIn"
    class="base-login-form">
    <AppSidebarPartialsHeader
      :headline="t('components.base.loginForm.headline')"
      icon="user" />
    <form
      class="px-10 mt-10"
      @submit.prevent="invokeLogin">
      <FormInput
        id="email"
        v-model="credentials.username"
        class="mb-5"
        type="email"
        placeholder="max@mustermann.de"
        autocomplete="username"
        required />
      <FormInput
        id="password"
        v-model="credentials.password"
        type="password"
        placeholder="**********"
        autocomplete="current-password"
        required />
      <div class="mb-6 text-xxs mt-2 pl-2">
        <NuxtLink to="#">
          {{ t('components.app.general.labels.lostPassword') }}
        </NuxtLink>
      </div>
      <BaseButton
        type="submit"
        icon="exit"
        icon-position="left"
        icon-class="size-4"
        class="!w-full">
        <div>{{ t('components.app.general.labels.login') }}</div>
      </BaseButton>
    </form>
    <BaseDivider
      variant="solid"
      class="my-10" />
    <i18n-t
      keypath="components.base.loginForm.register.text"
      tag="div"
      class="px-10 nl2br text-sm"
      scope="global">
      <template #url>
        <NuxtLink
          to="#"
          class="decoration-current underline">
          {{ t('components.base.loginForm.register.label') }}
        </NuxtLink>
      </template>
      <template #checkout-url>
        <NuxtLink
          to="#"
          class="decoration-current underline">
          {{ t('components.base.loginForm.register.guestCheckout') }}
        </NuxtLink>
      </template>
    </i18n-t>
  </div>
</template>

<style scoped>

</style>
