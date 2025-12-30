<script setup lang="ts">
const model = defineModel<string|number>()

defineProps({
  options: {
    type: Array<{
      [key: string]: number | string,
    }>,
    required: true,
    default () {
      return []
    }
  },
  name: {
    type: String,
    required: true
  },
  labelBy: {
    type: String,
    default: 'label'
  },
  valueBy: {
    type: String,
    default: 'value'
  },
  rounded: {
    type: Boolean,
    default: true
  },
  selectClass: {
    type: String,
    default: ''
  },
  variant: {
    type: String,
    default: 'big',
    validator: (value: string) => ['big', 'small'].includes(value)
  },
  required: {
    type: Boolean,
    default: false
  },
  label: {
    type: String,
    default: null
  }
})
</script>

<template>
  <div class="c-form-select w-full">
    <FormLabel
      :for="name"
      :label="label ?? name"
      :required="required"
      :class="{'sr-only': !label}" />
    <div class="relative flex flex-col">
      <select
        :id="name"
        v-model="model"
        class="h-full bg-white appearance-none hover:border-secondary w-full focus:outline-none"
        :class="[{
                   'rounded-md': rounded
                 },
                 selectClass,
                 variant === 'big' ? 'py-2 pl-3 pr-7 text-sm' : 'py-0.5 px-4 text-xs',
                 model ? 'border-2 border-secondary' : 'border border-secondary-light'
        ]">
        <option
          v-for="(option, index) in options"
          :key="index"
          :value="option[valueBy]">
          {{ option[labelBy] }}
        </option>
      </select>
    </div>
  </div>
</template>

<style scoped>

</style>
