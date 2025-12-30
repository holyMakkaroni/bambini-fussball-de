<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  currentRange: {
    type: Array,
    default: () => [0, 1000]
  },
  min: {
    type: Number,
    default: 0
  },
  max: {
    type: Number,
    default: 1000
  },
  step: {
    type: Number,
    default: 1
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update', 'end'])

const { getFormattedPrice } = usePrice()
const minPrice = ref(props.currentRange[0])
const maxPrice = ref(props.currentRange[1])
const isDragging = ref(null)
const minHandle = ref(null)
const maxHandle = ref(null)
const sliderContainer = ref(null)

minPrice.value = props.currentRange[0] ?? props.min
maxPrice.value = props.currentRange[1] ?? props.max

const formatMinValue = (minValue, minRange) => {
  return minValue !== null && minValue !== minRange ? minValue : ''
}

const formatMaxValue = (maxValue, maxRange) => {
  return maxValue !== null && maxValue !== maxRange ? maxValue : ''
}

watch(() => props.currentRange, (newRange) => {
  minPrice.value = newRange[0] ?? props.min
  maxPrice.value = newRange[1] ?? props.max
})

const updateMinPrice = (event) => {
  const value = Number(event.target.value)
  minPrice.value = Math.min(Math.max(value, props.min), maxPrice.value - props.step)
  emitUpdate()
}

const updateMaxPrice = (event) => {
  const value = Number(event.target.value)
  maxPrice.value = Math.max(Math.min(value, props.max), minPrice.value + props.step)
  emitUpdate()
}

const startDrag = (handle, event) => {
  event.preventDefault()
  isDragging.value = handle
  document.addEventListener('mousemove', drag)
  document.addEventListener('touchmove', drag, { passive: false })
  document.addEventListener('mouseup', stopDrag)
  document.addEventListener('touchend', stopDrag)
}

const stopDrag = () => {
  isDragging.value = null
  document.removeEventListener('mousemove', drag)
  document.removeEventListener('touchmove', drag)
  document.removeEventListener('mouseup', stopDrag)
  document.removeEventListener('touchend', stopDrag)
  emit('end', [minPrice.value, maxPrice.value])
}

const drag = (event) => {
  if (!isDragging.value) { return }
  event.preventDefault()

  const containerRect = sliderContainer.value.getBoundingClientRect()
  const containerWidth = containerRect.width
  const x = (event.type === 'touchmove' ? event.touches[0].clientX : event.clientX) - containerRect.left

  const percentage = Math.min(Math.max(x / containerWidth, 0), 1)
  const value = Math.max(Math.round((percentage * (props.max - props.min) + props.min) / props.step) * props.step, props.min)

  if (isDragging.value === 'min') {
    minPrice.value = Math.min(Math.max(value, props.min), maxPrice.value - props.step)
  } else {
    maxPrice.value = Math.max(Math.min(value, props.max), minPrice.value + props.step)
  }

  emitUpdate()
}

const emitUpdate = () => {
  emit('update', [minPrice.value, maxPrice.value])
}

onUnmounted(() => {
  document.removeEventListener('mousemove', drag)
  document.removeEventListener('touchmove', drag)
  document.removeEventListener('mouseup', stopDrag)
  document.removeEventListener('touchend', stopDrag)
})
</script>

<template>
  <div
    class="c-form-range-slider relative flex items-center min-w-[280px] border border-secondary-light px-3 py-1.5 rounded-md text-sm mr-4 gap-x-3"
    :class="{ 'cursor-not-allowed bg-secondary-light': props.disabled, 'bg-white': !props.disabled }">
    <div>
      <slot name="label" />
    </div>
    <div
      ref="sliderContainer"
      class="flex-1 relative w-full h-1 bg-secondary-light rounded-full">
      <div
        class="absolute h-full bg-primary rounded-full"
        :style="{
          left: `${Math.max(((minPrice - min) / (max - min)) * 100, 0)}%`,
          right: `${Math.max(100 - ((maxPrice - min) / (max - min)) * 100, 0)}%`
        }" />
      <div
        ref="minHandle"
        class="absolute size-4 border border-white bg-primary rounded-full top-0 -translate-y-1.5"
        :class="{ 'z-20': isDragging === 'min', 'cursor-pointer': !props.disabled }"
        :style="{ left: `${Math.max(((minPrice - min) / (max - min)) * 100, 0)}%` }"
        @touchstart="startDrag('min', $event)"
        @mousedown="startDrag('min', $event)">
        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 -translate-y-1 bg-secondary px-1.5 py-0.5 rounded">
          <div class="text-white text-xs whitespace-nowrap">
            {{ getFormattedPrice(formatMinValue(minPrice, min) || min) }}
          </div>
          <div class="absolute left-1/2 bottom-0 w-0 h-0 border-l-[3px] border-l-transparent border-t-[3px] border-t-secondary border-r-[3px] border-r-transparent translate-y-full -translate-x-1/2" />
        </div>
      </div>
      <div
        ref="maxHandle"
        class="absolute size-4 border border-white bg-primary rounded-full top-0 -translate-y-1.5"
        :class="{ 'z-20': isDragging === 'max', 'cursor-pointer': !props.disabled }"
        :style="{ right: `${Math.max(100 - ((maxPrice - min) / (max - min)) * 100, 0)}%` }"
        @touchstart="startDrag('max', $event)"
        @mousedown="startDrag('max', $event)">
        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 -translate-y-1 bg-secondary px-1.5 py-0.5 rounded">
          <div class="text-white text-xs whitespace-nowrap">
            {{ getFormattedPrice(formatMaxValue(maxPrice, max) || max) }}
          </div>
          <div class="absolute left-1/2 bottom-0 w-0 h-0 border-l-[3px] border-l-transparent border-t-[3px] border-t-secondary border-r-[3px] border-r-transparent translate-y-full -translate-x-1/2" />
        </div>
      </div>
    </div>
  </div>
</template>
