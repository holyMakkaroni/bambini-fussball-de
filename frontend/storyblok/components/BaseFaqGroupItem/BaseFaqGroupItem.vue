<script setup lang="ts">
const props = defineProps({
  group: {
    type: Object,
    required: true
  }
})

const { query } = useRoute()
const { locale, t } = useI18n()
const { isPreview, version, removeTrailingSlash } = useStoryblokHelper()
const storyblokApi = useStoryblokApi()

const title = computed(() => props.group.content.title)
const uuid = computed(() => props.group.uuid)

const { data: items } = await useLazyAsyncData(
  `faq-category-items-${locale.value}-${uuid.value}`, async () => {
    const { data } = await storyblokApi.get('cdn/stories', {
      version: isPreview(query) ? 'draft' : version.value,
      is_startpage: false,
      starts_with: props.group.full_slug,
      language: locale.value,
      per_page: 5,
      sort_by: 'position:asc'
    })

    return data.stories
  }
)
</script>

<template>
  <div
    v-if="items && items.length && title"
    class="c-faq-group-item border border-secondary-light p-5 md:p-10">
    <div
      v-if="title"
      class="font-semibold text-xl">
      {{ title }}
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
      <li>
        <NuxtLink
          :to="`/${removeTrailingSlash(group.full_slug)}`"
          class="text-primary font-semibold">
          » {{ t('storyblok.components.faqGroupItem.all') }}
        </NuxtLink>
      </li>
    </ul>
  </div>
</template>

<style scoped>

</style>
