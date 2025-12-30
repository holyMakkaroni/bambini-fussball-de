<script setup lang="ts">
import { onClickOutside } from '@vueuse/core'

const props = defineProps({
  show: {
    type: Boolean,
    required: true,
    default: false
  },
  position: {
    type: String,
    default: 'left',
    validator: (value: string) => ['left', 'right', 'center'].includes(value)
  },
  closePosition: {
    type: String,
    default: 'outside',
    validator: (value: string) => ['outside', 'inside'].includes(value)
  }
})

const emit = defineEmits([
  'closeSidebar'
])

const { t } = useI18n()
const sidebarRef = ref(null)
const transitionClassIn = computed(() => {
  return props.position === 'left' ? 'translate-x-0' : '-translate-x-0'
})

const transitionClassOut = computed(() => {
  switch (props.position) {
    case 'left':
      return '-translate-x-[calc(100%+100px)]'
    case 'right':
      return 'translate-x-[calc(100%+100px)]'
    default:
      return 'translate-y-[calc(100%+100px)]'
  }
})

onClickOutside(
  sidebarRef,
  () => {
    emit('closeSidebar')
  }
)
</script>

<template>
  <div class="app-sidebar">
    <Transition name="fade">
      <BaseOverlay
        v-if="show"
        :show-loading-indicator="false" />
    </Transition>
    <div
      ref="sidebarRef"
      class="fixed bg-white z-50 border-primary transition-transform duration-500 box-shadow"
      :class="[
        position === 'left' ? 'top-0 bottom-0 left-0 w-full max-w-[400px]' : '',
        position === 'right' ? 'top-0 bottom-0 right-0 w-full max-w-[400px]' : '',
        position === 'center' ? 'top-24 bottom-2 left-1 right-1' : '',
        show ? transitionClassIn : transitionClassOut,
        closePosition === 'inside' ? 'border-t-0' : 'border-t-4'
      ]">
      <button
        role="button"
        class="absolute flex justify-center items-center md:items-end"
        :class="[
          position === 'left' ? 'md:translate-x-full right-0' : 'right-0 md:-translate-x-full md:left-0',
          closePosition === 'inside' ? 'text-secondary md:translate-x-0 top-5 md:left-auto right-5 size-[32px] md:w-[32px]' : '-top-1 size-[70px] md:w-[70px] md:h-[100px] md-lg:w-[100px] md-lg:h-[130px] bg-primary text-white md:pb-8'
        ]"
        :title="t('components.base.navigationItem.label')"
        @click="emit('closeSidebar')">
        <BaseIcon
          name="close"
          class="w-8 h-8 -mt-[1px]"
          :class="{'transition-default hover:text-primary': closePosition === 'inside'}" />
      </button>
      <slot />
    </div>
  </div>
</template>

<style scoped>

</style>
