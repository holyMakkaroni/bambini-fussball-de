<script setup lang="ts">
import { useBreadcrumbStore } from '~/stores/breadcrumb'

const props = defineProps({
  blok: {
    type: Object,
    default: null
  }
})

useHead({
  title: props.blok.seo?.title || 'LivingActive',
  meta: [
    {
      name: 'description',
      content: props.blok.seo?.description || 'Meta description goes here'
    }
  ]
})

const route = useRoute()
const storyblokApi = useStoryblokApi()
const { isPreview, version, removeTrailingSlash } = useStoryblokHelper()
const { locale } = useI18n()
const { breadcrumbs } = useBreadcrumbHelper()
const { setBreadcrumb } = useBreadcrumbStore()
const { buildHierarchy } = useUtilities()

const storyTop = computed(() => props.blok.body_top ?? [])

setBreadcrumb(breadcrumbs)

const firstSegment = route.path.split('/')[1]
const { data: items } = useAsyncData(
  `folderNavigationData-${firstSegment}`,
  async () => {
    const response = await storyblokApi.get('cdn/links', {
      version: isPreview(route.query) ? 'draft' : version.value,
      starts_with: `${firstSegment}/`,
      per_page: 100,
      language: locale.value
    })

    return buildHierarchy(Object.values(response.data.links).map((link) => {
      return {
        id: link.id,
        parent_id: link.parent_id,
        name: link.name,
        url: removeTrailingSlash(link.real_path)
      }
    }))
  }
)

</script>

<template>
  <div
    v-editable="blok"
    class="type-page">
    <AppBreadcrumb />

    <AppContentSidebar
      v-if="storyTop.length"
      class="mt-4 md:mt-8">
      <template #default>
        <component
          :is="body.component"
          v-for="body in storyTop"
          :key="body._uid"
          :blok="body" />
      </template>
    </AppContentSidebar>
    <AppContentSidebar class="mt-4 md:mt-14">
      <template #sidebar>
        <BaseSidebarNavigation
          v-if="items"
          :items="items"
          class="sticky top-5" />
      </template>
      <template #default>
        <component
          :is="body.component"
          v-for="body in blok?.body"
          :key="body._uid"
          :blok="body" />
      </template>
    </AppContentSidebar>
  </div>
</template>
