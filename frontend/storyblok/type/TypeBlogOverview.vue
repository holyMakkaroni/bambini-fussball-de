<script setup lang="ts">
import { computed } from 'vue'
import type { ISbStoriesParams, ISbStoryData } from 'storyblok-js-client'
import { useBreadcrumbStore } from '~/stores/breadcrumb'

const props = defineProps({
  blok: {
    type: Object,
    default: null
  },
  story: {
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

const { breadcrumbs } = useBreadcrumbHelper()
const { setBreadcrumb } = useBreadcrumbStore()
const { path, query } = useRoute()
const { locale } = useI18n()
const { isPreview, version, removeTrailingSlash, websiteHandle, cleanUrl } = useStoryblokHelper()
const storyblokApi = useStoryblokApi()
const { buildHierarchyFolders } = useUtilities()
const folderPath = removeTrailingSlash(path).substring(1).split('/')

setBreadcrumb(breadcrumbs)

const route = useRoute()
const router = useRouter()
const currentPage = computed(() => route.query.p ? Number(route.query.p) : 1)
const perPage = computed(() => 18)
const totalPosts = computed(() => posts.value?.total || 0)
const totalPages = computed(() => Math.ceil(totalPosts.value / perPage.value))
const slug = computed(() => `${websiteHandle.value}/${route.path.split('/')[1]}/`)

const { data: categories } = await useCachedAsyncData(
  `blog-categories-${locale.value}`, async () => {
    const { data } = await storyblokApi.get('cdn/stories', {
      version: isPreview(query) ? 'draft' : version.value,
      is_startpage: true,
      starts_with: slug.value,
      language: locale.value,
      per_page: 25,
      sort_by: 'position:asc'
    })

    return data.stories
  }, {
    serverMaxAge: 3600,
    serverCacheTags: [
      'blog:categories'
    ],
    transform: (categories) => {
      return buildHierarchyFolders(categories.map((category: ISbStoryData) => {
        return {
          id: category.id,
          parent_id: category.parent_id,
          name: category.content.title,
          url: cleanUrl(category.full_slug)
        }
      }))
    }
  })

const defaultParams = ref<ISbStoriesParams>({
  version: isPreview(query) ? 'draft' : version.value,
  content_type: 'type-blog',
  sort_by: 'content.published_date:desc',
  per_page: perPage.value,
  page: currentPage.value,
  language: locale.value,
  resolve_relations: 'type-blog.author'
})

if (folderPath.length > 1) {
  defaultParams.value.filter_query = {
    categories: {
      in: props.story.uuid
    }
  }
} else {
  defaultParams.value.starts_with = slug.value
}

const { data: posts, refresh } = await useCachedAsyncData(
  `blog-posts-${folderPath.length > 1 ? props.story.uuid : folderPath[0]}-${locale.value}-${currentPage.value}`, async () => {
    const { data, total } = await storyblokApi.get('cdn/stories', defaultParams.value)

    if (!props.blok?.newsletter_title) {
      return {
        stories: [...data.stories],
        total
      }
    }

    return {
      stories: [...data.stories.slice(0, 6), { type: 'newsletter' }, ...data.stories.slice(6)],
      total
    }
  }, {
    serverMaxAge: 3600,
    serverCacheTags: [
      `blog:posts:p:${currentPage.value}:${locale.value}`
    ],
    transform: (response) => {
      return {
        stories: response.stories.map(story => ({
          id: story.id,
          uuid: story.uuid,
          type: story?.type,
          full_slug: story.full_slug,
          created_at: story.content?.published_date,
          content: {
            title: story.content?.title || '',
            author: story.content?.author || null,
            image: story.content?.image || null
          }
        })),
        total: response.total
      }
    }
  })

const changePage = async (page: number) => {
  await router.push({
    path: route.path,
    query: {
      p: page
    }
  })

  defaultParams.value.page = page

  await refresh()
}
</script>

<template>
  <div
    v-editable="blok"
    class="type-blog-overview">
    <AppBreadcrumb light />

    <BlogHero
      v-if="blok?.image"
      class="max-h-[768px] md:-mt-[109px]"
      :title="blok.title"
      headline_type="h1"
      :additional_title="blok.additional_title"
      :image="blok?.image" />

    <AppContentSidebar class="mt-4 md:mt-14">
      <template #sidebar>
        <BaseSidebarNavigation
          v-if="categories?.length"
          :items="categories" />
      </template>
      <template #default>
        <BaseTags
          class="border-t border-b border-secondary-light py-4 mb-4 md:mb-14"
          source="blog-tags" />
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
          <div
            v-for="story in posts?.stories"
            :key="story.uuid"
            :class="{'col-span-1': story?.type, 'col-span-full': story?.type}">
            <BaseBlogCard
              v-if="!story?.type"
              :image="story.content.image"
              :title="story.content.title ?? ''"
              :created-date="story.created_at"
              :url="cleanUrl(story.full_slug)"
              :author="story.content.author?.length ? story.content.author[0] : null" />
            <BaseNewsletterForm
              v-else-if="blok?.newsletter_title"
              :mailing-list-id="blok?.mailing_list_id"
              :interests="blok?.newsletter_interests"
              :title="blok.newsletter_title"
              :description="blok.newsletter_description"
              :headline_type="blok.newsletter_headline_type" />
          </div>
        </div>
        <div class="flex justify-center mt-8">
          <BasePagination
            :total="totalPages"
            :length="3"
            :current="route.query.p ? Number(route.query.p) : Number(currentPage)"
            @change-page="changePage" />
        </div>
      </template>
    </AppContentSidebar>
  </div>
</template>
