<script setup lang="ts">
const { mainNavigationDisplayStatus } = useMainNavigation()
</script>

<template>
  <div>
    <AppNavigation :show="mainNavigationDisplayStatus" />
    <AppHeader />
    <main>
      <slot />
    </main>
    <AppFooter
      :show-newsletter="false"
      :show-pre-footer="false" />
  </div>
</template>
