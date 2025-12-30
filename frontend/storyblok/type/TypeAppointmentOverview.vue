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
    class="type-appointment-overview">
    <div>Appointment overview page</div>
  </div>
</template>
