<script setup lang="ts">
import type { FormLabel } from '@/types/components/form'

withDefaults(defineProps<FormLabel>(), {})
</script>

<template>
  <label
    :for="for"
    class="c-form-label text-sm leading-6">
    <span>
      {{ label }} <slot />
    </span>
    <span v-if="required">*</span>
  </label>
</template>

<style scoped>

</style>
