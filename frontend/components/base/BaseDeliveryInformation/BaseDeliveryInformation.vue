<script setup lang="ts">
defineProps({
  showCountdown: {
    type: Boolean,
    default: true
  }
})
</script>

<template>
  <div class="flex flex-wrap text-xs gap-x-2">
    <div class="flex items-center text-success">
      <BaseIcon
        name="truck"
        class="w-5 h-5 mr-2 -mt-0.5" />
      <span class="font-semibold">Sofort lieferbar</span>
    </div>
    <div
      v-if="showCountdown"
      class="flex items-center">
      <span class="mr-1">Lieferung 19. bis 20. Januar. Bestellung innerhalb:</span>
      <span class="font-semibold text-success">3 Stdn. 46 Min.</span>
    </div>
  </div>
</template>

<style scoped>

</style>
