<script setup lang="ts">
const { mainNavigationDisplayStatus } = useMainNavigation()
</script>

<template>
  <div>
    <AppNotification />
    <AppNavigation :show="mainNavigationDisplayStatus" />
    <AppHeader />
    <main>
      <slot />
    </main>
    <AppFooter
      show-newsletter
      show-pre-footer />
  </div>
</template>
