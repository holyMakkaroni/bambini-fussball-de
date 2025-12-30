<script setup lang="ts">
const props = defineProps({
  source: {
    type: String as PropType<'blog-tags'>,
    required: true
  }
})

const { locale } = useI18n()
const storyblokApi = useStoryblokApi()

const name = computed(() => {
  switch (props.source) {
    case 'blog-tags':
      return 'magazin-tag-tag'
  }
})

const { data: tags } = await useCachedAsyncData(
  `tags-${props.source}-${locale.value}`, async () => {
    const { data } = await storyblokApi.get('cdn/datasource_entries', {
      datasource: 'blog-tags',
      dimension: locale.value !== 'de-DE' ? locale.value : undefined
    })

    return data.datasource_entries
  }, {
    serverMaxAge: 3600,
    serverCacheTags: [
      'blog:tags:list'
    ]
  })
</script>

<template>
  <ul class="c-base-tags flex flex-row flex-wrap gap-x-9">
    <li
      v-for="tag in tags"
      :key="tag.id">
      <NuxtLinkLocale
        :to="{
          name: name,
          params: {
            tag: tag.value
          }
        }"
        :locale="locale"
        class="flex font-light text-lg hover:no-underline">
        {{ tag.name }}
      </NuxtLinkLocale>
    </li>
  </ul>
</template>

<style scoped>

</style>
