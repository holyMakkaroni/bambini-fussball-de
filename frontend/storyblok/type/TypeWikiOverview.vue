<script setup lang="ts">
import { useBreadcrumbStore } from '@/stores/breadcrumb'
const props = defineProps({
  blok: {
    type: Object,
    default: null
  }
})

const { breadcrumbs } = useBreadcrumbHelper()
const { setBreadcrumb } = useBreadcrumbStore()
const { websiteHandle } = useStoryblokHelper()

setBreadcrumb(breadcrumbs)

const allowed = computed(() => {
  const visibility = props.blok.visibility
  return !!visibility.includes(websiteHandle.value)
})

useHead({
  title: 'LivingActive',
  meta: [
    {
      name: 'description',
      content: 'Meta description goes here'
    }
  ]
})
</script>

<template>
  <div
    v-if="allowed"
    v-editable="props.blok"
    class="type-wiki-overview">
    <AppBreadcrumb />

    <WikiHead
      :title="props.blok?.title"
      :description="props.blok?.description"
      :image="props.blok?.image"
      container />

    <component
      :is="body.component"
      v-for="body in props.blok?.body"
      :key="body._uid"
      container
      :blok="body" />
  </div>
  <ErrorRoutingNotFound v-else />
</template>
