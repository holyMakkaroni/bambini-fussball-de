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
  <div class="flex flex-col space-y-3">
    <div
      v-for="option in props.filter.options"
      :key="option.id"
      class="flex items-center group cursor-pointer"
      @click="selectProperty(props.filter.id, props.filter.customFields?.filterName || '', props.filter.customFields?.filterPriority || 0, {
        id: option.id,
        optionValue: option.customFields?.optionValue || ''
      })">
      <div
        class="relative size-4 border border-secondary-light rounded-sm"
        :class="{'bg-secondary-light': isOptionSelected(selectedOptionIds, option.id), 'group-hover:border-secondary group-hover:bg-secondary-light/30': !isOptionSelected(selectedOptionIds, option.id)}">
        <BaseIcon
          v-if="isOptionSelected(selectedOptionIds, option.id)"
          name="check"
          class="absolute left-1 bottom-1 size-3" />
      </div>
      <div class="flex-1 pl-3 text-sm">
        {{ option.translated.name }}
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
