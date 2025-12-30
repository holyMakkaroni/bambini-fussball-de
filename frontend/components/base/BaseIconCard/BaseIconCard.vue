<script setup lang="ts">
import { pascalCase } from 'scule'
import type { PropType } from 'vue'
import type { StoryblokLink } from '~/types/storyblok.config'

const props = defineProps({
  variant: {
    type: String,
    default: 'normal',
    validation: (value: string) => {
      return ['small', 'normal'].includes(value)
    }
  },
  url: {
    type: Object as PropType<StoryblokLink>,
    default: null
  },
  icon: {
    type: String,
    default: null
  },
  headline: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: null
  }
})

function render () {
  const variantComponent = resolveComponent(pascalCase(`BaseIconCardVariant_${props.variant}`))
  const componentProps = ref({})

  switch (props.variant) {
    case ('normal'):
      componentProps.value = {
        url: props.url,
        icon: props.icon,
        headline: props.headline,
        description: props.description
      }
      break

    case ('small'):
      componentProps.value = {
        icon: props.icon,
        headline: props.headline
      }
      break
  }

  if (variantComponent && componentProps.value) {
    return h(variantComponent, componentProps.value)
  }
}
</script>

<template>
  <render />
</template>

<style scoped>

</style>
