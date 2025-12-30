<script setup lang="ts">
import algoliasearch from 'algoliasearch'
import type { RefinementListItem } from 'instantsearch.js/es/connectors/refinement-list/connectRefinementList'
import type { AlgoliaItem } from '~/types/algolia'
import BaseProductBoxNew from '~/components/base/BaseProductBoxNew/BaseProductBoxNew.vue'

const { public: config } = useRuntimeConfig()
const route = useRoute()
const { t } = useI18n()
const localePath = useLocalePath()
const { languageId, sessionContext } = useSessionContext()
const allParams = route.params.all || []
const slug = allParams.length > 0 ? allParams[0] : ''
const { filters } = useAlgoliaMapping()
const toggleRefinements = useStaticFilterSettings('toggle')
const rangeRefinements = useStaticFilterSettings('range')
const salesChannelId = computed(() => sessionContext.value?.salesChannel.id)
const { getPrice } = useAlgoliaPriceHelper()

useHead({
  title: t('pages.search.headline', {
    query: slug
  }),
  meta: [
    {
      name: 'robots',
      content: 'noindex'
    }
  ]
})

const client = algoliasearch(config.algolia.applicationId, config.algolia.apiKey, {
  responsesCache: useAisStatefulCache(),
  requestsCache: useAisStatefulCache()
})

const indexPrefix = `${salesChannelId.value}_${languageId.value}`
const { algoliaSearchRouter } = useAlgoliaSearchRouting(indexPrefix)

const sortBy = [
  {
    value: `${indexPrefix}_product`,
    label: t('filter.sortBy.popularity.desc')
  },
  {
    value: `${indexPrefix}_product_sales_asc`,
    label: t('filter.sortBy.popularity.asc')
  },
  {
    value: `${indexPrefix}_product_ratingAverage_desc`,
    label: t('filter.sortBy.rating.desc')
  },
  {
    value: `${indexPrefix}_product_ratingAverage_asc`,
    label: t('filter.sortBy.rating.asc')
  }
]

const indexProductSuggestions = useAisIndex({
  indexName: `${indexPrefix}_product_query_suggestions`
})

indexProductSuggestions.addWidgets([
  useAisConfigure({
    searchParameters: {
      hitsPerPage: 10,
      page: 0
    }
  }),
  useAisHits({})
])

const refinementsList = Object.entries(filters.value).flatMap(([key, attribute]) => [
  useAisRefinementList({
    attribute: key
  }, 'search-result-refinement-list-' + key),
  useAisClearRefinements({
    includedAttributes: [key]
  }, key)
])

const toggleRefinementsList = toggleRefinements.map((toggleRefinement) => {
  return useAisToggleRefinement({
    attribute: toggleRefinement.name
  }, toggleRefinement.name)
})

const rangeRefinementsList = rangeRefinements.map((rangeRefinement) => {
  return useAisRangeInput({
    attribute: rangeRefinement.name
  }, rangeRefinement.name)
})

const widgets = computed(() => [
  useAisConfigure({
    searchParameters: {
      query: slug,
      hitsPerPage: 20,
      page: 0,
      facetingAfterDistinct: true
    }
  }),
  useAisHits({
    transformItems: (items: AlgoliaItem[]) =>
      items.map((item) => {
        const price = getPrice(item)
        return {
          ...item,
          price: price.price,
          listPrice: price?.listPrice,
          percentage: price?.percentage,
          regulationPrice: price?.regulationPrice
        }
      })
  }),
  useAisStats({}),
  useAisSortBy({
    items: sortBy
  }),
  useAisPagination({}),
  ...refinementsList,
  ...toggleRefinementsList,
  ...rangeRefinementsList,
  indexProductSuggestions
])

const configuration = ref({
  indexName: `${indexPrefix}_product`,
  searchClient: client,
  routing: algoliaSearchRouter.value,
  future: {
    preserveSharedStateOnUnmount: true
  }
})

const onRefine = (item: RefinementListItem, refine: Function) => {
  refine(item.value)
}

