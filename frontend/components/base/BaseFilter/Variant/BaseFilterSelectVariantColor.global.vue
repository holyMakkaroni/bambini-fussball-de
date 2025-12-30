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
const { getSwatchData } = useAlgoliaHelper()

const getLabel = (item: any) => {
  const swatchData = getSwatchData(item.value)

  return swatchData[0]
}

const getColorHexCode = (item: any) => {
  const swatchData = getSwatchData(item.value)

  if (swatchData.length !== 2) {
    return
  }

  return swatchData[1]
}
</script>

<template>
  <div class="c-base-filter-select-variant-color">
    <div class="grid grid-cols-2 gap-3">
      <a
        v-for="(item, index) in items"
        :key="index"
        :href="createUrl(item.value)"
        class="flex flex-row items-center group cursor-pointer hover:text-black"
        @click.prevent="emit('select', item)">
        <div
          v-if="getColorHexCode(item)"
          class="relative w-8 h-8 rounded-full border-2 border-secondary-light transition-default"
          :class="{
            'group-hover:bg-white group-hover:border-secondary': !item.isRefined,
          }"
          :style="`background-color: ${getColorHexCode(item)}`">
          <div
            v-if="item.isRefined"
            class="absolute bg-white size-4 rounded-full translate-x-1.5 translate-y-1.5 flex justify-center items-center">
            <BaseIcon
              name="check"
              class="size-2" />
          </div>
          <span class="sr-only">{{ getLabel(item) }}</span>
        </div>
        <div
          class="flex-1 text-xs pl-3 text-secondary group-hover:text-secondary"
          :class="{
            'text-secondary': item.isRefined,
            'text-secondary/50': !item.isRefined,
          }">
          {{ getLabel(item) }} ({{ item.count.toLocaleString() }})
        </div>
      </a>
    </div>
  </div>
</template>

<style scoped>

</style>
