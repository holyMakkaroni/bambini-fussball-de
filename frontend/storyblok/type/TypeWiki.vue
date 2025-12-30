<script setup lang="ts">
defineProps({
  blok: {
    type: Object,
    default: null
  }
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
  <ErrorRoutingNotFound />
</template>
