<script setup lang="ts">
import type { PropType } from 'vue'
import type { ISbRichtext } from 'storyblok-js-client'

const props = defineProps({
  text: {
    type: Object as PropType<ISbRichtext>,
    required: true
  }
})

const textRendered = computed(() => renderRichText(props.text))
</script>

<template>
  <div
    class="c-base-text"
    v-html="textRendered" />
</template>
