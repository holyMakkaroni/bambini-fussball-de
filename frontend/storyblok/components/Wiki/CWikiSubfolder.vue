<script setup lang="ts">
import type { ISbStoryData } from 'storyblok-js-client'

defineProps({
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
const { isPreview, version, removeTrailingSlash, websiteHandle } = useStoryblokHelper()
const storyblokApi = useStoryblokApi()
const { path, query } = useRoute()
const folderPath = path.substring(1)

const { data: stories } = await useLazyAsyncData(
  `wiki-subfolder-${folderPath}-${websiteHandle.value}-${locale.value}`, async () => {
    const { data } = await storyblokApi.get('cdn/stories', {
      version: isPreview(query) ? 'draft' : version.value,
      is_startpage: true,
      starts_with: folderPath,
      filter_query: {
        visibility: {
          any_in_array: websiteHandle.value
        }
      },
      per_page: 36,
      excluding_slugs: folderPath.endsWith('/') ? folderPath : `${folderPath}/`,
      language: locale.value
    })

    return data.stories || []
  },
  {
    transform: (stories) => {
      return stories.map((story: ISbStoryData) => {
        if (story.content.image && story.content.image?.filename) {
          return {
            uuid: story.uuid,
            full_slug: story.full_slug,
            content: {
              image: story.content.image
            }
          }
        }

        return false
      })
    }
  }
)
</script>

<template>
  <div
    v-if="stories.length"
    v-editable="blok"
    class="mt-14 md:mt-28"
    :class="{'container': container}">
    <BaseHeadline
      v-if="blok?.title"
      :tag="blok?.headline_type"
      :title="blok?.title" />
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 xl:gap-5">
      <NuxtLink
        v-for="story in stories"
        :key="story.uuid"
        :to="`/${ removeTrailingSlash(story.full_slug) }`"
        class="hover:no-underline">
        <BaseBorderedCard class="transition-default grayscale hover:grayscale-0 hover:box-shadow">
          <BaseImage
            :width="120"
            sizes="120"
            :lazy="true"
            provider="storyblok"
            img-class="max-h-[50px]"
            :image="story.content.image" />
        </BaseBorderedCard>
      </NuxtLink>
    </div>
  </div>
</template>
