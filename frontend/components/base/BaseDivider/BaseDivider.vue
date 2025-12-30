<script setup lang="ts">
import type { BaseDivider } from '~/types/components/base'

withDefaults(defineProps<BaseDivider>(), {
  variant: 'solid'
})
</script>

<template>
  <hr
    class="c-base-divider"
    :class="[variant]">
</template>

<style scoped>

</style>
