<script setup lang="ts">
import { computed } from 'vue'

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
const { isPreview, version, websiteHandle } = useStoryblokHelper()
const storyblokApi = useStoryblokApi()
const router = useRouter()
const route = useRoute()
const folderPath = route.path.substring(1)

const perPage = computed(() => 12)
const currentPage = computed(() => route.query.p ? Number(route.query.p) : 1)

const { data: stories, refresh } = await useLazyAsyncData(
  `wiki-listing-${folderPath}-${websiteHandle.value}-${locale.value}-${currentPage.value}`, async () => {
    const { data, total } = await storyblokApi.get('cdn/stories', {
      version: isPreview(route.query) ? 'draft' : version.value,
      per_page: perPage.value,
      page: currentPage.value,
      is_startpage: false,
      filter_query: {
        visibility: {
          any_in_array: websiteHandle.value
        }
      },
      starts_with: folderPath,
      language: locale.value
    })

    return {
      data: data.stories,
      total
    }
  })

const totalPosts = computed(() => stories.value?.total || 0)
const totalPages = computed(() => Math.ceil(totalPosts.value / perPage.value))

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
  <div
    v-if="stories?.data.length"
    v-editable="blok">
    <div
      class="h-[1px] bg-transparent md:bg-secondary-light mt-14 mb-7 md:mt-28 md:mb-14"
      :class="{'container': container}" />
    <div :class="{'container': container}">
      <div class="flex flex-col space-y-12">
        <div
          v-for="story in stories.data"
          :key="story.uuid"
          class="border-b border-secondary-light last:border-transparent pb-28">
          <WikiCard :story="story" />
        </div>
      </div>
      <div class="flex justify-center">
        <BasePagination
          :total="totalPages"
          :length="3"
          :current="route.query.p ? Number(route.query.p) : Number(currentPage)"
          @change-page="changePage" />
      </div>
    </div>
  </div>
</template>
