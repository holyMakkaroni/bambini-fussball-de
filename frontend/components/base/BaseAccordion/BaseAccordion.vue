<script setup lang="ts">
const expandedItemIndex = ref<Number|null>(null)

const setExpandedItem = (index: number) => {
  expandedItemIndex.value = index
}

provide('expandedItemIndex', expandedItemIndex)
provide('setExpandedItem', setExpandedItem)
</script>

<template>
  <div class="c-base-accordion">
    <slot />
  </div>
</template>

<style scoped>

</style>
