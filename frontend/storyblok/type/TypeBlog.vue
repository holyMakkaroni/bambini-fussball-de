<script setup lang="ts">
import { v4 as uuidv4 } from 'uuid'
import type { ISbStoryData } from 'storyblok-js-client'
import { breadcrumb } from 'instantsearch.js/es/widgets'
import { useBreadcrumbStore } from '~/stores/breadcrumb'
import type { TocStoryblok } from '~/types/components/base'

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

const { path, query } = useRoute()
const { t, locale } = useI18n()
const { isPreview, version, websiteHandle, cleanUrl } = useStoryblokHelper()
const storyblokApi = useStoryblokApi()
const { generateUrl } = useUtilities()
const { formatDate } = useUtilities()
const author = computed(() => props.blok.author?.length ? props.blok.author[0] : null)
const title = computed(() => props.blok.title)
const categories = computed(() => props.blok.categories?.length ? props.blok.categories : [])
const folderPath = path.substring(1).replace(/\/[^/]*$/, '')
const slug = computed(() => `${websiteHandle.value}/${folderPath}`)

const toc: TocStoryblok = {
  _uid: uuidv4(),
  component: 'c-toc',
  data: []
}

const processedBody = computed(() => {
  let tocAdded = false
  let skipFirstPair = true
  const result = []

  if (!props.blok.toc) {
    return props.blok.body
  }

  // Iterate through the blok body components
  for (let i = 0; i < props.blok.body.length; i++) {
    const currentComponent = props.blok.body[i]
    const nextComponent = props.blok.body[i + 1]

    if (skipFirstPair && currentComponent.component === 'c-headline' && currentComponent.headline_type === 'h2' && nextComponent?.component === 'c-text') {
      skipFirstPair = false
      result.push(currentComponent)
      result.push(nextComponent)
      i++
      continue
    }

    if (currentComponent.component === 'c-headline' && currentComponent.headline_type === 'h2') {
      toc.data.push({
        name: currentComponent.title,
        anchor: `#${generateUrl(currentComponent.title)}`
      })
    }

    if (!tocAdded && !skipFirstPair && currentComponent.component !== 'c-toc') {
      result.push(toc)
      tocAdded = true
    }

    result.push(currentComponent)
  }

  return result
})

if (categories.value.length) {
  const { breadcrumbs } = useBreadcrumbHelper(categories.value[0].full_slug)
  const { setBreadcrumb } = useBreadcrumbStore()

  breadcrumbs.push({
    name: title.value ?? '',
    path: ''
  })

  setBreadcrumb(breadcrumbs)
}

const { data: relatedPosts, pending } = await useLazyAsyncData(
  `related-blog-posts-${props.story.id}-${folderPath}-${locale.value}`, async () => {
    const { data } = await storyblokApi.get('cdn/stories', {
      version: isPreview(query) ? 'draft' : version.value,
      page: 1,
      per_page: 6,
      is_startpage: false,
      excluding_ids: props.story.id,
      starts_with: slug.value,
      language: locale.value,
      resolve_relations: 'type-blog.author'
    })

    return data.stories
  }, {
    transform: (stories) => {
      return stories.map((story: ISbStoryData) => {
        return {
          uuid: story.uuid,
          content: {
            image: story.content.image,
            headline: story.content.title,
            createdAt: story.content.published_date,
            url: story.full_slug,
            author: story.content.author
          }
        }
      })
    }
  })
</script>

<template>
  <div
    v-editable="blok"
    class="type-blog">
    <AppBreadcrumb light />

    <BlogHero
      class="md:-mt-[109px]"
      :title="title"
      headline_type="h1"
      :additional_title="blok.additional_title"
      :image="blok?.image" />
  </div>

  <div class="container">
    <div class="pt-10 py-12">
      <div class="flex flex-wrap gap-3 items-center text-sm">
        <div
          v-if="author?.content?.image"
          class="size-[90px] rounded-full overflow-hidden">
          <BaseImage
            v-if="author.content?.image?.filename"
            :lazy="false"
            :width="100"
            :height="100"
            sizes="100px"
            object-fit="cover"
            provider="storyblok"
            :image="author.content?.image" />
        </div>
        <div v-if="author">
          <NuxtLink
            :to="`/${author.full_slug}`"
            :title="author.name">
            {{ author.name }}
          </NuxtLink>
        </div>
        <div
          v-if="author"
          class="bg-secondary w-[1px] h-3.5" />
        <div v-if="blok.published_date">
          {{ formatDate(blok.published_date) }}
        </div>
        <div class="bg-secondary w-[1px] h-3.5" />
        <div>Kategorie</div>
        <div class="bg-secondary w-[1px] h-3.5" />
        <div>Optional Lesezeit: 5 Minuten</div>
        <div class="bg-secondary w-[1px] h-3.5" />
        <div>Optional Aufrufe: 3471</div>
      </div>
    </div>
  </div>

  <div class="[&>:first-child]:mt-0">
    <component
      :is="body.component"
      v-for="body in processedBody"
      :key="body._uid"
      container
      anchor
      :blok="body" />
  </div>

  <div
    v-if="author"
    class="container mt-15">
    <BaseAuthorBox :author="author" />
  </div>

  <div
    v-if="!pending && relatedPosts.length"
    class="container mt-15">
    <div class="text-2xl font-semibold mb-10">
      {{ t('storyblok.type.blogOverview.relatedPosts.headline') }}
    </div>
    <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 gap-5">
      <div
        v-for="post in relatedPosts"
        :key="post.uuid"
        class="w-full flex-shrink-0">
        <BaseBlogCard
          :image="post.content.image"
          :title="post.content.headline ?? ''"
          :created-date="post.content.createdAt"
          :url="cleanUrl(post.content.url)"
          :author="post.content.author?.length ? post.content.author[0] : null" />
      </div>
    </div>
  </div>
</template>
