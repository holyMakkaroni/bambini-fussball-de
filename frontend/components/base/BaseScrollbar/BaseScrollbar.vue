<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  fullWidth: {
    type: Boolean,
    default: false
  },
  arrows: {
    type: Boolean,
    default: false
  },
  padding: {
    type: String,
    default: null,
    validation: (value: string) => {
      ['small', 'normal'].includes(value)
    }
  },
  trackVariant: {
    type: String,
    default: 'gray',
    validation: (value: string) => {
      ['gray', 'white'].includes(value)
    }
  },
  scrollAmount: {
    type: Number,
    default: 200
  },
  items: {
    type: Number,
    default: 2
  },
  gap: {
    type: Number,
    default: 20
  },
  itemsToSlide: {
    type: Number,
    default: 1
  },
  responsive: {
    type: Object,
    default: () => ({})
  }
})

const anchors = ref<NodeListOf<HTMLAnchorElement>>()
const scrollContent = ref<HTMLElement | null>(null)
const scrollContainer = ref<HTMLElement | null>(null)
const scrollbar = ref<HTMLElement | null>(null)
const scrollThumb = ref<HTMLElement | null>(null)
const isDragging = ref<boolean>(false)
const isAtStart = ref<boolean>(true)
const isAtEnd = ref<boolean>(false)
const showScrollbar = ref<boolean>(false)
const startX = ref<number>(0)
const scrollStartLeft = ref<number>(0)
const startTouchX = ref<number>(0)
const thumbDragging = ref<boolean>(false)
const moveThreshold = 5 // Pixel, die als Drag erkannt werden
const currentMouseX = ref<number>(0)
const currentMouseY = ref<number>(0)

const safeArea = 64

const disableTextSelection = () => {
  document.body.style.userSelect = 'none'
}

const enableTextSelection = () => {
  document.body.style.userSelect = ''
}

const updateThumbSize = () => {
  if (!scrollContainer.value || !scrollContent.value) { return }

  const scrollContainerWidth = scrollContainer.value.clientWidth
  const scrollContentWidth = scrollContent.value.scrollWidth - safeArea
  const thumbWidth = (scrollContainerWidth / scrollContentWidth) * scrollContainerWidth

  if (scrollThumb.value) {
    scrollThumb.value.style.width = `${thumbWidth}px`
  }
}

const getElementWidth = () => {
  if (!scrollContainer.value || !scrollContent.value) {
    return
  }

  const settings = getResponsiveSettings({ items: props.items, gap: props.gap, itemsToSlide: props.itemsToSlide, responsive: props.responsive }, window.innerWidth)
  const scrollContainerWidth = scrollContainer.value.clientWidth - (settings.gap * (settings.items - 1))

  return scrollContainerWidth / settings.items
}

const updateElementWidth = () => {
  if (!scrollContainer.value || !scrollContent.value) {
    return
  }

  const settings = getResponsiveSettings({ items: props.items, gap: props.gap, itemsToSlide: props.itemsToSlide, responsive: props.responsive }, window.innerWidth)

  const elementWidth = getElementWidth()
  const elements = scrollContent.value.children

  for (let i = 0; i < elements.length; i++) {
    elements[i].style.width = `${elementWidth}px`
  }

  scrollContent.value.style.gap = `${settings.gap}px`
}

const getResponsiveSettings = (config: {items: number, gap: number, itemsToSlide: number, responsive: object}, viewportWidth: number) => {
  const { items: defaultItems, gap: defaultGap, itemsToSlide: defaultItemToSlide, responsive } = config

  let settings = { items: defaultItems, gap: defaultGap, itemsToSlide: defaultItemToSlide }

  if (responsive) {
    const breakpoints = Object.keys(responsive)
      .map(Number)
      .sort((a, b) => a - b)

    for (const bp of breakpoints) {
      if (viewportWidth >= bp) {
        settings = { ...settings, ...responsive[bp] }
      }
    }
  }

  return settings
}

const updateThumbPosition = () => {
  if (!scrollContent.value || !scrollContainer.value || !scrollbar.value || !scrollThumb.value) { return }

  const scrollPercentage = scrollContent.value.scrollLeft / ((scrollContent.value.scrollWidth - safeArea) - scrollContainer.value.clientWidth)
  const thumbPos = scrollPercentage * (scrollbar.value.clientWidth - scrollThumb.value.clientWidth)
  scrollThumb.value.style.left = `${thumbPos}px`

  isAtStart.value = scrollContent.value.scrollLeft === 0
  isAtEnd.value = Math.ceil(scrollContent.value.scrollLeft + scrollContainer.value.clientWidth) >= (scrollContent.value.scrollWidth - safeArea)

  showScrollbar.value = (scrollContent.value.scrollWidth - safeArea) > scrollContainer.value.clientWidth
}

