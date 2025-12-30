<script setup lang="ts">
import { Fragment, h } from 'vue'
import type { BaseHeadline } from '~/types/components/base'

const props = withDefaults(defineProps<BaseHeadline>(), {
  tag: 'div',
  anchor: false,
  customClass: ''
})

const { generateUrl } = useUtilities()

const render = () => {
  const headingClasses = {
    'c-base-headline headline': true,
    'font-semibold': props.tag.includes('h'),
    'text-primary': props.primary,
    [props.customClass]: !!props.customClass
  }

  const anchor = props.anchor && props.tag === 'h2'
    ? h('div', {
      id: `${generateUrl(props.title)}`
    })
    : null

  const heading = h(props.tag, {
    class: headingClasses
  }, props.title)

  return h(Fragment, {}, [
    anchor,
    heading
  ])
}

</script>

<template>
  <render v-if="props.title" />
</template>

<style scoped>

</style>
