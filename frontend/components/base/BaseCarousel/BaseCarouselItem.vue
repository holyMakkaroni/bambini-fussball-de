<script setup lang="ts">
import type { BaseCarouselItem } from '~/types/components/base'

withDefaults(defineProps<BaseCarouselItem>(), {
  itemClass: 'flex-[0_0_100%]'
})
</script>

<template>
  <div
    class="c-base-carousel-item"
    :class="itemClass">
    <slot />
  </div>
</template>
