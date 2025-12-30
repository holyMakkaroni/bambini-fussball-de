<script setup lang="ts">
import { onClickOutside } from '@vueuse/core'
import { pascalCase } from 'scule'
import type { RefinementListItem } from 'instantsearch.js/es/connectors/refinement-list/connectRefinementList'

const props = defineProps({
  name: { type: String, required: true },
  variant: {
    type: String,
    required: true,
    default: 'text',
    validation: (value: string) => ['text', 'select', 'media', 'color'].includes(value)
  },
  items: {
    type: Array as PropType<RefinementListItem[]>,
    required: true
  },
  canToggleShowMore: { type: Boolean, default: false },
  attribute: { type: String, default: null },
  createUrl: { type: Function, required: true },
  isMobile: { type: Boolean, default: false }
})

const emit = defineEmits(['refine'])

const isFilterVisible = ref(false)
const isMobileFilterVisible = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)
const filterRef = ref<HTMLElement | null>(null)

const active = computed(() => props.items.some(item => item.isRefined))
const disabled = computed(() => props.items.length < 1)

const toogleFilter = () => {
  isFilterVisible.value = !isFilterVisible.value
}

const toggleMobileFilter = () => {
  isMobileFilterVisible.value = !isMobileFilterVisible.value
}

onClickOutside(filterRef, () => {
  isFilterVisible.value = false
})

const loadComponent = () => {
  return resolveComponent(pascalCase(`BaseFilterSelectVariant_${props.variant}`))
}

const onSelect = (item: object) => {
  emit('refine', item)
}

const teleportStyles = ref<Record<string, string>>({})

const updateTeleportPosition = () => {
  const el = dropdownRef.value
  if (!el) { return }

  const rect = el.getBoundingClientRect()
  const dropdownWidth = rect.width

  const left = rect.left + window.scrollX

  teleportStyles.value = {
    position: 'absolute',
    top: `${rect.bottom + window.scrollY}px`,
    left: `${left}px`,
    width: `${dropdownWidth}px`
  }
}

watch(isFilterVisible, (visible) => {
  if (visible) {
    nextTick(() => {
      updateTeleportPosition()
    })
  }
})

onMounted(() => {
  window.addEventListener('resize', updateTeleportPosition)
  window.addEventListener('scroll', updateTeleportPosition, true)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateTeleportPosition)
  window.removeEventListener('scroll', updateTeleportPosition, true)
})
</script>

<template>
  <div
    v-if="items.length > 1"
    ref="dropdownRef"
    class="c-base-filter">
    <div
      v-if="!isMobile"
      class="relative">
      <div
        class="flex items-center gap-x-2 border border-secondary-light rounded-md px-3 py-1.5 text-sm whitespace-nowrap"
        :class="{
          'bg-secondary-light': active,
          'hover:bg-secondary-light/30': !active,
          'cursor-not-allowed': disabled,
          'cursor-pointer': !disabled
        }"
        @click="!disabled ? toogleFilter() : null">
        <div class="flex-1">
          {{ name }}
        </div>
        <slot name="clear">
          <BaseIcon
            name="close"
            class="size-2 text-white" />
        </slot>
        <BaseIcon
          name="chevron"
          class="size-2 rotate-90" />
      </div>

      <Teleport to="#teleports">
        <Transition>
          <div
            v-if="isFilterVisible"
            ref="filterRef"
            :style="teleportStyles"
            class="flex flex-col absolute left-0 -bottom-2 translate-y-2 bg-white w-full min-w-[375px] max-h-[300px] overflow-y-scroll box-shadow p-5 z-40">
            <div class="flex justify-content-between mb-11 md:hidden">
              <div class="flex-1 font-semibold text-sm flex items-center">
                {{ name }}
              </div>
              <button @click="toogleFilter()">
                <BaseIcon
                  name="close"
                  class="size-8 hover:text-primary" />
              </button>
            </div>
            <component
              :is="loadComponent()"
              :items="items"
              class="overflow-y-scroll"
              :create-url="createUrl"
              @select="onSelect" />
          </div>
        </Transition>
      </Teleport>
    </div>

    <!-- Mobile Variante bleibt unverändert -->
    <div
      v-else
      class="flex flex-col mt-4 border-b-[1px] border-secondary-light pb-4">
      <div
        class="flex items-center"
        @click="toggleMobileFilter">
        <div class="flex-1 font-semibold text-sm">
          {{ name }}
        </div>
        <BaseIcon
          name="chevron-thin"
          class="size-4 transition-default"
          :class="{ 'rotate-90': isMobileFilterVisible }" />
      </div>
      <Transition name="notification">
        <div v-if="isMobileFilterVisible">
          <component
            :is="loadComponent()"
            :items="items"
            :create-url="createUrl"
            class="pt-3"
            @select="onSelect" />
        </div>
      </Transition>
    </div>
  </div>
</template>
