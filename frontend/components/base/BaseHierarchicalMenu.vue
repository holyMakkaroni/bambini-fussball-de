<script setup lang="ts">
defineProps<{
  items: Array<{
    label: string
    value: string
    count: number
    isRefined: boolean
    data: any[]
  }>
  createUrl:(item: any) => string
  refine: (item: any) => void
}>()
</script>

<template>
  <ul class="space-y-5">
    <li
      v-for="item in items"
      :key="item.value"
      class="text-sm font-semibold">
      <a
        :href="createUrl(item.value)"
        :class="[
          'flex items-center hover:no-underline mb-5',
          item.isRefined ? 'text-primary' : ''
        ]"
        @click.prevent="refine(item.value)">
        {{ item.label }} ({{ item.count }})
      </a>

      <!-- Recursive rendering for nested data -->
      <BaseHierarchicalMenu
        v-if="item.data && item.data.length"
        :items="item.data"
        :create-url="createUrl"
        :refine="refine"
        class="ml-2.5 space-y-4 font-normal" />
    </li>
  </ul>
</template>

<style scoped>

</style>
