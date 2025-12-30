<script setup lang="ts">
import type { ISbStoryData } from 'storyblok-js-client'
import { computed } from 'vue'
import BaseTags from '~/components/base/BaseTags/BaseTags.vue'
import { useBreadcrumbStore } from '~/stores/breadcrumb'

const route = useRoute()
const router = useRouter()
const { locale } = useI18n()
const storyblokApi = useStoryblokApi()
const { isPreview, version, websiteHandle, cleanUrl } = useStoryblokHelper()

const { removePath } = useBreadcrumbHelper()
const { setBreadcrumb } = useBreadcrumbStore()
const { buildHierarchyFolders } = useUtilities()
const { query } = useRoute()
const currentPage = computed(() => route.query.p ? Number(route.query.p) : 1)
const perPage = computed(() => 18)
const totalPosts = computed(() => posts.value?.total || 0)
const totalPages = computed(() => Math.ceil(totalPosts.value / perPage.value))
const slug = computed(() => `${websiteHandle.value}/${route.path.split('/')[1]}/`)

setBreadcrumb(removePath(['Tag']))

const { data: blogConfig } = useCachedAsyncData(
  `blog-config-${locale.value}`, async () => {
    const { data } = await storyblokApi.get('cdn/stories', {
      version: isPreview(query) ? 'draft' : version.value,
      starts_with: `${websiteHandle.value}/config/blog`,
      resolve_links: 'url',
      language: locale.value
    })

    return data.stories[0]
  }, {
    serverMaxAge: 3600,
    serverCacheTags: [
      'blog:config'
    ]
  })

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

const { data: posts, refresh } = await useCachedAsyncData(
  `blog-posts-by-tag-${route.params.tag}-${locale.value}-${currentPage.value}`, async () => {
    const { data, total } = await storyblokApi.get('cdn/stories', {
      version: isPreview(route.query) ? 'draft' : version.value,
      per_page: perPage.value,
      page: currentPage.value,
      starts_with: `${websiteHandle.value}/magazin/`,
      filter_query: {
        tags: {
          in_array: route.params.tag
        }
      },
      language: locale.value,
      resolve_relations: 'type-blog.author'
    })

    return {
      stories: [...data.stories],
      total
    }
  }, {
    serverMaxAge: 3600,
    serverCacheTags: [
      `blog-posts-by-tag-${route.params.tag}-${locale.value}-${currentPage.value}`
    ]
  })

const changePage = async (page: number) => {
  await router.push({
    path: route.path,
    query: {
      p: page
    }
  })

  await refresh()
}
</script>

<template>
  <div class="type-blog-tag">
    <AppBreadcrumb light />

    <BlogHero
      v-if="blogConfig.content?.image"
      class="max-h-[768px] md:-mt-[109px]"
      :title="blogConfig.content.title"
      headline_type="h1"
      :additional_title="blogConfig.content.additional_title"
      :image="blogConfig.content?.image" />

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
          </div>
        </div>

        <div class="flex justify-center mt-8">
          <BasePagination
            :total="totalPages"
            :length="1"
            :current="route.query.p ? Number(route.query.p) : Number(currentPage)"
            @change-page="changePage" />
        </div>
      </template>
    </AppContentSidebar>
  </div>
</template>

<style scoped>

</style>
