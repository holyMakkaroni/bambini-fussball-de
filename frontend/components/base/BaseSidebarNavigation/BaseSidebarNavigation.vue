<script setup lang="ts">
import type { BaseSidebarNavigation } from '~/types/components/base'

defineProps<BaseSidebarNavigation>()
</script>

<template>
  <ul class="c-base-sidebar-navigation space-y-5">
    <BaseSidebarItem
      v-for="item in items"
      :key="item.id"
      class="text-sm font-semibold"
      :item="item" />
  </ul>
</template>

<style scoped>

</style>
