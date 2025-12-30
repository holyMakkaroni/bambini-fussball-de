<script setup lang="ts">
import { getCategoryUrl } from '@shopware-pwa/helpers-next'

const props = defineProps({
  blok: {
    type: Object,
    default: null
  },
  container: {
    type: Boolean,
    default: false
  }
})

const { apiClient } = useShopwareContext()
const { locale } = useI18n()

const categoryIds = computed(() =>
  props.blok.categories.items?.filter((item: { id: string, type: string }) => item.type === 'category').map((item: { id: string, type: string }) => item.id) || []
)

const { data: categories } = await useLazyAsyncData(
  `categories-by-ids-${categoryIds.value.join('-')}-${locale.value}`,
  async () => {
    const { data } = await apiClient.invoke('readCategoryList post /category', {
      headers: {
        'sw-include-seo-urls': true
      },
      body: {
        ids: categoryIds.value
      }
    })

    return data.elements || []
  })
</script>

<template>
  <BaseSectionWrapper
    v-if="categories?.length"
    v-editable="blok"
    identifier="c-popular-categories"
    :title="blok.title"
    :link="blok.link"
    :container="container"
    :label="blok.label">
    <div class="flex flex-wrap gap-4">
      <NuxtLink
        v-for="category in categories"
        :key="category.id"
        :to="getCategoryUrl(category)"
        :title="category.translated.name"
        class="bg-secondary-light text-sm rounded-md px-3 py-1 text-nowrap">
        {{ category.translated.name }}
      </NuxtLink>
    </div>
  </BaseSectionWrapper>
</template>
