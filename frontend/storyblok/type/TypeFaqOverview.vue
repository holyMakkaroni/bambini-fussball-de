<script setup lang="ts">
import type { ISbStoryData } from 'storyblok-js-client'
import { useLazyAsyncData } from '#app'
import { useBreadcrumbStore } from '~/stores/breadcrumb'
import type { TypeFaqOverviewStoryblok } from '~/types/storyblok'
import BaseFaqGroupItem from '~/components/base/BaseFaqGroup/BaseFaqGroupItem.vue'
import BaseFaqSearch from '~/components/base/BaseFaqSearch/BaseFaqSearch.vue'

const props = defineProps({
  story: {
    type: Object as PropType<TypeFaqOverviewStoryblok>,
    default: null
  },
  blok: {
    type: Object,
    default: null
  }
})

const { breadcrumbs, replaceLastElement } = useBreadcrumbHelper()
const { setBreadcrumb } = useBreadcrumbStore()
const route = useRoute()
const { path, query } = useRoute()
const { locale, t } = useI18n()
const { isPreview, version, removeTrailingSlash, websiteHandle } = useStoryblokHelper()
const storyblokApi = useStoryblokApi()
const { buildHierarchyFolders } = useUtilities()
const folderPath = removeTrailingSlash(path).substring(1).split('/')

replaceLastElement(breadcrumbs, {
  name: props.blok.title ?? props.story.name
})

setBreadcrumb(breadcrumbs)

const searchQuery = ref<string>('')

const { data: categories } = await useLazyAsyncData(
  `faq-categories-${locale.value}-${websiteHandle.value}-${searchQuery}`, async () => {
    const { data } = await storyblokApi.get('cdn/stories', {
      version: isPreview(query) ? 'draft' : version.value,
      is_startpage: true,
      starts_with: `${route.path.split('/')[1]}/`,
      filter_query: {
        visibility: {
          any_in_array: websiteHandle.value
        }
      },
      language: locale.value,
      per_page: 25,
      search_term: searchQuery.value,
      sort_by: 'position:asc'
    })

    return data.stories
  }, {
    watch: [searchQuery]
  })

const sidebarItems = computed(() => buildHierarchyFolders(categories.value?.map((category: ISbStoryData) => {
  return {
    id: category.id,
    parent_id: category.parent_id,
    name: category.content.title ?? category.name,
    url: `/${removeTrailingSlash(category.full_slug)}`
  }
})))

const cleanedPath = computed(() => path.startsWith('/') ? path.slice(1) : path)

const currentGroup = computed(() => {
  return categories.value?.filter((category: any) => category.full_slug.startsWith(cleanedPath.value) && category.id === props.story.id) || []
})

const groups = computed(() => {
  return categories.value?.filter((category: any) => category.full_slug.startsWith(cleanedPath.value) && category.id !== props.story.id) || []
})

const allowed = computed(() => {
  const visibility = props.blok.visibility
  return !!visibility.includes(websiteHandle.value)
})

useHead({
  title: 'LivingActive',
  meta: [
    {
      name: 'description',
      content: 'Meta description goes here'
    }
  ]
})
</script>

<template>
  <div
    v-if="allowed"
    v-editable="blok"
    class="type-faq-overview">
    <AppBreadcrumb />

    <AppContentSidebar class="mt-4 md:mt-14">
      <template #sidebar>
        <BaseSidebarNavigation
          v-if="sidebarItems?.length"
          :items="sidebarItems" />
      </template>
      <template #default>
        <div class="max-w-[840px]">
          <div class="flex flex-col">
            <BaseHeadline
              tag="h1"
              :title="blok.title ? blok.title : story.name" />
            <BaseText
              v-if="blok?.description"
              :text="blok?.description" />
          </div>
          <div class="flex flex-col my-11">
            <div class="text-4xl md:text-6xl lg:text-8xl font-semibold text-primary">
              Hier finden Sie Antworten.
            </div>
            <div class="min-h-12">
              <ClientOnly>
                <BaseFaqSearch instance-key="faq-search" />
              </ClientOnly>
            </div>
          </div>
        </div>
        <div class="mt-15 flex flex-col">
          <ul
            v-if="currentGroup && folderPath.length > 1"
            class="flex flex-col mt-8 space-y-3">
            <li
              v-for="group in currentGroup"
              :key="group._uid">
              <BaseFaqGroupItem
                :title="t('components.base.faqGroupItem.headline', {
                  groupName: group.content.title
                })"
                :group="group"
                :show-all="false"
                :border="false"
                :limit="100" />
            </li>
          </ul>
          <div
            v-if="groups.length"
            class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-15">
            <BaseFaqGroupItem
              v-for="group in groups"
              :key="group._uid"
              :group="group" />
          </div>
        </div>
      </template>
    </AppContentSidebar>
  </div>
  <ErrorRoutingNotFound v-else />
</template>
