<script setup lang="ts">
import type { RefinementListItem } from 'instantsearch.js/es/connectors/refinement-list/connectRefinementList'
import type { Refinements } from '~/types/filter'

defineProps({
  toggleRefinements: {
    type: Array,
    default: null
  },
  rangeRefinements: {
    type: Array,
    default: null
  },
  filters: {
    type: Object as PropType<Refinements[]>,
    default: null
  }
})

const { t } = useI18n()
const { isMobile } = useMobileDetect()
const { sidebarFilterMobileDisplayStatus, open: openFilterMobileSidebar, close: closeFilterMobileSidebar } = useSidebarFilterMobile()

const filterExpanded = ref<boolean>(false)

const onSortByChange = (event, refine: Function) => {
  refine(event.target.value)
}

const onRefine = (item: RefinementListItem, refine: Function) => {
  refine(item.value)
}

const expandFilter = () => {
  filterExpanded.value = !filterExpanded.value
}
</script>

<template>
  <div class="c-base-filter-panel">
    <div class="mb-4">
      <BaseDivider
        variant="solid"
        class="border-secondary-light" />
      <div
        v-if="!isMobile"
        class="flex">
        <div
          class="flex items-center overflow-x-hidden flex-1 gap-y-3 py-4 relative"
          :class="[{
            'flex-wrap': filterExpanded
          }]">
          <div
            v-for="(toggleRefinement, index) in toggleRefinements"
            :key="index">
            <AisToggleRefinement :attribute="toggleRefinement.name">
              <template #default="{ value, refine }">
                <BaseFilterToggle
                  :is-refine="value.isRefined"
                  @click="refine(value)">
                  <template #label>
                    {{ toggleRefinement.label }}
                  </template>
                </BaseFilterToggle>
              </template>
            </AisToggleRefinement>
          </div>
          <div
            v-for="(rangeRefinement, index) in rangeRefinements"
            :key="index">
            <AisRangeInput
              :attribute="rangeRefinement.name"
              :precision="2">
              <template
                #default="{
                  currentRefinement,
                  range,
                  canRefine,
                  refine
                }">
                <BaseFilterRangeSlider
                  :current-range="[currentRefinement.min, currentRefinement.max]"
                  :min="range.min"
                  :max="range.max"
                  :step="1"
                  :disabled="!canRefine"
                  @end="refine({
                    min: $event[0],
                    max: $event[1]
                  })">
                  <template #label>
                    Preis
                  </template>
                </BaseFilterRangeSlider>
              </template>
            </AisRangeInput>
          </div>
          <div
            v-for="(filter, index) in filters"
            :key="index">
            <AisRefinementList
              :attribute="filter.name"
              show-more>
              <template #default="{ items, refine, canRefine, canToggleShowMore, createURL }">
                <BaseFilterSelect
                  v-if="canRefine"
                  class="mr-2"
                  :name="filter.label"
                  :attribute="filter.name"
                  :variant="filter.displayType"
                  :items="items"
                  :create-url="createURL"
                  @refine="onRefine($event, refine)">
                  <template #clear>
                    <AisClearRefinements
                      :id="'clear-refinement-' + filter.name"
                      :included-attributes="[filter.name]">
                      <template #default="{ canRefine, refine }">
                        <div
                          v-if="canRefine"
                          class="bg-black size-4 flex justify-center items-center rounded-full">
                          <BaseIcon
                            name="close"
                            class="size-2 text-white"
                            @click.prevent="refine" />
                        </div>
                      </template>
                    </AisClearRefinements>
                  </template>
                </BaseFilterSelect>
              </template>
            </AisRefinementList>
          </div>
          <div
            class="flex items-center"
            :class="[{
              'right-0 top-0 h-full pointer-events-none bg-white z-10 absolute shadow-[-20px_0px_10px_10px_rgb(255,255,255)]': !filterExpanded,
            }]">
            <BaseButton
              custom-class="rounded pointer-events-auto min-w-32"
              variant="primary"
              icon-position="right"
              icon="filter"
              icon-class="size-4"
              size="small"
              full-width
              @click="expandFilter">
              {{ filterExpanded ? 'weniger Filter' : 'mehr Filter' }}
            </BaseButton>
          </div>
        </div>
        <div class="border-l border-secondary-light pl-5 ml-5 py-4 flex items-center">
          <AisSortBy>
            <template #default="{ items, currentRefinement, refine }">
              <div class="bg-secondary-light px-3 py-1.5 border border-secondary-light rounded-md text-sm flex items-center gap-x-3">
                <select
                  class="flex-1 bg-transparent appearance-none outline-none cursor-pointer"
                  @change="onSortByChange($event, refine)">
                  <option
                    v-for="item in items"
                    :key="item.value"
                    :value="item.value"
                    :selected="currentRefinement === item.value">
                    {{ t('filter.sortBy.label', { label: item.label }) }}
                  </option>
                </select>
                <BaseIcon
                  name="chevron-thin"
                  class="size-2 text-secondary-dark rotate-90" />
              </div>
            </template>
          </AisSortBy>
        </div>
      </div>
      <div v-else>
        <div class="flex gap-x-5 my-5">
          <BaseButton
            class="flex-1"
            custom-class="rounded"
            variant="primary"
            icon-position="right"
            icon="filter"
            icon-class="size-4"
            size="small"
            full-width
            @click="openFilterMobileSidebar">
            Filter
          </BaseButton>
          <AisSortBy class="flex-1">
            <template #default="{ items, currentRefinement, refine }">
              <div class="bg-secondary-light px-3 py-1.5 border border-secondary-light rounded-md text-sm flex items-center gap-x-3">
                <select
                  class="flex-1 bg-transparent appearance-none outline-none cursor-pointer line-clamp-1"
                  @change="onSortByChange($event, refine)">
                  <option
                    v-for="item in items"
                    :key="item.value"
                    :value="item.value"
                    :selected="currentRefinement === item.value"
                    class="line-clamp-1">
                    {{ item.label }}
                  </option>
                </select>
                <BaseIcon
                  name="chevron-thin"
                  class="size-2 text-secondary-dark rotate-90" />
              </div>
            </template>
          </AisSortBy>
        </div>
      </div>
      <BaseDivider
        variant="solid"
        class="border-secondary-light" />
    </div>
    <Teleport to="#teleports">
      <AppSidebar
        class="filter-mobile-sidebar"
        position="center"
        :show="sidebarFilterMobileDisplayStatus"
        close-position="inside"
        @close-sidebar="closeFilterMobileSidebar">
        <template #default>
          <div class="px-3 py-6 h-full">
            <div class="flex flex-col h-full">
              <div class="flex flex-col">
                <BaseHeadline
                  title="Filter"
                  tag="div"
                  custom-class="font-semibold text-2xl" />
                <BaseDivider
                  variant="solid"
                  class="border-secondary-light mt-8" />
              </div>
              <div class="overflow-y-scroll h-full pt-6 pb-8">
                <div class="flex flex-col">
                  <div>
                    <AisCurrentRefinements id="all">
                      <template #default="{ items, createURL }">
                        <div
                          v-if="items.length"
                          class="flex items-center mb-3">
                          <div class="flex-1 font-semibold text-sm">
                            Gefiltert nach
                          </div>
                          <AisClearRefinements id="all">
                            <template #default="{ canRefine, refine, createURL }">
                              <a
                                v-if="canRefine"
                                :href="createURL()"
                                class="text-sm underline"
                                @click.prevent="refine">
                                Alle zurücksetzten
                              </a>
                            </template>
                          </AisClearRefinements>
                        </div>

                        <div class="flex flex-wrap gap-3">
                          <template
                            v-for="item in items"
                            :key="item.attribute">
                            <template
                              v-for="refinement in item.refinements"
                              :key="[
                                refinement.attribute,
                                refinement.type,
                                refinement.value,
                                refinement.operator
                              ].join(':')">
                              <a
                                :href="createURL(refinement)"
                                class="border border-secondary-light rounded-md px-3 py-1 text-xs flex items-center gap-x-1"
                                @click.prevent="item.refine(refinement)">
                                <span>{{ refinement.label }}</span>
                                <div class="rounded-full size-4 bg-black flex justify-center items-center">
                                  <BaseIcon
                                    name="close"
                                    class="size-2 text-white" />
                                </div>
                              </a>
                            </template>
                          </template>
                        </div>
                        <BaseDivider
                          v-if="items.length"
                          variant="solid"
                          class="border-secondary-light mt-10 mb-3" />
                      </template>
                    </AisCurrentRefinements>
                  </div>
                </div>
                <div
                  v-for="(toggleRefinement, index) in toggleRefinements"
                  :key="index">
                  <AisToggleRefinement :attribute="toggleRefinement.name">
                    <template #default="{ value, refine }">
                      <BaseFilterToggle
                        :is-refine="value.isRefined"
                        is-mobile
                        @click="refine(value)">
                        <template #label>
                          {{ toggleRefinement.label }}
                        </template>
                      </BaseFilterToggle>
                    </template>
                  </AisToggleRefinement>
                </div>
                <BaseDivider
                  variant="solid"
                  class="border-secondary-light mt-4" />
                <div
                  v-for="(rangeRefinement, index) in rangeRefinements"
                  :key="index">
                  <div class="flex flex-col mt-4">
                    <div class="font-semibold text-sm mb-3">
                      Preis
                    </div>
                    <AisRangeInput
                      :attribute="rangeRefinement.name"
                      :precision="2">
                      <template
                        #default="{
                          currentRefinement,
                          range,
                          canRefine,
                          refine
                        }">
                        <BaseFilterRangeSlider
                          :current-range="[currentRefinement.min, currentRefinement.max]"
                          :min="range.min"
                          :max="range.max"
                          :step="1"
                          :disabled="!canRefine"
                          @end="refine({
                            min: $event[0],
                            max: $event[1]
                          })">
                          <template #label>
                            Preis
                          </template>
                        </BaseFilterRangeSlider>
                      </template>
                    </AisRangeInput>
                  </div>
                </div>
                <BaseDivider
                  variant="solid"
                  class="border-secondary-light mt-4" />
                <div
                  v-for="(filter, index) in filters"
                  :key="index">
                  <AisRefinementList
                    :attribute="filter.name"
                    show-more>
                    <template #default="{ items, refine, canRefine, canToggleShowMore, createURL }">
                      <BaseFilterSelect
                        v-if="canRefine"
                        class="mr-2"
                        :name="filter.label"
                        :attribute="filter.name"
                        :variant="filter.displayType"
                        :items="items"
                        :create-url="createURL"
                        :is-mobile
                        @refine="onRefine($event, refine)">
                        <template #clear>
                          <AisClearRefinements
                            :id="'clear-refinement-' + filter.name"
                            :included-attributes="[filter.name]">
                            <template #default="{ canRefine, refine }">
                              <div
                                v-if="canRefine"
                                class="bg-black size-4 flex justify-center items-center rounded-full">
                                <BaseIcon
                                  name="close"
                                  class="size-2 text-white"
                                  @click.prevent="refine" />
                              </div>
                            </template>
                          </AisClearRefinements>
                        </template>
                      </BaseFilterSelect>
                    </template>
                  </AisRefinementList>
                </div>
              </div>
              <div class="mt-auto mb-0">
                <div class="flex flex-col pt-4">
                  <AisStats>
                    <template #default="{ hitsPerPage, nbPages, nbHits, page, processingTimeMS, query }">
                      <BaseButton
                        class="!w-full"
                        variant="secondary"
                        outline
                        @click="closeFilterMobileSidebar">
                        Zeige {{ nbHits }} Ergebnisse
                      </BaseButton>
                    </template>
                  </AisStats>
                  <button @click="closeFilterMobileSidebar">
                    Abbrechen
                  </button>
                </div>
              </div>
            </div>
          </div>
        </template>
      </AppSidebar>
    </Teleport>
  </div>
</template>

<style scoped>

</style>
