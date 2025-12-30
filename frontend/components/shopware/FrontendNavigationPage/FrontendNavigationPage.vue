<script setup lang="ts">
const props = defineProps({
  navigationId: {
    type: String,
    required: true
  }
})

const { apiClient } = useShopwareContext()

const { data: category } = await useLazyAsyncData(
  'categoryPage' + props.navigationId,
  async () => {
    const { data } = await apiClient.invoke('readCategory post /category/{navigationId}', {
      headers: {
        'sw-include-seo-urls': true
      },
      pathParams: {
        navigationId: props.navigationId
      },
      body: {
        includes: {
          category: ['name', 'id', 'translated', 'seoUrls', 'parentId', 'path', 'cmsPage']
        }
      }
    })

    return data
  }
)

const categoryName = computed(() => {
  return category.value?.translated?.name ?? ''
})

useHead({
  title: categoryName
})
</script>

<template>
  <AppBreadcrumb />

  <BaseCategoryListing
    v-if="category"
    :category="category" />
</template>

<style scoped>

</style>
