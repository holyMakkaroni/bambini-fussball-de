<script setup lang="ts">
const props = defineProps({
  size: {
    type: String,
    default: 'small',
    validator (value: string) {
      return ['compact', 'small', 'base'].includes(value)
    }
  },
  color: {
    type: String,
    default: 'red',
    validator (value: string) {
      return ['red', 'black'].includes(value)
    }
  }
})

let pillSize = ''
let pillColor = ''

switch (props.size) {
  case 'compact':
    pillSize = 'text-xs rounded-[2px] py-1 px-2'
    break
  case 'small':
    pillSize = 'text-xxs rounded-[5px] py-1 px-3'
    break
  case 'base':
    pillSize = 'text-base rounded-[5px] py-1 px-3'
    break
}

switch (props.color) {
  case 'red':
    pillColor = 'bg-primary'
    break
  case 'black':
    pillColor = 'bg-secondary'
    break
}
</script>

<template>
  <div class="c-base-pill">
    <div
      class="text-white font-semibold uppercase text-center h-full flex items-center"
      :class="[pillSize, pillColor]">
      <slot />
    </div>
  </div>
</template>

<style scoped>

</style>
