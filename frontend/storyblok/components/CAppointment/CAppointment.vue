<script setup lang="ts">
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
const { isPreview, version } = useStoryblokHelper()
const storyblokApi = useStoryblokApi()
const { query } = useRoute()

const teasersTop = computed(() => {
  return props.blok?.teaser.slice(0, 2)
})

const teasersBottom = computed(() => {
  return props.blok?.teaser.slice(2, 4)
})

const { data: appointments } = await useLazyAsyncData(
  `load-next-appointments-${locale.value}`, async () => {
    const { data } = await storyblokApi.get('cdn/stories', {
      version: isPreview(query) ? 'draft' : version.value,
      is_startpage: false,
      starts_with: 'appointment/',
      language: locale.value,
      resolve_links: 'url',
      filter_query: {
        start_at: {
          gt_date: new Date().toISOString()
        }
      },
      sort_by: 'content.start_at:asc'
    })

    return data.stories || []
  })
</script>

<template>
  <div
    v-editable="blok"
    class="c-appointment bg-secondary-light margin-default">
    <div
      class="pt-14 pb-28"
      :class="{'container': container}">
      <div
        v-if="blok.headline || blok.description"
        class="max-w-4xl mx-auto">
        <div class="flex flex-col justify-center text-center mb-6">
          <BaseHeadline
            v-if="blok.headline"
            tag="div"
            :title="blok.headline"
            custom-class="text-2xl md:text-3xl !mb-0" />
          <div
            v-if="blok.description"
            class="text-xl md:text-2xl">
            {{ blok.description }}
          </div>
        </div>
      </div>
      <div
        v-if="teasersTop.length"
        :class="{'mb-2': appointments?.length === 0}">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div
            v-for="teaser in teasersTop"
            :key="teaser.uid"
            class="flex-1 relative">
            <StoryblokComponent :blok="teaser" />
          </div>
        </div>
      </div>
      <div v-if="appointments?.length">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 my-4">
          <BaseAppointmentCard
            v-for="appointment in appointments"
            :key="appointment.uuid"
            :title="appointment.content?.title"
            :description="appointment.content?.description"
            :start-at="appointment.content?.start_at"
            :end-at="appointment.content?.end_at"
            :url="appointment.full_slug" />
        </div>
      </div>
      <div
        v-if="teasersBottom.length"
        :class="{'mt-2': appointments?.length === 0}">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div
            v-for="teaser in teasersBottom"
            :key="teaser.uid"
            class="flex-1 relative">
            <StoryblokComponent :blok="teaser" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
