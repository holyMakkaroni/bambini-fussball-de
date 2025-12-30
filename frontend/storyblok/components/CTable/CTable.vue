<script setup lang="ts">
defineProps({
  blok: {
    type: Object,
    default: null
  },
  container: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <div
    v-editable="blok"
    class="c-table margin-components"
    :class="{'container': container}">
    <div class="table">
      <div
        v-if="blok.table.thead.length"
        class="row heading">
        <div
          v-for="thead in blok.table.thead"
          :key="thead._uid"
          class="cell">
          {{ thead.value }}
        </div>
      </div>
      <div
        v-for="tbody in blok.table.tbody"
        :key="tbody._uid"
        class="row odd:bg-secondary-light even:bg-white">
        <div
          v-for="(body, index) in tbody.body"
          :key="body._uid"
          class="cell"
          :class="{'font-bold': index === 0 && blok.first_column_bold}">
          {{ body.value }}
        </div>
      </div>
    </div>
  </div>
</template>
