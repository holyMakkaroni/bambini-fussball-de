<script setup lang="ts">
import { filename } from 'pathe/utils'
import type { BaseIcon } from '~/types/components/base'

const props = defineProps<BaseIcon>()

const glob = import.meta.glob('~/assets/icons/*.svg', {
  eager: true
})

const icons = Object.fromEntries(
  Object.entries(glob).map(([key, value]) => [filename(key), value.default])
)

const icon = computed(() => icons[props.name])
</script>

<template>
  <div
    v-if="icon"
    class="c-base-icon fill-current">
    <component :is="icon" />
  </div>
</template>
