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

const getImagePath = (item: any) => {
  const swatchData = getSwatchData(item.value)

  if (swatchData.length !== 2) {
    return
  }

  return swatchData[1]
}
</script>

<template>
  <div class="c-base-filter-select-variant-media">
    <div class="grid grid-cols-4 gap-3">
      <a
        v-for="(item, index) in items"
        :key="index"
        :href="createUrl(item.value)"
        class="flex flex-col group cursor-pointer hover:text-black"
        @click.prevent="emit('select', item)">
        <BaseBorderedCard
          v-if="getImagePath(item)"
          class="!p-0.5 group-hover:border-secondary"
          :class="{
            'border !border-secondary': item.isRefined,
          }">
          <BaseImage
            :width="90"
            :height="90"
            provider="gumlet"
            sizes="90"
            :lazy="true"
            :image="{
              name: getLabel(item),
              title: getLabel(item),
              alt: getLabel(item),
              filename: getImagePath(item)
            }" />
        </BaseBorderedCard>
        <div class="text-xxs text-center">
          {{ getLabel(item) }} ({{ item.count.toLocaleString() }})
        </div>
      </a>
    </div>
  </div>
</template>

<style scoped>

</style>
