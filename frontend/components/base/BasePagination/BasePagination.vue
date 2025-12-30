<script setup lang="ts">
const props = defineProps({
  total: {
    type: Number,
    required: true
  },
  current: {
    type: Number,
    required: true
  },
  length: {
    type: Number,
    default: 3
  }
})

const { t } = useI18n()

const pages = computed(() => {
  const result: Array<Number> = []
  const pagesArray = Array.from({ length: props.total }, (_, i) => i + 1)

  if (props.current === 1) {
    return pagesArray.slice(0, props.length)
  }

  result.push.apply(result, pagesArray.slice(props.current - props.length - (props.length >= props.current ? props.current - props.length : 1), props.current - 1))
  result.push.apply(result, pagesArray.slice(props.current - 1, props.length + props.current))

  return result
})

const buttonClass = 'w-9 h-9 flex justify-center items-center transition-default hover:bg-primary hover:text-white'

defineEmits(['changePage'])
</script>

<template>
  <div
    v-if="total > 1"
    class="flex flex-wrap items-center gap-5">
    <button
      v-if="props.current !== 1"
      :aria-label="t('components.base.pagination.firstPage')"
      :class="[buttonClass, 'bg-secondary-light']"
      @click="$emit('changePage', 1)">
      <BaseIcon
        name="chevron-double"
        class="size-2.5 rotate-180" />
    </button>
    <button
      v-if="props.current - 1 >= 1"
      :aria-label="t('components.base.pagination.prevPage')"
      :class="[buttonClass, 'bg-secondary-light']"
      @click="$emit('changePage', props.current - 1)">
      <BaseIcon
        name="chevron"
        class="size-2.5 rotate-180" />
    </button>
    <button
      v-for="page in pages"
      :key="page.toString()"
      :aria-label="t('components.base.pagination.goToPage', {
        page: page
      })"
      :class="[buttonClass, {'bg-primary text-white': props.current === page, 'bg-secondary-light': props.current !== page }]"
      @click="$emit('changePage', page)">
      {{ page }}
    </button>
    <button
      v-if="props.total > props.current"
      :aria-label="t('components.base.pagination.nextPage')"
      :class="[buttonClass, 'bg-secondary-light']"
      @click="$emit('changePage', props.current + 1)">
      <BaseIcon
        name="chevron"
        class="size-2.5" />
    </button>
    <button
      v-if="props.current !== props.total"
      :aria-label="t('components.base.pagination.lastPage')"
      :class="[buttonClass, 'bg-secondary-light']"
      @click="$emit('changePage', props.total)">
      <BaseIcon
        name="chevron-double"
        class="size-2.5" />
    </button>
    <div>
      {{ t('components.base.pagination.totalPages', {
        total: props.total
      }) }}
    </div>
  </div>
</template>

<style scoped>

</style>
