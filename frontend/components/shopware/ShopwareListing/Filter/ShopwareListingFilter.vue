<script setup lang="ts" generic="ListingFilter extends { code: string }">
import type { ConcreteComponent } from 'vue'

const props = defineProps<{
  filter: ListingFilter;
  index: number
}>()

const cmsMap = () => {
  const map: {
    [key: string]: ConcreteComponent | string;
  } = {
    properties: resolveComponent('ShopwareListingFilterProperties')
  }

  return map[props.filter?.code]
}

</script>
<template>
  <component
    :is="cmsMap()"
    :filter="filter" />
</template>