const scrollLeftByAmount = () => {
  if (!scrollContent.value) { return }

  const settings = getResponsiveSettings({ items: props.items, gap: props.gap, itemsToSlide: props.itemsToSlide, responsive: props.responsive }, window.innerWidth)
  const elementWidth = getElementWidth()

  if (elementWidth) {
    scrollContent.value.scrollBy({
      left: -(elementWidth + settings.gap) * settings.itemsToSlide,
      behavior: 'smooth'
    })
    updateThumbPosition()
  }
}

const scrollRightByAmount = () => {
  if (!scrollContent.value) { return }

  const settings = getResponsiveSettings({ items: props.items, gap: props.gap, itemsToSlide: props.itemsToSlide, responsive: props.responsive }, window.innerWidth)
  const elementWidth = getElementWidth()

  if (elementWidth) {
    scrollContent.value.scrollBy({
      left: (elementWidth + settings.gap) * settings.itemsToSlide,
      behavior: 'smooth'
    })
    updateThumbPosition()
  }
}

const onMouseDown = (e: MouseEvent) => {
  if (!scrollContent.value) { return }

  isDragging.value = false // Initialisieren als false
  startX.value = e.pageX - scrollContent.value.offsetLeft
  scrollStartLeft.value = scrollContent.value.scrollLeft

  currentMouseX.value = e.pageX
  currentMouseY.value = e.pageY

  disableTextSelection()

  document.addEventListener('mousemove', onMouseMove)
  document.addEventListener('mouseup', onMouseUp)
}

const onMouseMove = (e: MouseEvent) => {
  if (!scrollContent.value) { return }

  const deltaX = Math.abs(e.pageX - currentMouseX.value)
  const deltaY = Math.abs(e.pageY - currentMouseY.value)

  if (deltaX > moveThreshold || deltaY > moveThreshold) {
    isDragging.value = true
  }

  if (isDragging.value) {
    e.preventDefault()

    const x = e.pageX - scrollContent.value.offsetLeft
    const walk = (x - startX.value)
    scrollContent.value.scrollLeft = scrollStartLeft.value - walk

    updateThumbPosition()
  }
}

const onMouseUp = () => {
  enableTextSelection()

  document.removeEventListener('mousemove', onMouseMove)
  document.removeEventListener('mouseup', onMouseUp)
}

const onTouchStart = (e: TouchEvent) => {
  if (!scrollContent.value) { return }

  isDragging.value = true
  startTouchX.value = e.touches[0].pageX - scrollContent.value.offsetLeft
  scrollStartLeft.value = scrollContent.value.scrollLeft

  disableTextSelection()

  document.addEventListener('touchmove', onTouchMove, { passive: false })
  document.addEventListener('touchend', onTouchEnd)
}

const onTouchMove = (e: TouchEvent) => {
  if (!isDragging.value || !scrollContent.value) { return }

  e.preventDefault()
  const x = e.touches[0].pageX - scrollContent.value.offsetLeft
  const walk = (x - startTouchX.value)
  scrollContent.value.scrollLeft = scrollStartLeft.value - walk
  updateThumbPosition()
}

const onTouchEnd = () => {
  isDragging.value = false

  enableTextSelection()

  document.removeEventListener('touchmove', onTouchMove)
  document.removeEventListener('touchend', onTouchEnd)
}

const onScrollbarMouseDown = (e: MouseEvent) => {
  thumbDragging.value = true
  startX.value = e.pageX

  disableTextSelection()

  document.addEventListener('mousemove', onThumbMouseMove)
  document.addEventListener('mouseup', onThumbMouseUp)
}

const onThumbMouseMove = (e: MouseEvent) => {
  if (!thumbDragging.value || !scrollbar.value || !scrollContent.value) { return }

  const deltaX = e.pageX - startX.value
  startX.value = e.pageX
  scrollContent.value.scrollLeft += (deltaX / scrollbar.value.clientWidth) * (scrollContent.value.scrollWidth - safeArea)

  updateThumbPosition()
}

const onThumbMouseUp = () => {
  thumbDragging.value = false

  enableTextSelection()

  document.removeEventListener('mousemove', onThumbMouseMove)
  document.removeEventListener('mouseup', onThumbMouseUp)
}

const onThumbTouchStart = (e: TouchEvent) => {
  thumbDragging.value = true
  startTouchX.value = e.touches[0].pageX

  disableTextSelection()

  document.addEventListener('touchmove', onThumbTouchMove, { passive: false })
  document.addEventListener('touchend', onThumbTouchEnd)
}

