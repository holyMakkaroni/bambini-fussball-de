<script setup lang="ts">
import type { PropType } from 'vue'
import type { Schemas } from '#shopware'

defineProps({
  optionGroup: {
    type: Object as PropType<Schemas['PropertyGroup']>,
    default () {
      return {}
    }
  }
})

defineEmits(['option-select'])

const {
  getSelectedOptions
} = useProductConfigurator()

const { isOptionSelected } = useShopwareHelper()
</script>

<template>
  <div class="grid gap-4 grid-cols-2 xs:grid-cols-4 xl:grid-cols-6">
    <div
      v-for="option in optionGroup.options"
      :key="option.id"
      class="cursor-pointer bg-secondary-light border text-xxs text-center p-1 transition-default hover:bg-white hover:border-secondary"
      :class="{
        'border-2 bg-white border-secondary': isOptionSelected(getSelectedOptions, option.id),
        'border-secondary-light': !isOptionSelected(getSelectedOptions, option.id)
      }"
      @click="$emit('option-select', {
        optionGroupName: optionGroup.name,
        optionId: option.id
      })">
      {{ option.translated.name }}
    </div>
  </div>
</template>

<style scoped>

</style>
