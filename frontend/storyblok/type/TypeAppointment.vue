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
  <div
    v-editable="blok"
    class="type-appointment">
    <div>Appointment page</div>

    <component
      :is="body.component"
      v-for="body in blok?.body"
      :key="body._uid"
      :blok="body" />
  </div>
</template>
