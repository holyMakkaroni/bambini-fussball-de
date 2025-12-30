<script lang="ts" setup>
</script>

<template>
  <li class="c-app-search-group-item">
    <div class="flex items-center">
      <div class="flex-1 cursor-pointer transition-all duration-300 ease-in-out hover:text-primary">
        <slot />
      </div>
      <div>
        <slot name="icon" />
      </div>
    </div>
  </li>
</template>

<style scoped>

</style>
