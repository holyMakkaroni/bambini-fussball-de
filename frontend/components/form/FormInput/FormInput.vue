<script setup lang="ts">
import type { FormInput } from '@/types/components/form'

withDefaults(defineProps<FormInput>(), {})

const emit = defineEmits(['update:modelValue', 'is-focused'])

const isFocused = ref<boolean>(false)

const setFocus = (focus: boolean) => {
  isFocused.value = focus

  emit('is-focused', isFocused)
}
</script>

<template>
  <div
    class="c-form-input"
    :class="{ 'active': isFocused }">
    <FormLabel
      v-if="label"
      :for="id"
      :label="label"
      :required="required" />
    <input
      :id="id"
      :class="{'with-icon': withIcon}"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :autocomplete="autocomplete"
      @focus="setFocus(true)"
      @click="setFocus(true)"
      @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)">
  </div>
</template>
