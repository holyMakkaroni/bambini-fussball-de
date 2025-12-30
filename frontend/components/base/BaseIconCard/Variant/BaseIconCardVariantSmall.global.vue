<script setup lang="ts">
defineProps({
  icon: {
    type: String,
    required: true
  },
  headline: {
    type: String,
    required: true
  }
})
</script>

<template>
  <div class="c-base-icon-card-small bg-white flex flex-col items-center p-4">
    <div class="w-20 h-14 flex justify-center items-center">
      <BaseIcon
        v-if="icon"
        :name="icon"
        draggable="false"
        class="w-full" />
    </div>
    <hr
      v-if="headline"
      class="w-full h-[1px] border-secondary-light">
    <div
      v-if="headline"
      class="text-xs mt-4 text-center">
      {{ headline }}
    </div>
  </div>
</template>

<style scoped>

</style>
