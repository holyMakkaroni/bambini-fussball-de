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
  <div class="grid grid-cols-4 gap-3">
    <div
      v-for="option in props.filter.options"
      :key="option.id"
      class="flex flex-col group cursor-pointer"
      @click="selectProperty(props.filter.id, props.filter.customFields?.filterName || '', props.filter.customFields?.filterPriority || 0, {
        id: option.id,
        optionValue: option.customFields?.optionValue || ''
      })">
      <BaseBorderedCard
        v-if="option.media"
        class="!p-0.5 group-hover:border-secondary"
        :class="{
          'border-2 border-secondary': isOptionSelected(selectedOptionIds, option.id),
        }">
        <BaseImage
          :width="90"
          :height="90"
          provider="gumlet"
          sizes="90"
          :lazy="true"
          :image="{
            name: option.name,
            title: option.media.title,
            alt: option.media.alt,
            filename: option.media.path
          }" />
      </BaseBorderedCard>
      <div class="text-xxs text-center">
        {{ option.translated.name }}
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
