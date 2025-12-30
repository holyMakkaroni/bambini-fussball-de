<script setup lang="ts">
import algoliasearch from 'algoliasearch'
import { useLocalStorage } from '@vueuse/core'
import AppSearchGroupItem from '~/components/app/AppSearch/AppSearchGroupItem.vue'

defineProps({
  instanceKey: {
    type: String,
    required: true
  }
})

const route = useRoute()
const { t } = useI18n()
const id = useId()
const searchOpen = useState<boolean>('searchOpen', () => false)
const searchQuery = ref<string>('')
const recentSearches = useLocalStorage<string[]>('recentSearches', [])
const { public: config } = useRuntimeConfig()
const { languageId, sessionContext } = useSessionContext()
const localePath = useLocalePath()

const salesChannelId = computed(() => sessionContext.value?.salesChannel.id)

const client = algoliasearch(config.algolia.applicationId, config.algolia.apiKey, {
  responsesCache: useAisStatefulCache(),
  requestsCache: useAisStatefulCache()
})

const indexProductSuggestions = useAisIndex({
  indexName: `${salesChannelId.value}_${languageId.value}_product_query_suggestions`
})

indexProductSuggestions.addWidgets([useAisHits({})])

const indexCategory = useAisIndex({
  indexName: `${salesChannelId.value}_${languageId.value}_category`
})
indexCategory.addWidgets([useAisHits({})])

const widgets = computed(() => [
  indexProductSuggestions,
  indexCategory,
  useAisAutocomplete({}),
  useAisConfigure({
    searchParameters: {
      hitsPerPage: 10
    }
  }),
  useAisHits({})
])

const configuration = ref({
  indexName: `${salesChannelId.value}_${languageId.value}_product`,
  searchClient: client
})

function addRecentSearchTerm (searchTerm: string) {
  if (!searchTerm.length) { return }

  const normalizedTerm = searchTerm.toLowerCase()

  if (!recentSearches.value.some(term => term.toLowerCase() === normalizedTerm)) {
    recentSearches.value = [searchTerm, ...recentSearches.value]

    if (recentSearches.value.length > 10) {
      recentSearches.value = recentSearches.value.slice(0, 10)
    }
  }
}

const handleSearch = () => {
  navigateTo(localePath({
    name: 'search-all',
    params: {
      all: searchQuery.value
    }
  }))

  addRecentSearchTerm(searchQuery.value)

  searchQuery.value = ''
  searchOpen.value = false
}

const removeRecentSearch = (search: string) => {
  recentSearches.value = recentSearches.value.filter(item => item !== search)
}

const onFocus = (state: boolean) => {
  searchOpen.value = state
}

watch(() => route.path, () => {
  searchOpen.value = false
},
{
  immediate: true
})
</script>

<template>
  <div
    class="c-app-search w-full transition-default md:relative z-20"
    :class="{'absolute left-0': searchOpen}">
    <Transition name="fade">
      <BaseOverlay
        v-if="searchOpen"
        :show-loading-indicator="false"
        @click="searchOpen = false" />
    </Transition>
    <AisInstantSearch
      :widgets
      :configuration
      :instance-key
      class="relative z-[60]">
      <AisAutocomplete class="relative">
        <template #default="{ refine }">
          <form
            class="relative z-10"
            @submit.prevent="handleSearch()">
            <FormInput
              :id="`${id}`"
              v-model="searchQuery"
              type="search"
              autofocus
              :with-icon="true"
              autocomplete="off"
              :class="{'active': searchOpen}"
              :placeholder="t('components.app.search.placeholder')"
              @is-focused="onFocus"
              @update:model-value="refine(searchQuery)" />
            <button
              type="submit"
              role="button"
              class="w-14 h-full bg-primary absolute top-0 right-0 flex justify-center items-center rounded-tr-md rounded-br-md group"
              :title="t('components.app.search.label')">
              <BaseIcon
                name="search"
                class="size-5 text-white transition-default group-hover:scale-105" />
            </button>
          </form>

          <div
            v-if="searchOpen"
            class="absolute w-full bg-white rounded-bl-md rounded-br-md -mt-1">
            <div class="max-h-[230px] [@media(min-height:595px)]:max-h-[450px] [@media(min-height:945px)]:max-h-[900px] overflow-y-scroll">
              <div class="p-5">
                <AppSearchGroup
                  v-if="recentSearches.length > 0 && searchQuery.length === 0"
                  :headline="t('components.app.search.indices.recentSearches')"
                  show-divider>
                  <AppSearchGroupItem
                    v-for="(query, index) in recentSearches"
                    :key="index">
                    <template #default>
                      <NuxtLink
                        :to="localePath({
                          name: 'search-all',
                          params: {
                            all: query
                          }
                        })">
                        {{ query }}
                      </NuxtLink>
                    </template>
                    <template #icon>
                      <button
                        type="button"
                        @click="removeRecentSearch(query)">
                        <BaseIcon
                          name="close"
                          class="size-3 text-gray-400 hover:text-gray-500" />
                      </button>
                    </template>
                  </AppSearchGroupItem>
                </AppSearchGroup>

                <div class="index-result">
                  <AisIndex
                    v-if="searchQuery.length"
                    :index="`${salesChannelId}_${languageId}_product_query_suggestions`">
                    <AisHits>
                      <template #default="{ items }">
                        <AppSearchGroup
                          v-if="items.length > 0"
                          :headline="t('components.app.search.indices.suggestions')">
                          <NuxtLink
                            v-for="item in items"
                            :key="item.objectID"
                            :to="localePath({
                              name: 'search-all',
                              params: {
                                all: item.query
                              }
                            })">
                            <AppSearchGroupItem>
                              <template #default>
                                <AisHighlight
                                  :hit="item"
                                  attribute="query" />
                              </template>
                            </AppSearchGroupItem>
                          </NuxtLink>
                        </AppSearchGroup>
                      </template>
                    </AisHits>
                  </AisIndex>

                  <AisIndex :index="`${salesChannelId}_${languageId}_product`">
                    <AisHits>
                      <template #default="{ items }">
                        <AppSearchGroup
                          v-if="items.length > 0"
                          :headline="t('components.app.search.indices.products')">
                          <AppSearchGroupItem
                            v-for="item in items"
                            :key="item.objectID">
                            <template #default>
                              <NuxtLink
                                :to="`/${item.url}`"
                                :title="item.name">
                                <AisHighlight
                                  :hit="item"
                                  attribute="name" />
                              </NuxtLink>
                            </template>
                          </AppSearchGroupItem>
                        </AppSearchGroup>
                      </template>
                    </AisHits>
                  </AisIndex>

                  <AisIndex :index="`${salesChannelId}_${languageId}_category`">
                    <AisHits>
                      <template #default="{ items }">
                        <AppSearchGroup
                          v-if="items.length > 0"
                          :headline="t('components.app.search.indices.categories')"
                          :show-divider="false">
                          <AppSearchGroupItem
                            v-for="item in items"
                            :key="item.objectID">
                            <template #default>
                              <NuxtLink :to="`/${item.url}`">
                                <AisHighlight
                                  :hit="item"
                                  attribute="name" />
                              </NuxtLink>
                            </template>
                          </AppSearchGroupItem>
                        </AppSearchGroup>
                      </template>
                    </AisHits>
                  </AisIndex>
                </div>
              </div>
            </div>
          </div>
        </template>
      </AisAutocomplete>
    </AisInstantSearch>
  </div>
</template>

<style scoped>

</style>
