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
  <div class="c-base-filter-select-variant-select space-y-2">
    <a
      v-for="(item, index) in items"
      :key="index"
      :href="createUrl(item.value)"
      class="flex items-center group cursor-pointer hover:text-black "
      @click.prevent="emit('select', item)">
      <div
        class="relative size-4 border border-secondary-light rounded-sm"
        :class="{'bg-success': item.isRefined, 'group-hover:border-secondary group-hover:bg-secondary-light/30': !item.isRefined}">
        <BaseIcon
          v-if="item.isRefined"
          name="check"
          class="absolute left-0.5 bottom-0.5 size-[10px] text-white" />
      </div>
      <div class="flex flex-1 items-center pl-3 pt-0.5 text-sm">
        <div class="flex-1">
          {{ splitLabel(item.value) }}
        </div>
        <div class="opacity-25 text-base">
          ({{ item.count.toLocaleString() }})
        </div>
      </div>
    </a>
  </div>
</template>

<style scoped>

</style>