const onThumbTouchMove = (e: TouchEvent) => {
  if (!thumbDragging.value || !scrollbar.value || !scrollContent.value) { return }

  const deltaX = e.touches[0].pageX - startTouchX.value
  startTouchX.value = e.touches[0].pageX
  scrollContent.value.scrollLeft += (deltaX / scrollbar.value.clientWidth) * scrollContent.value.scrollWidth

  updateThumbPosition()
}

const onThumbTouchEnd = () => {
  thumbDragging.value = false

  enableTextSelection()

  document.removeEventListener('touchmove', onThumbTouchMove)
  document.removeEventListener('touchend', onThumbTouchEnd)
}

const onWheel = (e: WheelEvent) => {
  if (!scrollContent.value) { return }

  if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
    e.preventDefault()
    scrollContent.value.scrollLeft += e.deltaX

    updateThumbPosition()
  }
}

const onScroll = () => {
  updateThumbPosition()
}

const handleResize = () => {
  updateElementWidth()
  updateThumbSize()
  updateThumbPosition()
}

onMounted(() => {
  updateElementWidth()
  updateThumbSize()
  updateThumbPosition()

  anchors.value = scrollContent.value?.querySelectorAll('a')

  if (anchors.value) {
    addAnchorEventListener(anchors.value)
  }

  window.addEventListener('resize', handleResize)
})

onBeforeUnmount(() => {
  if (anchors.value) {
    removeAnchorEventListener(anchors.value)
  }

  window.removeEventListener('resize', handleResize)
})

const addAnchorEventListener = (anchors: NodeListOf<HTMLAnchorElement>) => {
  anchors?.forEach((anchor) => {
    anchor.addEventListener('click', onAnchorClick, true)
  })
}

const removeAnchorEventListener = (anchors: NodeListOf<HTMLAnchorElement>) => {
  anchors?.forEach((anchor) => {
    anchor.removeEventListener('click', onAnchorClick)
  })
}

const onAnchorClick = (e: MouseEvent) => {
  if (isDragging.value) {
    e.preventDefault()
  }
}

const trackVariantStyle = computed(() => {
  let trackClass = ''
  switch (props.trackVariant) {
    case 'gray':
      trackClass = 'bg-secondary-light'
      break
    case 'white':
      trackClass = 'bg-white'
  }

  return trackClass
})

const paddingSize = computed(() => {
  let paddingSizeClass = ''
  switch (props.padding) {
    case 'small':
      paddingSizeClass = 'pb-0.5 md:pb-1'
      break
    case 'normal':
      paddingSizeClass = 'pb-6 md:pb-8'
  }

  return paddingSizeClass
})
</script>

<template>
  <div class="base-scrollbar relative flex items-center group">
    <div
      ref="scrollContainer"
      class="relative w-full h-full user-select-none"
      :class="{'cursor-grab': showScrollbar}"
      @wheel="onWheel">
      <div
        ref="scrollContent"
        class="flex justify-start h-full overflow-hidden p-8 -mx-8 whitespace-nowrap transition-transform duration-300 ease-linear"
        :class="[paddingSize]"
        @mousedown="onMouseDown"
        @touchstart="onTouchStart"
        @scroll="onScroll">
        <slot />
      </div>
      <div
        v-show="showScrollbar"
        ref="scrollbar"
        class="absolute bottom-0 transition-default rounded-[5px] h-[2px] group-hover:h-[4px] cursor-pointer"
        :class="[trackVariantStyle, {'h-[4px]': thumbDragging, 'w-[calc(100%-2rem)] left-4 xl:left-0 xl:w-full': fullWidth, 'w-full': !fullWidth}]">
        <div
          ref="scrollThumb"
          class="absolute top-0 left-0 h-full rounded-[5px] cursor-pointer bg-primary hover:bg-primary-dark"
          :class="{ 'bg-primary-dark': thumbDragging }"
          @mousedown="onScrollbarMouseDown"
          @touchstart="onThumbTouchStart" />
      </div>
    </div>
    <button
      v-show="arrows"
      class="arrow left-3 z-40 xl:-left-6"
      :class="{'group-hover:opacity-100': !isAtStart, 'opacity-0': isAtStart}"
      @click="scrollLeftByAmount">
      <BaseIcon
        class="w-3 h-3 rotate-180"
        name="chevron" />
    </button>
    <button
      v-show="arrows"
      class="arrow right-3 z-40 xl:-right-6"
      :class="{'group-hover:opacity-100': !isAtEnd, 'group-hover:opacity-0': isAtEnd}"
      @click="scrollRightByAmount">
      <BaseIcon
        class="w-3 h-3"
        name="chevron" />
    </button>
  </div>
</template>

<style scoped>
</style>