const onPaginationChange = (page: number, refine: Function) => {
  refine(page - 1)
}

const onSortByChange = (event, refine: Function) => {
  refine(event.target.value)
}
</script>

<template>
  <div class="type-search-index">
    <AisInstantSearch
      :widgets
      :configuration
      instance-key="search-result-page">
      <AppContentSidebar class="my-6 md:my-12">
        <template #default>
          <AisStats>
            <template #default="{ nbHits, processingTimeMS, query }">
              <div class="flex flex-wrap items-end gap-x-5">
                <div class="flex text-4xl font-semibold">
                  <div>„</div>
                  <div>{{ query }}</div>
                  <div>“</div>
                </div>
                <div class="text-2xl mb-[2px]">
                  {{ t('pages.search.result', nbHits, { hits: nbHits }) }}
                </div>
                <div class="text-xs mb-[6px]">
                  {{ t('pages.search.time', { time: processingTimeMS / 1000 }) }}
                </div>
              </div>
            </template>
          </AisStats>
        </template>
      </AppContentSidebar>
      <AppContentSidebar>
        <template #sidebar>
          <AisIndex :index="`${indexPrefix}_product_query_suggestions`">
            <AisHits>
              <template #default="{ items }">
                <div
                  v-if="items.length > 0"
                  class="flex flex-col">
                  <div class="text-sm font-light mb-5">
                    {{ t('components.app.search.indices.suggestions') }}
                  </div>
                  <BaseSidebarNavigation
                    :items="items.map((link, index) => {
                      return {
                        id: index,
                        parent_id: null,
                        name: link.query,
                        url: localePath({
                          name: 'search-all',
                          params: {
                            all: link.query
                          }
                        }),
                        children: []
                      }
                    })" />
                </div>
              </template>
            </AisHits>
          </AisIndex>
        </template>
        <template #default>
          <div class="mb-4">
            <BaseDivider
              variant="solid"
              class="border-secondary-light" />
            <div class="flex py-3 md:py-5">
              <div class="flex flex-wrap flex-1 gap-y-3">
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
                    <template #default="{ items, refine, canRefine}">
                      <BaseFilterSelect
                        v-if="canRefine"
                        class="mr-4"
                        :name="filter.label"
                        :attribute="filter.name"
                        :variant="filter.displayType"
                        :items="items"
                        @refine="onRefine($event, refine)">
                        <template #clear>
                          <AisClearRefinements
                            :id="filter.name"
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
              <div class="border-l border-secondary-light pl-5 ml-5">
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
            <BaseDivider
              variant="solid"
              class="border-secondary-light" />
          </div>
          <AisIndex :index="`${indexPrefix}_product`">
            <AisHits>
              <template #default="{ items }">
                <div class="col-span-12 mt-6 md:mt-12 grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 lg:gap-5">
                  <BaseProductBoxNew
                    v-for="item in items"
                    :key="item.objectID"
                    :product-id="item.id"
                    :title="item.name"
                    :rating-average="item.ratingAverage"
                    :image-path="item?.image?.path"
                    :delivery-time="item?.deliveryTime"
                    :price="item.price"
                    :list-price="item.listPrice"
                    :percentage="item.percentage"
                    :regulation-price="item.regulationPrice"
                    lazy
                    :url="`/${item.url}`" />
                </div>
              </template>
            </AisHits>
            <div class="flex justify-center mt-8 md:mt-16">
              <AisPagination
                :show-first="true"
                :show-previous="true"
                :show-next="true"
                :show-last="true">
                <template
                  #default="{
                    currentRefinement,
                    nbPages,
                    refine
                  }">
                  <BasePagination
                    :total="nbPages"
                    :length="3"
                    :current="currentRefinement + 1"
                    @change-page="onPaginationChange($event, refine)" />
                </template>
              </AisPagination>
            </div>
          </AisIndex>
        </template>
      </AppContentSidebar>
    </AisInstantSearch>
  </div>
</template>

<style scoped>

</style>
