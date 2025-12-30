<script setup lang="ts">
import type { BaseButton } from '@/types/components/base'

withDefaults(defineProps<BaseButton>(), {
  variant: 'primary',
  size: 'normal'
})
</script>

<template>
  <NuxtLink
    v-if="href"
    :to="href"
    :title="title"
    :target="target"
    class="c-base-button hover:no-underline"
    :class="[variant, {
      'outline': outline
    }, size, customClass]">
    <span class="flex h-full items-center">
      <BaseIcon
        v-if="icon"
        :class="[
          {
            'mr-2': iconPosition === 'left',
            'ml-2 order-1': iconPosition === 'right'
          },
          iconClass
        ]"
        :name="icon" />
      <span class="flex-1 font-semibold">
        <slot />
      </span>
    </span>
  </NuxtLink>

  <button
    v-else
    role="button"
    :title="title"
    class="c-base-button"
    :class="[variant, {
      'outline': outline
    }, size, customClass]">
    <span
      class="flex h-full items-center"
      :class="[
        {
          'w-full': fullWidth,
          'text-left': iconPosition === 'right',
          'text-right': iconPosition === 'left',
        }
      ]">
      <BaseIcon
        v-if="icon"
        :class="[
          {
            'mr-2': iconPosition === 'left',
            'ml-2 order-1': iconPosition === 'right'
          },
          iconClass
        ]"
        :name="icon" />
      <span class="flex-1 font-semibold">
        <slot>Label</slot>
      </span>
    </span>
  </button>
</template>
