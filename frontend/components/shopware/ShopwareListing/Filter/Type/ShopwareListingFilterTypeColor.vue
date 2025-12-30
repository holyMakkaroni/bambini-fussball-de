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
  <div class="grid grid-cols-2 gap-3">
    <div
      v-for="option in props.filter.options"
      :key="option.id"
      class="flex items-center cursor-pointer group"
      @click="selectProperty(props.filter.id, props.filter.customFields?.filterName || '', props.filter.customFields?.filterPriority || 0, {
        id: option.id,
        optionValue: option.customFields?.optionValue || ''
      })">
      <div
        class="relative w-8 h-8 rounded-full border-2 border-secondary-light transition-default"
        :class="{
          'group-hover:bg-white group-hover:border-secondary': !isOptionSelected(selectedOptionIds, option.id),
        }"
        :style="`background-color: ${option.colorHexCode}`">
        <div
          v-if="isOptionSelected(selectedOptionIds, option.id)"
          class="absolute bg-white size-4 rounded-full translate-x-1.5 translate-y-1.5 flex justify-center items-center">
          <BaseIcon
            name="check"
            class="size-2" />
        </div>
        <span class="sr-only">{{ option.translated.name }}</span>
      </div>
      <div
        class="flex-1 text-xs pl-3 text-secondary group-hover:text-secondary"
        :class="{
          'text-secondary': isOptionSelected(selectedOptionIds, option.id),
          'text-secondary/50': !isOptionSelected(selectedOptionIds, option.id)
        }">
        {{ option.translated.name }}
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
