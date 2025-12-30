<script setup lang="ts">
import type { BaseSidebarNavigationItem } from '~/types/components/base'

const props = defineProps({
  item: {
    type: Object as PropType<BaseSidebarNavigationItem>,
    required: true
  }
})

const route = useRoute()
const showChildren = ref<boolean>(false)

const toggleChildren = () => {
  showChildren.value = !showChildren.value
}

const linkActive = (link: BaseSidebarNavigationItem): boolean => {
  return (
    link.url === route.path ||
    link.children?.some(child => linkActive(child))
  )
}

const hasChildren = computed(() => props.item.children && props.item.children.length > 0)
const isActive = computed(() => linkActive(props.item) || showChildren.value)
</script>

<template>
  <li>
    <NuxtLink
      v-if="item.url"
      :to="item.url"
      class="flex items-center hover:no-underline mb-5"
      :class="{'text-primary': isActive}">
      <span>{{ item.name }}</span>
      <BaseIcon
        v-if="hasChildren"
        name="chevron-thin"
        class="size-2 ml-2 transition-transform duration-300"
        :class="{'rotate-90': isActive}" />
    </NuxtLink>
    <div
      v-else
      class="flex items-center cursor-pointer transition-all duration-300 ease-in-out decoration-1 decoration-transparent underline-offset-4 hover:text-primary font-semibold mb-5"
      :class="{'text-primary': isActive}"
      @click="toggleChildren">
      <div>{{ item.name }}</div>
      <BaseIcon
        name="chevron-thin"
        class="size-2 ml-2 transition-transform duration-300"
        :class="{'rotate-90': isActive}" />
    </div>
    <ul
      v-if="hasChildren && isActive"
      class="ml-2.5 space-y-4">
      <BaseSidebarItem
        v-for="child in item.children"
        :key="child.id"
        class="font-normal"
        :item="child" />
    </ul>
  </li>
</template>

<style scoped>

</style>
