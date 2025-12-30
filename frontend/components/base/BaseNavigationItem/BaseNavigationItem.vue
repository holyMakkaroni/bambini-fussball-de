<script setup lang="ts">
import type { BaseSidebarNavigationItem } from '~/types/components/base'

const props = defineProps({
  navigationItems: {
    type: Array as () => BaseSidebarNavigationItem[],
    required: true
  }
})

const { t } = useI18n()
const route = useRoute()
const parentStack = ref<BaseSidebarNavigationItem[][]>([])
const transitionDirection = ref<'slide-left'|'slide-right'>('slide-left')

// Computed: Current Active Level
const activeItems = computed(() => {
  if (parentStack.value.length === 0) {
    return props.navigationItems
  }
  return parentStack.value[parentStack.value.length - 1] // Last level in the stack
})

const navigationKey = computed(() => {
  return activeItems.value?.length ? activeItems.value[0].id : 0
})

// Navigate to child level
const handleItemClick = (item: BaseSidebarNavigationItem) => {
  if (item.children?.length) {
    parentStack.value.push(item.children)
    transitionDirection.value = 'slide-right'
  }
}

const handleBackClick = () => {
  if (parentStack.value.length > 0) {
    parentStack.value.pop()
    transitionDirection.value = 'slide-left'
  }
}

const findParentStack = (items: BaseSidebarNavigationItem[], targetUrl: string): BaseSidebarNavigationItem[][] => {
  const stack: BaseSidebarNavigationItem[][] = [items]

  const traverse = (currentItems: BaseSidebarNavigationItem[], depth: number): boolean => {
    for (const item of currentItems) {
      if (item.url === targetUrl) {
        return true
      }

      if (item.children.length > 0 && typeof item.url === 'string' && targetUrl.startsWith(item.url)) {
        if (depth + 1 >= stack.length) {
          stack.push(item.children)
        }
        if (traverse(item.children, depth + 1)) {
          return true
        }
      }
    }

    return false
  }

  traverse(items, 0)

  return stack
}

watch(() => route.path, () => {
  parentStack.value = findParentStack(props.navigationItems, route.path)
},
{ immediate: true }
)
</script>

<template>
  <div class="c-base-navigation-item">
    <div
      v-if="parentStack.length > 1"
      class="mb-8 px-8 h-[24px]">
      <button
        role="button"
        :title="t('components.base.navigationItem.back')"
        class="flex flex-row items-center"
        @click="handleBackClick">
        <BaseIcon
          class="w-2 h-2 rotate-180 mr-1"
          name="chevron" />
        <span class="flex-1 text-sm font-semibold underline underline-offset-2 decoration-1 decoration-secondary">{{ t('components.base.navigationItem.back') }}</span>
      </button>
    </div>

    <transition
      :name="transitionDirection"
      mode="out-in">
      <ul
        v-if="activeItems"
        :key="navigationKey"
        class="flex flex-col divide-y divide-solid divide-secondary-light border-t border-b border-secondary-light">
        <li
          v-for="(mainItem, index) in activeItems"
          :key="index"
          class="flex flex-row items-center pl-8 pr-4 py-4 cursor-pointer group transition-default hover:bg-primary"
          role="listitem"
          @click="mainItem.children.length ? handleItemClick(mainItem) : null">
          <div
            v-if="mainItem.children.length"
            class="flex-1 text-xl font-medium group-hover:decoration-transparent group-hover:text-white">
            {{ mainItem.name }}
          </div>
          <NuxtLink
            v-else
            :to="mainItem.url"
            :title="mainItem.name"
            class="flex-1 text-xl font-medium group-hover:decoration-transparent group-hover:text-white">
            {{ mainItem.name }}
          </NuxtLink>
          <div
            v-if="mainItem.children.length"
            class="flex flex-row">
            <BaseIcon
              name="chevron-thin"
              class="w-4 h-4 group-hover:text-white" />
          </div>
        </li>
      </ul>
    </transition>
  </div>
</template>

<style scoped>
</style>
