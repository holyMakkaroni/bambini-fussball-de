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
      class="flex flex-col group cursor-pointer"
      @click="$emit('option-select', {
        optionGroupName: optionGroup.name,
        optionId: option.id
      })">
      <BaseBorderedCard
        v-if="option.media"
        class="!p-0.5 group-hover:border-secondary"
        :class="{
          'border-2 !border-secondary': isOptionSelected(getSelectedOptions, option.id),
        }">
        <BaseImage
          :width="90"
          :height="90"
          provider="gumlet"
          sizes="90"
          :lazy="true"
          :image="{
            name: option.name,
            title: option.media.title,
            alt: option.media.alt,
            filename: option.media.path
          }" />
      </BaseBorderedCard>
      <div class="text-xxs text-center">
        {{ option.translated.name }}
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
