<script setup lang="ts">
import { useBreadcrumbStore } from '~/stores/breadcrumb'

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

const { breadcrumbs } = useBreadcrumbHelper()
const { setBreadcrumb } = useBreadcrumbStore()
const name = computed(() => props.story.name)

setBreadcrumb(breadcrumbs)
</script>

<template>
  <div
    v-editable="blok"
    class="type-author">
    <AppBreadcrumb />
  </div>

  <div class="container">
    Author page {{ name }}
  </div>
</template>
