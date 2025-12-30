<script setup lang="ts">
import type { TypeFaqOverviewStoryblok } from '~/types/storyblok'

const props = defineProps({
  title: {
    type: String,
    default: null
  },
  group: {
    type: Object as PropType<TypeFaqOverviewStoryblok>,
    required: true
  },
  showAll: {
    type: Boolean,
    default: true
  },
  border: {
    type: Boolean,
    default: true
  },
  limit: {
    type: Number,
    default: 5
  }
})

const { query } = useRoute()
const { locale, t } = useI18n()
const { isPreview, version, removeTrailingSlash, websiteHandle } = useStoryblokHelper()
const storyblokApi = useStoryblokApi()

const groupTitle = computed(() => props.group.content.title)
const uuid = computed(() => props.group.uuid)

const { data: items } = await useLazyAsyncData(
  `faq-category-items-${locale.value}-${websiteHandle.value}-${uuid.value}`, async () => {
    const { data } = await storyblokApi.get('cdn/stories', {
      version: isPreview(query) ? 'draft' : version.value,
      is_startpage: false,
      starts_with: props.group.full_slug,
      filter_query: {
        visibility: {
          any_in_array: websiteHandle.value
        }
      },
      language: locale.value,
      per_page: props.limit,
      sort_by: 'position:asc'
    })

    return data.stories
  }
)
</script>

<template>
  <div
    v-if="items && items.length && (title || groupTitle)"
    class="c-base-faq-group-item"
    :class="{'border border-secondary-light p-5 md:p-10': border}">
    <div
      v-if="title || groupTitle"
      class="font-semibold text-xl">
      {{ title ? title : groupTitle }}
    </div>
    <ul class="flex flex-col mt-8 space-y-3">
      <li
        v-for="item in items"
        :key="item.uuid">
        <NuxtLink
          :to="`/${item.full_slug}`"
          class="text-primary font-semibold">
          » {{ item.name }}
        </NuxtLink>
      </li>
      <li v-if="showAll">
        <NuxtLink
          :to="`/${removeTrailingSlash(group.full_slug)}`"
          class="text-primary font-semibold">
          » {{ t('components.base.faqGroupItem.all') }}
        </NuxtLink>
      </li>
    </ul>
  </div>
</template>

<style scoped>

</style>
