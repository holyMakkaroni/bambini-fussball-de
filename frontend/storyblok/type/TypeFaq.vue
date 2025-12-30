<script setup lang="ts">
import { useLazyAsyncData } from '#app'
import type { ISbStoryData } from 'storyblok-js-client'
import { useBreadcrumbStore } from '~/stores/breadcrumb'

const props = defineProps({
  story: {
    type: Object,
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
const { query } = useRoute()
const { locale, t } = useI18n()
const { isPreview, version, removeTrailingSlash } = useStoryblokHelper()
const storyblokApi = useStoryblokApi()
const { buildHierarchyFolders } = useUtilities()

replaceLastElement(breadcrumbs, {
  name: props.story.name
})

setBreadcrumb(breadcrumbs)

const { data: categories } = await useLazyAsyncData(
  `faq-categories-${locale.value}`, async () => {
    const { data } = await storyblokApi.get('cdn/stories', {
      version: isPreview(query) ? 'draft' : version.value,
      is_startpage: true,
      starts_with: `${route.path.split('/')[1]}/`,
      language: locale.value,
      per_page: 25,
      sort_by: 'position:asc'
    })

    return data.stories
  })

const title = computed(() => props.blok.title ? props.blok.title : props.story.name)
const text = computed(() => props.blok?.body)
const tags = computed(() => props.blok?.tags || [])
const sidebarItems = computed(() => buildHierarchyFolders(categories.value?.map((category: ISbStoryData) => {
  return {
    id: category.id,
    parent_id: category.parent_id,
    name: category.content.title ?? category.name,
    url: `/${removeTrailingSlash(category.full_slug)}`
  }
})))

const { data: relatedFaq } = await useLazyAsyncData(
  `related-faq-${locale.value}-${tags.value?.join('-')}`, async () => {
    const { data } = await storyblokApi.get('cdn/stories', {
      version: isPreview(query) ? 'draft' : version.value,
      is_startpage: false,
      language: locale.value,
      excluding_ids: props.story.id,
      filter_query: {
        tags: {
          any_in_array: tags.value?.join(',')
        }
      },
      per_page: 10,
      sort_by: 'position:asc'
    })

    return data.stories
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
    v-editable="blok"
    class="type-faq">
    <AppBreadcrumb />

    <AppContentSidebar class="mt-4 md:mt-14">
      <template #sidebar>
        <BaseSidebarNavigation
          v-if="sidebarItems?.length"
          :items="sidebarItems" />
      </template>
      <template #default>
        <div class="max-w-[840px]">
          <BaseHeadline
            tag="h1"
            :title="title" />
          <BaseText
            v-if="text"
            :text="text" />
        </div>
        <div
          v-if="tags.length && relatedFaq?.length"
          class="mt-15 flex flex-col">
          <div
            v-if="title"
            class="font-semibold text-xl">
            {{ t('storyblok.type.faq.relatedFaq') }}
          </div>
          <ul class="flex flex-col mt-8 space-y-3">
            <li
              v-for="faq in relatedFaq"
              :key="faq.uuid">
              <NuxtLink
                :to="`/${faq.full_slug}`"
                class="text-primary font-semibold">
                » {{ faq.name }}
              </NuxtLink>
            </li>
          </ul>
        </div>
      </template>
    </AppContentSidebar>
  </div>
</template>
