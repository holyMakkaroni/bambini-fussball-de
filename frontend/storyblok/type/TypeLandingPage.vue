<script setup lang="ts">
const props = defineProps({
  blok: {
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

</script>

<template>
  <div
    v-editable="blok"
    class="type-landing-page">
    <component
      :is="body.component"
      v-for="body in blok?.body"
      :key="body._uid"
      container
      :blok="body" />
  </div>
</template>
