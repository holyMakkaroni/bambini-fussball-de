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
  <div class="flex flex-wrap gap-4">
    <div
      v-for="option in optionGroup.options"
      :key="option.id"
      class="flex items-center cursor-pointer group"
      @click="$emit('option-select', {
        optionGroupName: optionGroup.name,
        optionId: option.id
      })">
      <div
        class="relative w-8 h-8 rounded-full border-2 border-secondary-light transition-default"
        :class="{
          'group-hover:bg-white group-hover:border-secondary': !isOptionSelected(getSelectedOptions, option.id),
        }"
        :style="`background-color: ${option.colorHexCode}`">
        <div
          v-if="isOptionSelected(getSelectedOptions, option.id)"
          class="absolute bg-white size-4 rounded-full translate-x-1.5 translate-y-1.5 flex justify-center items-center">
          <BaseIcon
            name="check"
            class="size-2" />
        </div>
        <span class="sr-only">{{ option.translated.name }}</span>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
