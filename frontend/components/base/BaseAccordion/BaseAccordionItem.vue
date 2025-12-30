<script setup lang="ts">
const props = defineProps({
  index: {
    type: Number,
    required: true
  }
})

const expandedItemIndex = inject<number|null>('expandedItemIndex')
const setExpandedItem = inject('setExpandedItem')
const isExpanded = ref<boolean>(false)

const toggle = () => {
  if (isExpanded.value) {
    isExpanded.value = false
    setExpandedItem(null)
  } else {
    setExpandedItem(props.index)
  }
}

watch(expandedItemIndex, (newVal) => {
  isExpanded.value = newVal === props.index
})

onMounted(() => {
  if (expandedItemIndex.value === props.index) {
    isExpanded.value = true
  }
})

const enter = (el: HTMLElement) => {
  el.style.height = el.scrollHeight + 'px'
}

const leave = (el: HTMLElement) => {
  el.style.height = '0'
}
</script>

<template>
  <div class="c-base-accordion-item">
    <div
      class="flex"
      @click="toggle">
      <div class="flex-1">
        <slot name="head" />
      </div>
      <BaseIcon
        name="chevron"
        class="w-4 h-4 transition-transform duration-300"
        :class="{'bg-primary rotate-90': isExpanded}" />
    </div>
    <Transition
      name="accordion"
      @before-enter="leave"
      @enter="enter"
      @before-leave="enter"
      @leave="leave">
      <div
        v-show="isExpanded"
        class="transition-all duration-300 overflow-hidden">
        <div class="py-6 px-8">
          <slot />
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>

</style>
