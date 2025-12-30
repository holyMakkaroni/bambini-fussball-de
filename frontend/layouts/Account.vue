<script setup lang="ts">
const { logout } = useUser()
const { t } = useI18n()
const { mainNavigationDisplayStatus } = useMainNavigation()
const sidebarLinks = useStaticPages('account')
const localePath = useLocalePath()
const items = computed(() => sidebarLinks.map((link, index) => {
  return {
    id: index,
    parent_id: null,
    name: link.name,
    url: localePath(link.url),
    children: []
  }
}))

const invokeLogout = async () => {
  await logout()
  await navigateTo('/')
}
</script>

<template>
  <div>
    <AppNavigation :show="mainNavigationDisplayStatus" />
    <AppHeader />
    <main>
      <AppContentSidebar class="mt-4 md:mt-14">
        <template #sidebar>
          <BaseSidebarNavigation
            v-if="items"
            :items="items" />

          <BaseDivider
            variant="solid"
            class="border-secondary-light mb-3 md:mb-5" />

          <BaseButton
            type="button"
            icon="exit"
            icon-position="left"
            icon-class="size-4"
            class="!w-full"
            @click="invokeLogout">
            <div>{{ t('components.app.general.labels.logout') }}</div>
          </BaseButton>
        </template>
        <template #default>
          <slot />
        </template>
      </AppContentSidebar>
    </main>
    <AppFooter
      show-newsletter
      show-pre-footer />
  </div>
</template>
