<script setup lang="ts">
import { useBreadcrumbStore } from '@/stores/breadcrumb'

defineProps({
  light: {
    type: Boolean,
    default: false
  }
})

const breadcrumbStore = useBreadcrumbStore()

const { isPenultimate } = useBreadcrumbHelper()
</script>

<template>
  <div v-if="breadcrumbStore.breadcrumb && breadcrumbStore.breadcrumb.length">
    <div
      class="c-app-breadcrumb container z-[1]"
      aria-label="Breadcrumb">
      <div class="relative py-2 md:pt-4 md:mt-5">
        <div class="hidden md:flex w-[1px] h-[89px] -translate-y-[20px] bg-secondary-light absolute left-0 bottom-0" />
        <div class="flex h-full">
          <div class="hidden md:flex w-4 mr-2 relative">
            <div class="w-full h-[1px] bg-secondary-light left-0 top-1/2 absolute" />
            <div class="w-full h-full bg-transparent absolute top-1/2 -left-[1px] translate-y-[1px]" />
          </div>
          <ol
            role="list"
            itemscope
            itemtype="https://schema.org/BreadcrumbList"
            class="flex-1 flex gap-x-2">
            <li
              v-for="(breadcrumb, index) in breadcrumbStore.breadcrumb"
              :key="index"
              role="listitem"
              itemprop="itemListElement"
              itemscope
              itemtype="https://schema.org/ListItem"
              :class="{'go-back': isPenultimate(breadcrumb)}">
              <span
                class="hidden md:block mr-1.5"
                :class="{'text-base md:text-white': light, 'text-base': !light}">»</span>
              <span
                v-if="breadcrumbStore.breadcrumb.length > 1 && isPenultimate(breadcrumb)"
                class="w-3.5 mr-2 rotate-180 block md:hidden">
                <BaseIcon name="chevron" />
              </span>
              <NuxtLink
                v-if="breadcrumb.path"
                itemprop="item"
                :to="breadcrumb.path"
                :class="{'text-base md:text-white': light}">
                <span
                  class="label"
                  itemprop="name">
                  {{ breadcrumb.name }}
                </span>
                <meta
                  itemprop="position"
                  :content="index.toString()">
              </NuxtLink>
              <span
                v-else
                class="label"
                :class="{'md:text-white': light}"
                itemprop="name">
                {{ breadcrumb.name }}
              </span>
              <meta
                itemprop="position"
                :content="index.toString()">
            </li>
          </ol>
        </div>
      </div>
    </div>
    <div class="md:hidden h-[1px] w-full bg-secondary-light" />
  </div>
</template>

<style scoped>

</style>
