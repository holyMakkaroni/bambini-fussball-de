<script setup lang="ts">
import type { ISbStoryData } from 'storyblok-js-client'

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
const { isPreview, version, websiteHandle } = useStoryblokHelper()
const storyblokApi = useStoryblokApi()
const { path, query } = useRoute()
const folderName = path.substring(1)
const modal = ref(null)
const selectedStory = ref<ISbStoryData>()

const { data: stories } = await useLazyAsyncData(
  `wiki-suitable-for-${props.blok?.filter}-${websiteHandle.value}-${locale.value}`, async () => {
    const { data } = await storyblokApi.get('cdn/stories', {
      version: isPreview(query) ? 'draft' : version.value,
      is_startpage: false,
      starts_with: folderName,
      per_page: 12,
      filter_query: {
        visibility: {
          any_in_array: websiteHandle.value
        },
        suitable_for: {
          any_in_array: props.blok?.filter
        }
      },
      language: locale.value
    })

    return data.stories || []
  }, {
    transform: (stories) => {
      return stories.map((story: ISbStoryData) => {
        if (story.content.image && story.content.image?.filename) {
          return {
            uuid: story.uuid,
            content: {
              title: story.content.title,
              image: story.content.image,
              description: story.content.description,
              datasheet: story.content.datasheet
            }
          }
        }

        return false
      })
    }
  }
)

const openModal = (story: ISbStoryData) => {
  selectedStory.value = story
  modal.value?.open()
}

const closeModal = () => {
  selectedStory.value = undefined
}
</script>

<template>
  <div
    v-if="stories.length"
    v-editable="props.blok"
    class="mt-14 md:mt-28"
    :class="{'container': container}">
    <Teleport to="#teleports">
      <BaseModal
        ref="modal"
        @close-modal="closeModal">
        <WikiCard
          :story="selectedStory"
          :show-products="false" />
      </BaseModal>
    </Teleport>
    <BaseHeadline
      v-if="props.blok?.title"
      custom-class="col-span-12"
      :tag="props.blok.headline_type"
      :title="props.blok?.title" />
    <div class="flex flex-col">
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 xl:gap-5">
        <div
          v-for="story in stories"
          :key="story.uuid"
          class="cursor-pointer hover:no-underline"
          @click="openModal(story)">
          <BaseBorderedCard class="transition-default grayscale hover:grayscale-0 hover:box-shadow">
            <BaseImage
              :width="120"
              sizes="120"
              :lazy="true"
              provider="storyblok"
              img-class="max-h-[60px]"
              :image="story.content.image" />
            <div class="mt-1.5 text-sm text-center text-ellipsis max-w-full text-nowrap overflow-x-hidden">
              {{ story.content.title }}
            </div>
          </BaseBorderedCard>
        </div>
      </div>
    </div>
  </div>
</template>
