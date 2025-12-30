<script setup lang="ts">
defineProps({
  isRefine: {
    type: Boolean,
    required: true
  },
  isMobile: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <div
    class="c-base-filter-toggle cursor-pointer group"
    :class="[
      {'mr-4': !isMobile}
    ]">
    <div
      v-if="!isMobile"
      class="flex items-center border border-secondary-light px-3 py-1.5 rounded-md text-sm gap-x-3"
      :class="{'bg-secondary-light': isRefine, 'hover:bg-secondary-light/30': !isRefine}">
      <div class="flex flex-col justify-center">
        <div
          class="relative h-1 w-6 rounded-full"
          :class="{'bg-primary': isRefine, 'bg-secondary-light': !isRefine}">
          <div
            class="absolute top-[1.75px] size-4 rounded-full transition-default border border-white bg-secondary -translate-y-1/2"
            :class="{'translate-x-2': isRefine, '-translate-x-1': !isRefine}" />
        </div>
      </div>

      <div class="transition-default whitespace-nowrap">
        <slot name="label" />
      </div>
    </div>
    <div v-else>
      <div class="font-semibold text-sm mb-3">
        <slot name="label" />
      </div>
      <div class="rounded-full w-[58px] h-[30px] bg-secondary-light relative">
        <div
          class="size-6 rounded-full absolute top-[3px] left-0 transition-default"
          :class="[
            {
              'translate-x-[calc(0px_+_3px)] bg-secondary': !isRefine,
              'translate-x-[calc(100%_+_7px)] bg-primary': isRefine
            }
          ]" />
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
