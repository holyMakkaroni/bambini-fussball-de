<script setup lang="ts">

import type { RefinementListItem } from 'instantsearch.js/es/connectors/refinement-list/connectRefinementList'

defineProps({
  items: {
    type: Array as PropType<RefinementListItem[]>,
    required: true
  },
  createUrl: {
    type: Function,
    required: true
  }
})

const emit = defineEmits(['select'])
const { splitLabel } = useAlgoliaHelper()
</script>

<template>
  <div class="c-base-filter-select-text">
    <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 gap-3">
      <a
        v-for="(item, index) in items"
        :key="index"
        :href="createUrl(item.value)"
        class="flex items-center cursor-pointer border px-3 py-1.5 !no-underline hover:text-black"
        :class="{'border-primary text-white hover:text-white bg-primary': item.isRefined, 'bg-white hover:text-black border-secondary-light hover:border-secondary': !item.isRefined}"
        @click.prevent="emit('select', item)">
        {{ splitLabel(item.label) }}
      </a>
    </div>
  </div>
</template>

<style scoped>

</style>
