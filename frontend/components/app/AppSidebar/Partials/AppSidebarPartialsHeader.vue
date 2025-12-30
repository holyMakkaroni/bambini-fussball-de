<script setup lang="ts">
import type { Icon } from '~/types/components/base'

defineProps({
  headline: {
    type: String,
    default: null
  },
  icon: {
    type: String as PropType<Icon>,
    default: null
  },
  iconBadge: {
    type: Number,
    default: null
  },
  showBadge: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <div v-if="headline">
    <div class="app-sidebar-partials-header flex px-10 pt-[60px] pb-[30px]">
      <div class="flex items-center h-[40px] space-x-5">
        <div
          v-if="icon"
          class="flex-none relative">
          <div
            v-if="showBadge"
            class="absolute w-7 h-7 text-xxs font-semibold border-2 border-white rounded-full flex justify-center items-center top-1 right-0 transform translate-x-1/2 -translate-y-1/2"
            :class="{'bg-secondary-light': iconBadge === 0, 'bg-success text-white': iconBadge > 0}">
            {{ iconBadge }}
          </div>
          <BaseIcon
            :name="icon"
            class="size-6" />
        </div>
        <div class="flex-1 text-wrap hyphens-auto text-xl font-medium">
          {{ headline }}
        </div>
      </div>
    </div>
    <BaseDivider variant="solid" />
  </div>
</template>

<style scoped>

</style>
