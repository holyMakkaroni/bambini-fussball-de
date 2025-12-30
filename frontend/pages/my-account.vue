<script setup lang="ts">
definePageMeta({
  layout: 'account',
  middleware: 'auth'
})
</script>

<template>
  <div>
    <NuxtPage
      :page-key="route => route.fullPath"
      class="space-y-8 sm:space-y-12" />
  </div>
</template>

<style scoped>

</style>
