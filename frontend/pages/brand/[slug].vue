<script setup lang="ts">
import algoliasearch from 'algoliasearch'
import { useBreadcrumbStore } from '~/stores/breadcrumb'
import type { AlgoliaItem } from '~/types/algolia'

const { query, path, params } = useRoute()
const { t, locale } = useI18n()
const { breadcrumbs } = useBreadcrumbHelper(path)
const { setBreadcrumb } = useBreadcrumbStore()
const { public: config } = useRuntimeConfig()
const { languageId, sessionContext } = useSessionContext()
const { getPrice } = useAlgoliaPriceHelper()
const { filters } = useAlgoliaMapping()
const toggleRefinements = useStaticFilterSettings('toggle')
const rangeRefinements = useStaticFilterSettings('range')
const { isPreview, version } = useStoryblokHelper()

setBreadcrumb(breadcrumbs)

const slug = typeof params.slug === 'string' ? params.slug : ''
const filtersManufacturer = computed(() => `manufacturer:"${slug}"`)
const salesChannelId = computed(() => sessionContext.value?.salesChannel.id)

const client = algoliasearch(config.algolia.applicationId, config.algolia.apiKey, {
  responsesCache: useAisStatefulCache(),
  requestsCache: useAisStatefulCache()
})
const indexPrefix = `${salesChannelId.value}_${languageId.value}`

const configuration = ref({
  indexName: `${indexPrefix}_product`,
  searchClient: client,
  future: {
    preserveSharedStateOnUnmount: true
  }
})

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

const refinementsList = Object.entries(filters.value).flatMap(([key, attribute]) => [
  useAisRefinementList({
    attribute: key
  }),
  useAisClearRefinements({
    includedAttributes: [key]
  }, 'clear-refinement-' + key)
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
      hitsPerPage: 20,
      page: 0,
      filters: filtersManufacturer.value,
      facetingAfterDistinct: true
    }
  }),
  useAisHierarchicalMenu({
    attributes: [
      'hierarchicalCategories.lvl0',
      'hierarchicalCategories.lvl1',
      'hierarchicalCategories.lvl2',
      'hierarchicalCategories.lvl3',
      'hierarchicalCategories.lvl4'
    ]
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
  useAisSortBy({
    items: sortBy
  }),
  useAisPagination({}),
  ...refinementsList,
  ...toggleRefinementsList,
  ...rangeRefinementsList
])

const onPaginationChange = (page: number, refine: Function) => {
  refine(page - 1)
}

const story = await useAsyncStoryblok(`hersteller/${slug}`, {
  version: isPreview(query) ? 'draft' : version.value,
  resolve_links: 'url',
  language: locale.value
})

const storyAbove = computed(() => story.value?.content?.body_above)
const storyBottom = computed(() => story.value?.content?.body_bottom)
</script>

<template>
  <div class="type-brand-index">
    <AppBreadcrumb />

    <AppContentSidebar class="my-6 md:my-12">
      <template #default>
        <component
          :is="body.component"
          v-for="body in storyAbove"
          :key="body._uid"
          :blok="body" />
      </template>
    </AppContentSidebar>

    <AisInstantSearch
      :widgets
      :configuration
      :instance-key="`brand-result-page-${slug}`">
      <AppContentSidebar>
        <template #sidebar>
          <AisHierarchicalMenu attribute="hierarchicalCategories.lvl0">
            <template
              #default="{
                items,
                canToggleShowMore,
                isShowingMore,
                refine,
                toggleShowMore,
                createURL,
                sendEvent,
              }">
              <BaseHierarchicalMenu
                :items="items"
                :create-url="createURL"
                :refine="refine" />
            </template>
          </AisHierarchicalMenu>
        </template>
        <template #default>
          <BaseFilterPanel
            :toggle-refinements="toggleRefinements"
            :range-refinements="rangeRefinements"
            :filters="filters" />

          <AisHits>
            <template #default="{ items }">
              <div class="col-span-12 mt-6 md:mt-12 grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 lg:gap-5">
                <BaseProductBoxNew
                  v-for="item in items"
                  :key="item.objectID"
                  :title="item.name"
                  :rating-average="item.ratingAverage"
                  :image-path="item?.image?.path"
                  :delivery-time="item?.deliveryTime"
                  :price="item.price"
                  :list-price="item.listPrice"
                  :percentage="item.percentage"
                  :regulation-price="item.regulationPrice"
                  lazy
                  :product-id="item.id"
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

          <component
            :is="body.component"
            v-for="body in storyBottom"
            :key="body._uid"
            :blok="body" />
        </template>
      </AppContentSidebar>
    </AisInstantSearch>
  </div>
</template>

<style scoped>

</style>
