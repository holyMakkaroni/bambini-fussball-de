<script setup lang="ts">
import { computed } from 'vue'
import type { ComputedRef } from 'vue'
import { getListingFilters } from '@shopware-pwa/helpers-next'
import type { Schemas } from '#shopware'
import type { PropertyMapping } from '~/types/shopware'
import type { BaseSidebarNavigationItem } from '~/types/components/base'

const props = defineProps({
  category: {
    type: Object,
    required: true
  }
})

const router = useRouter()
const { apiClient } = useShopwareContext()
const { websiteHandle } = useStoryblokHelper()
const { t, locale } = useI18n()
const propertyMapping = useState<PropertyMapping>('propertyMapping')
const { query, path } = useRoute()
const { getAllSelectedOptionIds, generateSeoUrl, removeSeoFilterSegments } = useSeoFilterUrl(propertyMapping, path)

const defaultSearchCriteria : ComputedRef<Schemas['ProductListingCriteria'] & Schemas['ProductListingFlags']> = computed(() => ({
  includes: {
    product: ['name', 'id', 'translated', 'cover', 'calculatedPrice', 'seoUrls']
  }
}))

const selectedPropertyIds = computed(() => {
  return getAllSelectedOptionIds()
})

const searchCriteriaForRequest: ComputedRef<Schemas['ProductListingCriteria'] & Schemas['ProductListingFlags']> = computed(() => ({
  properties: selectedPropertyIds.value.join('|'),
  limit: 100
}))

const changePage = async (page: number) => {
  const seoUrl = generateSeoUrl(propertyMapping.value?.properties)
  const url = [removeSeoFilterSegments(path), seoUrl].filter(Boolean).join('/')

  await router.push({
    path: url,
    query: {
      p: page
    }
  })

  await refresh()
}

const { data, refresh } = await useAsyncData(
  `category-${props.category.id}-${query.p ? 'p' + query.p : 'p1'}-${locale.value}-${selectedPropertyIds.value.join('-')}`,
  async () => {
    const { data } = await apiClient.invoke('readProductListing post /product-listing/{categoryId}', {
      headers: {
        'sw-include-seo-urls': true
      },
      pathParams: {
        categoryId: props.category.id
      },
      body: {
        ...defaultSearchCriteria.value,
        ...searchCriteriaForRequest.value,
        ...query
      }
    })

    return data
  },
  {
    // TODO: Remove if bug https://github.com/nuxt/nuxt/issues/18405 is fixed in Vue 3.5
    getCachedData (key, nuxt) {
      return nuxt.payload.data[key] || nuxt.static.data[key]
    }
  })

const { isPreview, version } = useStoryblokHelper()

const seoPath = Array.isArray(props.category?.seoUrls)
  ? props.category.seoUrls[0]?.seoPathInfo
  : ''

const story = await useAsyncStoryblok(`${websiteHandle.value}/category/${seoPath}`, {
  version: isPreview(query) ? 'draft' : version.value,
  resolve_links: 'url',
  language: locale.value
})

const products = computed(() => data.value?.elements || [])
const limit = computed(() => data.value?.limit || 10)
const currentPage = computed(() => data.value?.page || 1)
const totalProducts = computed(() => data.value?.total || 0)
const totalPages = computed(() => Math.ceil(totalProducts.value / limit.value))
const filters = computed(() => getListingFilters(data.value?.aggregations))
const categoryName = computed(() => props.category.translated.name)
const storyTop = computed(() => story.value?.content.body_top ?? [])
const storyBottom = computed(() => story.value?.content.body_bottom ?? [])
const categoryRootId = computed(() => {
  if (!props.category.path) {
    return null
  }

  const categoryPath = props.category.path.split('|').filter((item: string) => item !== '')
  return categoryPath.length === 1 ? categoryPath[0] : categoryPath[1]
})

const { data: categories } = await useAsyncData<BaseSidebarNavigationItem[]>(
  `category-navigation-${categoryRootId.value}-${locale.value}`,
  async () => {
    const { data } = await apiClient.invoke('readNavigation post /navigation/{activeId}/{rootId}', {
      headers: {
        'sw-include-seo-urls': true
      },
      pathParams: {
        activeId: props.category.id,
        rootId: categoryRootId.value
      },
      body: {
        depth: 4
      }
    })

    return data
  },
  {
    transform: (categories) => {
      const transformCategory = (category: Schemas['Category']) => {
        return {
          id: category.id,
          parent_id: category.parentId,
          name: category.translated.name,
          url: category?.seoUrls?.[0]?.seoPathInfo ? `/${category.seoUrls[0].seoPathInfo}` : '#',
          children: category.children ? category.children.map(transformCategory) : []
        }
      }

      return categories.map(transformCategory)
    }
  })
</script>

<template>
  <div class="type-category-listing">
    <AppContentSidebar>
      <template #default>
        <BaseHeadline
          tag="h1"
          :title="categoryName"
          custom-class="my-6 md:my-12" />

        <div v-if="storyTop.length">
          <component
            :is="body.component"
            v-for="body in storyTop"
            :key="body._uid"
            :blok="body" />
        </div>
      </template>
    </AppContentSidebar>
    <AppContentSidebar>
      <template #sidebar>
        <BaseSidebarNavigation
          v-if="categories?.length"
          :items="categories" />
      </template>
      <template #default>
        <BaseDivider
          variant="solid"
          class="border-secondary-light" />
        <div
          v-if="filters.length"
          class="py-3 md:py-5 flex">
          <div
            v-if="products.length"
            class="flex-1">
            <div class="flex flex-wrap gap-3">
              <ShopwareListingFilter
                v-for="(filter, index) in filters"
                :key="`${filter?.id || filter?.code}`"
                :filter="filter"
                :index="index" />
            </div>
          </div>
          <div>Sortierung</div>
        </div>
        <BaseDivider
          variant="solid"
          class="border-secondary-light" />
        <div
          v-if="products.length"
          class="col-span-12 mt-6 md:mt-12 grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 lg:gap-5">
          <BaseProductBox
            v-for="(product, index) in products"
            :key="product.id"
            :lazy="index > 11"
            :decoding="index > 11 ? 'async' : 'sync'"
            :product="product" />
        </div>
        <div
          v-else
          class="col-span-12">
          {{ t('components.shopware.listing.noProducts') }}
        </div>
        <div class="col-span-12 flex justify-center mt-8 md:mt-16">
          <BasePagination
            :total="totalPages"
            :length="3"
            :current="query.p ? Number(query.p) : Number(currentPage)"
            @change-page="changePage" />
        </div>
        <div v-if="storyBottom.length">
          <component
            :is="body.component"
            v-for="body in storyBottom"
            :key="body._uid"
            :blok="body" />
        </div>
      </template>
    </AppContentSidebar>
  </div>
</template>
