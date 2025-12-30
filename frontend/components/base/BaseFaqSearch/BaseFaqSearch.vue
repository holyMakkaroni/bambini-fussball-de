<script setup lang="ts">
import algoliasearch from 'algoliasearch'

defineProps({
  instanceKey: {
    type: String,
    required: true
  }
})

const { t } = useI18n()
const id = useId()
const searchOpen = ref<boolean>(false)
const searchQuery = ref<string>('')
const { public: config } = useRuntimeConfig()
const { websiteHandle } = useStoryblokHelper()

const client = algoliasearch(config.algolia.applicationId, config.algolia.apiKey, {
  responsesCache: useAisStatefulCache(),
  requestsCache: useAisStatefulCache()
})

const widgets = computed(() => [
  useAisAutocomplete({}),
  useAisConfigure({
    searchParameters: {
      hitsPerPage: 10,
      filters: `visibility:"${websiteHandle.value}"`
    }
  }),
  useAisHits({})
])

const configuration = ref({
  indexName: 'storyblok_helpcenter',
  searchClient: client
})

const onFocus = (state: boolean) => {
  searchOpen.value = state
}
</script>

<template>
  <div class="c-faq-search w-full transition-default">
    <AisInstantSearch
      :widgets
      :configuration
      :instance-key>
      <AisAutocomplete class="relative">
        <template #default="{ refine }">
          <div class="relative z-10">
            <FormInput
              :id="`${id}`"
              v-model="searchQuery"
              type="search"
              autofocus
              :with-icon="true"
              :class="{'active': searchOpen}"
              :placeholder="t('components.app.search.placeholder')"
              @is-focused="onFocus"
              @update:model-value="refine(searchQuery)" />
            <button
              type="submit"
              role="button"
              class="w-14 h-full bg-primary absolute top-0 right-0 flex justify-center items-center rounded-tr-md rounded-br-md"
              :title="t('components.app.search.label')">
              <BaseIcon
                name="search"
                class="size-5 text-white" />
            </button>
          </div>

          <div
            v-if="searchQuery.length > 0"
            class="w-full p-5 -mt-1 border border-secondary-light p-5 md:p-10">
            <div class="max-h-[900px] overflow-y-scroll">
              <div class="index-result">
                <AisHits>
                  <template #default="{ items }">
                    <ul>
                      <AppSearchGroupItem
                        v-for="item in items"
                        :key="item.objectID">
                        <template #default>
                          <NuxtLink
                            :to="`/${item.full_slug}`"
                            :title="item.name">
                            <AisHighlight
                              :hit="item"
                              attribute="name" />
                          </NuxtLink>
                        </template>
                      </AppSearchGroupItem>
                    </ul>
                  </template>
                </AisHits>
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
