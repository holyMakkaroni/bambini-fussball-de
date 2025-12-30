<script setup lang="ts">
const { t } = useI18n()
const { user, logout } = useUser()
const sidebarLinks = useStaticPages('account')
const localePath = useLocalePath()

const invokeLogout = async () => {
  await logout()
  await navigateTo('/')
}
</script>

<template>
  <div class="c-base-account-menu">
    <AppSidebarPartialsHeader
      :headline="t('components.base.accountMenu.headline', {
        firstName: user?.firstName,
        lastName: user?.lastName
      })"
      icon="user" />
    <ul class="flex flex-col divide-y divide-solid divide-secondary-light">
      <li
        v-for="(link, index) in sidebarLinks"
        :key="index"
        class="flex flex-row items-center px-10 py-3 cursor-pointer group transition-default hover:bg-primary"
        role="listitem">
        <NuxtLink
          :to="localePath(link.url)"
          :title="link.name"
          class="flex-1 font-medium group-hover:decoration-transparent group-hover:text-white">
          {{ link.name }}
        </NuxtLink>
      </li>
    </ul>
    <BaseDivider
      variant="solid"
      class="mb-5" />
    <div class="px-10">
      <BaseButton
        type="button"
        icon="exit"
        icon-position="left"
        icon-class="size-4"
        class="!w-full"
        @click="invokeLogout">
        <div>{{ t('components.app.general.labels.logout') }}</div>
      </BaseButton>
    </div>
  </div>
</template>

<style scoped>

</style>
