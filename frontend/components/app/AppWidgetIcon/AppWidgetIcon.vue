<script setup lang="ts">
import type { Icon } from '~/types/components/base'

const props = defineProps({
  badgeCount: {
    type: Number,
    default: 0
  },
  icon: {
    type: String as PropType<Icon>,
    required: true
  },
  label: {
    type: String,
    required: true
  }
})
</script>

<template>
  <div class="flex relative group cursor-pointer hover:scale-105 transition-default">
    <ClientOnly>
      <div
        v-if="props.badgeCount"
        class="absolute w-7 h-7 text-xxs font-semibold bg-secondary-light border-2 border-white rounded-full flex justify-center items-center top-1.5 right-0 transform translate-x-1/2 -translate-y-1/2 transition-default group-hover:text-white group-hover:bg-primary">
        {{ props.badgeCount }}
      </div>
    </ClientOnly>
    <button
      role="button"
      class="size-6"
      :title="props.label">
      <slot name="icon">
        <BaseIcon
          v-if="props.icon"
          :name="props.icon"
          class="transition-default group-hover:text-primary" />
      </slot>
    </button>
  </div>
</template>

<style scoped>

</style>
