<script setup lang="ts">
import type { FormCheckbox } from '@/types/components/form'

const props = defineProps<FormCheckbox>()
const emit = defineEmits(['update:modelValue', 'select'])

const model = computed({
  get () {
    return props.modelValue
  },
  set (value) {
    emit('update:modelValue', value)
  }
})
</script>

<template>
  <div class="c-form-checkbox">
    <label
      class="flex items-center cursor-pointer relative mr-1.5 !mt-0.5"
      :for="id">
      <input
        :id="id"
        v-model="model"
        type="checkbox"
        :value="value"
        :checked="checked"
        class="peer cursor-pointer transition-all appearance-none rounded shadow hover:shadow-md border border-white bg-gray-200 checked:bg-primary"
        @click="emit('select')">
      <span class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-2.5 w-2.5"
          viewBox="0 0 20 20"
          fill="currentColor"
          stroke="currentColor"
          stroke-width="1">
          <path
            fill-rule="evenodd"
            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
            clip-rule="evenodd" />
        </svg>
      </span>
    </label>
    <FormLabel
      :for="id"
      :label="label"
      :required="required"
      class="cursor-pointer pt-[1px]">
      <slot />
    </FormLabel>
  </div>
</template>

<style scoped>

</style>
