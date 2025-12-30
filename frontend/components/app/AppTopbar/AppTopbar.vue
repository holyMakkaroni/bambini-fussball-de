<script setup lang="ts">
import { useConfigStore } from '~/stores/config'
import type { StoryblokDefaultConfig } from '~/types/storyblok.config'

const average = ref<number>(4.63)
const configStore = useConfigStore()
const configDefault: StoryblokDefaultConfig | null = configStore.defaultConfig

const usps = computed(() => {
  return configDefault?.content?.topbar_usps
})
</script>

<template>
  <div class="c-app-topbar bg-gray-100 text-xs">
    <div class="container">
      <BaseUsp
        class="h-[30px]"
        :step-interval="3000"
        :responsive="{ 600: 1, 768: 2, 1024: 3, default: 4 }">
        <template #step-0>
          <div class="flex items-center h-full">
            <BaseReviewStars
              class="mr-2 text-success"
              icon-class="w-3 h-3"
              :average="average" />
            <div class="flex-1 text-nowrap pt-0.5">
              <b>{{ average }} / 5 "{{ $t('components.app.topbar.review.rating') }}"</b>
              <i18n-t
                scope="global"
                keypath="components.app.topbar.review.label">
                <template #reviewCount>
                  1074
                </template>
              </i18n-t>
            </div>
          </div>
        </template>

        <template
          v-for="(usp, index) in usps"
          :key="usp._uid"
          #[`step-${index+1}`]>
          <div
            v-editable="usp"
            class="flex items-center">
            <BaseIcon
              v-if="usp.icon"
              :name="usp.icon"
              class="w-3 h-3 mr-2 text-success" />
            <BaseText
              class="flex-1 text-nowrap pt-0.5"
              :text="usp.title" />
          </div>
        </template>
      </BaseUsp>
    </div>
  </div>
</template>

<style scoped>

</style>
