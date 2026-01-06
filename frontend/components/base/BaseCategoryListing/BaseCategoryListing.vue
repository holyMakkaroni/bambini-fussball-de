<script setup lang="ts">
import algoliasearch from 'algoliasearch'
import { ref } from 'vue'
import type { ISbStoryData } from 'storyblok-js-client'
import type { AlgoliaItem } from '~/types/algolia'
import type { Schemas } from '#shopware'

const props = defineProps({
  category: {
    type: Object as PropType<Schemas['Category']>,
    required: true
  },
  dynamicFilters: {
    type: Array,
    default () {
      return []
    }
  }
})

const { path, query } = useRoute()
const { public: config } = useRuntimeConfig()
const { t, locale } = useI18n()
const { languageId, sessionContext } = useSessionContext()
const { filters } = useAlgoliaMapping(props.category)
const { getCategoryUrl } = useAlgoliaHelper()
const toggleRefinements = useStaticFilterSettings('toggle')
const rangeRefinements = useStaticFilterSettings('range')
const { getPrice } = useAlgoliaPriceHelper()
const { isPreview, version, websiteHandle } = useStoryblokHelper()

const seoPath = ref<string>(getCategoryUrl(path))

const storyTop = ref<ISbStoryData | []>([])
const storyBottom = ref<ISbStoryData | []>([])

const salesChannelId = computed(() => sessionContext.value?.salesChannel.id)
const categoryId = computed(() => props.category?.id)
const categoryName = computed(() => props.category?.translated?.name ?? '')
const categoryMainId = computed(() => {
  const parts = props.category.path?.split('|').filter(Boolean) || []
  return parts[1] || categoryId.value
})

const indexPrefix = `${salesChannelId.value}_${languageId.value}`
const { algoliaListingRouter } = useAlgoliaCategoryRouting(indexPrefix, seoPath.value)

const { apiClient } = useShopwareContext()

const transformShopwareCategoryToNavItem = (category: Schemas['Category']): BaseSidebarNavigationItem => {
  const getUrl = (cat: Schemas['Category']): string => {
    if (cat.seoUrls && cat.seoUrls.length > 0) {
      return `/${cat.seoUrls[0].seoPathInfo}`
    }
    // Fallback URL generation
    return '#'
  }

  return {
    id: category.id,
    parent_id: category.parentId,
    name: category.translated?.name || category.name || '',
    url: getUrl(category),
    children: category.children?.map(child => transformShopwareCategoryToNavItem(child)) || []
  }
}

const { data: sidebarNav } = await useLazyAsyncData(
  'categoryPage-side-navigation' + categoryMainId.value,
  async () => {
    const { data } = await apiClient.invoke('readCategoryList post /category', {
      headers: {
        'sw-include-seo-urls': true
      },
      body: {
        filter: [
          {
            type: 'equals',
            field: 'id',
            value: categoryMainId.value
          }
        ],
        associations: {
          children: {
            associations: {
              children: {}
            }
          }
        }
      }
    })

    const categories = data.elements || []

    return categories.map((category: Schemas['Category']) =>
      transformShopwareCategoryToNavItem(category)
    )
  }
)

const { data: story } = await useCachedAsyncData(path, async () => {
  return unref(await useAsyncStoryblok(`${websiteHandle.value}/category/${seoPath.value}`, {
    version: isPreview(query) ? 'draft' : version.value,
    resolve_links: 'url',
    language: locale.value
  }))
})

storyTop.value = story.value?.content?.body_top ?? []
storyBottom.value = story.value?.content?.body_bottom ?? []

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
      hitsPerPage: 50,
      page: 0,
      filters: `categoriesRo.id: "${categoryId.value}"`,
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
  useAisClearRefinements(
    {},
    'all'
  ),
  useAisCurrentRefinements({
    excludedAttributes: ['defaultPricing.gross', 'defaultPricing.inSale']
  }, 'all'),
  useAisSortBy({
    items: sortBy
  }),
  useAisPagination({}),
  ...refinementsList,
  ...toggleRefinementsList,
  ...rangeRefinementsList
])

const client = algoliasearch(config.algolia.applicationId, config.algolia.apiKey, {})

const configuration = ref({
  indexName: `${indexPrefix}_product`,
  searchClient: client,
  routing: algoliaListingRouter.value,
  future: {
    preserveSharedStateOnUnmount: false
  }
})

const onPaginationChange = (page: number, refine: Function) => {
  refine(page - 1)
}
</script>

<template>
  <div class="c-base-category-listing">
    {{ filters }}
    <AisInstantSearch
      :widgets
      :configuration
      :instance-key="`category-listing-page-${category.id}`">
      <AppContentSidebar>
        <template #default>
          <BaseHeadline
            tag="h1"
            :title="categoryName"
            custom-class="my-6 md:my-12" />
        </template>
      </AppContentSidebar>
      <AppContentSidebar>
        <template #sidebar>
          <BaseSidebarNavigation
            v-if="sidebarNav"
            :items="sidebarNav" />
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
          <div class="col-span-12 flex justify-center mt-8 md:mt-16">
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
        </template>
      </AppContentSidebar>
    </AisInstantSearch>
  </div>
</template>
