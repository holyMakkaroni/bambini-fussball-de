<script setup lang="ts">
import type { TypeBlogStoryblok } from '~/types/storyblok'

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

const { locale } = useI18n()
const { isPreview, version, websiteHandle, cleanUrl } = useStoryblokHelper()
const storyblokApi = useStoryblokApi()
const { query } = useRoute()
const postQuery = ref<Object>({})

if (props.blok.categories.length) {
  postQuery.value = {
    filter_query: {
      categories: {
        any_in_array: props.blok?.categories.join(',')
      }
    }
  }
}

if (props.blok.posts.length) {
  postQuery.value = {
    by_uuids: props.blok?.posts.join(',')
  }
}

const { data: stories } = await useLazyAsyncData(
  `posts-${websiteHandle.value}-by-categories-${locale.value}`, async () => {
    const { data } = await storyblokApi.get('cdn/stories', {
      version: isPreview(query) ? 'draft' : version.value,
      is_startpage: false,
      starts_with: `${websiteHandle.value}/magazin/`,
      sort_by: 'content.published_date:desc',
      per_page: props.blok.limit,
      language: locale.value,
      resolve_relations: 'type-blog.author',
      ...postQuery.value
    })

    return data.stories || []
  }, {
    transform: (stories) => {
      return stories.map((story: TypeBlogStoryblok) => {
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
  <BaseSectionWrapper
    v-if="stories?.length"
    v-editable="blok"
    identifier="c-blog-posts"
    :title="blok.title"
    :link="blok.link"
    :label="blok.label"
    :container="container">
    <BaseScrollbar
      class="flex"
      padding="normal"
      :items="1"
      :items-to-slide="1"
      :gap="20"
      :responsive="{
        640: {
          items: 2,
          itemsToSlide: 2,
        },
        991: {
          items: 3,
          itemsToSlide: 3,
        }
      }"
      arrows>
      <div
        v-for="story in stories"
        :key="story.uuid"
        class="w-full flex-shrink-0">
        <BaseBlogCard
          :image="story.content.image"
          :title="story.content.headline ?? ''"
          :created-date="story.content.createdAt"
          :url="cleanUrl(story.content.url)"
          :author="story.content.author?.length ? story.content.author[0] : null" />
      </div>
    </BaseScrollbar>
  </BaseSectionWrapper>
</template>

<style scoped>

</style>
