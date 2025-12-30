<script setup lang="ts">
import type { PropType } from 'vue'
import type { Schemas } from '#shopware'

const props = defineProps({
  optionGroup: {
    type: Object as PropType<Schemas['PropertyGroup']>,
    default () {
      return {}
    }
  }
})

defineEmits(['option-select'])

const currentSelection = ref<string>('')

const {
  getSelectedOptions
} = useProductConfigurator()

currentSelection.value = getSelectedOptions.value[props.optionGroup.name]
</script>

<template>
  <div class="flex">
    <FormSelect
      v-model="currentSelection"
      :name="`${props.optionGroup.id}-select`"
      :rounded="false"
      class="max-w-80"
      :options="props.optionGroup.options?.map((option) => {
        return {
          label: option.translated.name,
          value: option.id
        }
      })"
      @change="$emit('option-select', {
        optionGroupName: props.optionGroup.name,
        optionId: currentSelection
      })" />
  </div>
</template>

<style scoped>

</style>
