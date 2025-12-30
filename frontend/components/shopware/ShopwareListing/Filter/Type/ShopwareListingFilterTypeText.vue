<script setup lang="ts">
import type { PropType } from 'vue'
import type { Schemas } from '#shopware'
import type { Option, PropertyMapping } from '~/types/shopware'

const props = defineProps({
  filter: {
    type: Object as PropType<Schemas['PropertyGroup']>,
    required: true
  }
})

const propertyMapping = useState<PropertyMapping>('propertyMapping')
const { path } = useRoute()
const { addOrUpdateOption, getSelectedOptionIds } = useSeoFilterUrl(propertyMapping, path)
const { isOptionSelected } = useShopwareHelper()

const selectProperty = (filterId: string, filterName: string, filterPriority: number, option: Option) => {
  addOrUpdateOption(filterId, filterName, filterPriority, {
    id: option.id,
    optionValue: option.optionValue
  })
}

const selectedOptionIds = computed(() => {
  return getSelectedOptionIds(props.filter.id)
})
</script>

<template>
  <div class="grid grid-cols-3 gap-3">
    <div
      v-for="option in props.filter.options"
      :key="option.id"
      class="flex items-center cursor-pointer border px-3 py-1.5"
      :class="{'border-primary text-white bg-primary': isOptionSelected(selectedOptionIds, option.id), 'bg-white border-secondary-light hover:border-secondary': !isOptionSelected(selectedOptionIds, option.id)}"
      @click="selectProperty(props.filter.id, props.filter.customFields?.filterName || '', props.filter.customFields?.filterPriority || 0, {
        id: option.id,
        optionValue: option.customFields?.optionValue || ''
      })">
      {{ option.translated.name }}
    </div>
  </div>
</template>

<style scoped>

</style>
