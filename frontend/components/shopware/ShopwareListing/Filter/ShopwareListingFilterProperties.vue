<script setup lang="ts">
import type { ConcreteComponent, PropType } from 'vue'
import { onClickOutside } from '@vueuse/core'
import type { ListingFilters, PropertyMapping } from '~/types/shopware'

const props = defineProps({
  filter: {
    type: Object as PropType<ListingFilters>,
    required: true
  }
})

const propertyMapping = useState<PropertyMapping>('propertyMapping')

const { path } = useRoute()
const { getSelectedOptionIds, removeGroup } = useSeoFilterUrl(propertyMapping, path)

const optionIds = computed(() => props.filter.options?.map(option => option.id))
const filterActive = computed(() => optionIds.value?.filter(id => getSelectedOptionIds(props.filter.id).includes(id)))

const isFilterVisible = ref<boolean>(false)
const filterRef = ref(null)

const toogleFilter = () => {
  isFilterVisible.value = !isFilterVisible.value
}

const clearFilter = () => {
  removeGroup(props.filter.id)
}

onClickOutside(
  filterRef,
  () => {
    isFilterVisible.value = false
  }
)

const filterMap = () => {
  const map: {
    [key: string]: ConcreteComponent | string;
  } = {
    text: resolveComponent('ShopwareListingFilterTypeText'),
    color: resolveComponent('ShopwareListingFilterTypeColor'),
    select: resolveComponent('ShopwareListingFilterTypeSelect'),
    media: resolveComponent('ShopwareListingFilterTypeMedia')
  }

  return map[props.filter.displayType]
}
</script>

<template>
  <div
    ref="filterRef"
    class="relative"
    :class="{'z-40': isFilterVisible}">
    <div
      class="flex items-center gap-x-3 border border-secondary-light rounded-md px-3 py-1.5 text-sm cursor-pointer"
      :class="{'bg-secondary-light': filterActive?.length, 'hover:bg-secondary-light/30': !filterActive?.length}"
      @click="toogleFilter">
      <div class="flex-1">
        {{ filter.translated?.name }}
      </div>
      <div
        v-if="filterActive?.length"
        class="bg-black size-4 flex justify-center items-center rounded-full"
        @click="clearFilter">
        <BaseIcon
          name="close"
          class="size-2 text-white" />
      </div>
      <BaseIcon
        name="chevron"
        class="size-2 rotate-90" />
    </div>
    <Transition name="fade-in-out">
      <div
        v-if="isFilterVisible"
        class="absolute -bottom-2 translate-y-full bg-white w-full min-w-[375px] max-h-[300px] overflow-y-scroll box-shadow p-5 z-40">
        <component
          :is="filterMap()"
          :filter="filter"
          :selected-option-ids="getSelectedOptionIds" />
      </div>
    </Transition>
  </div>
</template>
