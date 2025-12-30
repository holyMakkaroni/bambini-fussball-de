<template>
  <div class="flex flex-wrap overflow-hidden">
    <Transition
      name="fade"
      mode="out-in">
      <ul
        :key="currentIndex"
        role="list"
        class="flex flex-wrap justify-center lg:justify-between gap-6 w-full py-0.5">
        <li
          v-for="(item, index) in visibleItems"
          :key="index"
          role="listitem">
          <slot :name="'step-' + item" />
        </li>
      </ul>
    </Transition>
  </div>
</template>

<script setup lang="ts">
// Props
const props = defineProps({
  stepInterval: {
    type: Number,
    default: 3000
  },
  responsive: {
    type: Object,
    default: () => ({
      768: 1,
      1024: 2,
      default: 5
    })
  }
})

const currentIndex = ref(0)
const items = ref([])
const visibleCount = ref(0)
let interval = null

const slots = useSlots()

const visibleItems = computed(() => {
  const total = Object.keys(slots).length
  const visible = []
  for (let i = 0; i < visibleCount.value; i++) {
    const index = (currentIndex.value + i) % total
    visible.push(index)
  }
  return visible
})

const startRotation = () => {
  interval = setInterval(() => {
    const total = Object.keys(slots).length
    currentIndex.value = (currentIndex.value + visibleCount.value) % total
  }, props.stepInterval)
}

const stopRotation = () => {
  clearInterval(interval)
  interval = null
}

const startOrStopRotation = () => {
  const total = Object.keys(slots).length
  if (visibleCount.value >= total) {
    stopRotation()
  } else {
    stopRotation()
    startRotation()
  }
}

const updateVisibleCount = () => {
  const width = window.innerWidth
  const breakpoints = Object.keys(props.responsive)
    .map(Number)
    .sort((a, b) => a - b)

  for (let i = 0; i < breakpoints.length; i++) {
    if (width <= breakpoints[i]) {
      visibleCount.value = props.responsive[breakpoints[i]]
      return
    }
  }

  visibleCount.value = props.responsive.default
}

const handleResize = () => {
  const prevVisibleCount = visibleCount.value
  updateVisibleCount()
  if (prevVisibleCount !== visibleCount.value) {
    currentIndex.value = 0
  }
  startOrStopRotation()
}

onMounted(() => {
  items.value = Object.keys(slots)
  updateVisibleCount()
  startOrStopRotation()

  window.addEventListener('resize', handleResize)
})

onBeforeUnmount(() => {
  clearInterval(interval)
  window.removeEventListener('resize', handleResize)
})

items.value = Object.keys(slots)
visibleCount.value = props.responsive.default
</script>
