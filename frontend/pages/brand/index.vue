<script setup lang="ts">
import { useBreadcrumbStore } from '~/stores/breadcrumb'
import BaseLexicon from '~/components/base/BaseLexicon/BaseLexicon.vue'
import BaseLexiconItem from '~/components/base/BaseLexicon/BaseLexiconItem.vue'

const { query } = useRoute()
const { t, locale } = useI18n()
const { breadcrumbs } = useBreadcrumbHelper()
const { setBreadcrumb } = useBreadcrumbStore()
const { fetchManufacturers, formatManufacturersLexicon, fetchAlgoliaManufacturers } = useManufacturer()
const { isPreview, version } = useStoryblokHelper()

setBreadcrumb(breadcrumbs)

const manufacturers = await fetchManufacturers()

const story = await useAsyncStoryblok('hersteller', {
  version: isPreview(query) ? 'draft' : version.value,
  resolve_links: 'url',
  language: locale.value
})

const active = ref<string>('all')
const groupedManufacturers = computed(() => formatManufacturersLexicon(manufacturers))

const filteredManufacturers = computed(() => {
  if (active.value === 'all') {
    return groupedManufacturers.value
  }

  return Object.keys(groupedManufacturers.value)
    .filter(key => key.startsWith(active.value))
    .reduce((acc, key) => {
      acc[key] = groupedManufacturers.value[key]
      return acc
    }, {} as Record<string, typeof groupedManufacturers.value[string]>)
})

const countManufacturers = computed(() => active.value === 'all' ? manufacturers.length : filteredManufacturers.value[active.value].brands.length)
</script>

<template>
  <div class="type-brand-index">
    <AppBreadcrumb />

    <div class="container mt-12">
      <div class="flex flex-col">
        <div class="text-4xl font-semibold">
          {{ t('pages.brand.index.headline') }}
        </div>
        <div class="font-semibold mt-4">
          {{ t('pages.brand.index.description', {
            countBrands: countManufacturers
          }) }}
        </div>
      </div>

      <nav class="flex justify-center xl:justify-between gap-3 mt-7 flex-wrap">
        <button
          type="button"
          class="flex-shrink-0 py-1.5 px-3 hover:bg-primary hover:text-white"
          :class="{ 'bg-primary text-white': active === 'all', 'bg-secondary-light' : active !== 'all' }"
          @click="active = 'all'">
          {{ t('pages.brand.index.all') }}
        </button>
        <button
          v-for="(letter, index) in Object.keys(groupedManufacturers)"
          :key="index"
          type="button"
          class="flex-shrink-0 py-1.5 px-3 hover:bg-primary hover:text-white"
          :class="{ 'bg-primary text-white': active === letter, 'bg-secondary-light' : active !== letter }"
          @click="active = letter">
          {{ letter }}
        </button>
      </nav>

      <BaseDivider
        variant="solid"
        class="mt-15" />
    </div>

    <div
      v-if="story"
      class="mt-10">
      <StoryblokComponent :blok="story.content" />
    </div>

    <div class="container">
      <BaseLexicon>
        <BaseLexiconItem
          v-for="(group, letter, index) in filteredManufacturers"
          :key="index"
          :title="letter as string"
          :manufacturer-lexicon="group" />
      </BaseLexicon>
    </div>
  </div>
</template>

<style scoped>

</style>
