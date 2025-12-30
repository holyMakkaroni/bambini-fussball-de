<script setup lang="ts">
import { useConfigStore } from '~/stores/config'
import type { StoryblokDefaultConfig } from '~/types/storyblok.config'
import type { AppFooter } from '~/types/components/app'

withDefaults(defineProps<AppFooter>(), {
  showNewsletter: true,
  showPreFooter: true
})

const config = useAppConfig()
const { storyblokUrl } = useStoryblokHelper()
const configStore = useConfigStore()
const configDefault: StoryblokDefaultConfig | null = configStore.defaultConfig

const currentYear = computed(() => {
  const date = new Date()
  return date.getFullYear()
})

const footerColumns = computed(() => {
  return configDefault?.content?.footer_columns
})

const footerLinkItems = computed(() => {
  return configDefault?.content?.footer_links
})

const notice = computed(() => {
  return configDefault?.content?.notice
})
</script>

<template>
  <footer class="flex flex-col margin-default">
    <BaseNewsletter v-if="showNewsletter" />

    <div
      v-if="showPreFooter"
      class="bg-secondary-light py-8 md:py-14">
      <div
        v-if="configDefault?.content?.teaser"
        class="container">
        <div
          v-if="configDefault?.content?.teaser_headline"
          class="text-center mb-8 font-semibold">
          {{ configDefault?.content?.teaser_headline }}
        </div>
        <div class="flex flex-col">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <BaseIconCard
              v-for="teaser in configDefault?.content?.teaser"
              :key="teaser._uid"
              v-editable="teaser"
              :icon="teaser.icon"
              :headline="teaser.headline"
              :url="teaser.link ?? null"
              :description="teaser.description" />
          </div>
        </div>
      </div>

      <div
        v-if="configDefault?.content?.payment"
        class="container margin-default">
        <div
          v-if="configDefault?.content?.payment_headline"
          class="text-center mb-8 font-semibold">
          {{ configDefault?.content?.payment_headline }}
        </div>
        <div class="flex flex-col">
          <div class="w-full flex flex-col xs:flex-row xs:flex-wrap justify-center gap-4">
            <BaseIconCard
              v-for="payment in configDefault?.content?.payment"
              :key="payment._uid"
              v-editable="payment"
              class="flex-shrink-0 w-full xs:w-[calc(50%_-_0.75rem)] sm:w-[198px]"
              variant="small"
              :icon="payment.icon"
              :headline="payment.headline" />
          </div>
        </div>
      </div>

      <div
        v-if="configDefault?.content?.partner"
        class="container margin-default">
        <div
          v-if="configDefault?.content?.partner_headline"
          class="text-center mb-8 font-semibold">
          {{ configDefault?.content?.partner_headline }}
        </div>
        <div class="flex flex-col">
          <div class="w-full flex flex-col xs:flex-row xs:flex-wrap justify-center gap-4">
            <BaseIconCard
              v-for="partner in configDefault?.content?.partner"
              :key="partner._uid"
              v-editable="partner"
              class="flex-shrink-0 w-full xs:w-[calc(50%_-_0.75rem)] sm:w-[198px]"
              variant="small"
              :icon="partner.icon"
              :headline="partner.headline" />
          </div>
        </div>
      </div>
    </div>

    <div class="bg-secondary text-white/50 py-8 md:py-14">
      <div class="container pb-10 md:pb-12">
        <div class="flex flex-col md:flex-row md:gap-x-5">
          <div
            v-for="(column, index) in footerColumns"
            :key="column._uid"
            v-editable="column"
            class="flex-1"
            :class="{'mb-6 md:mb-0': index !== 3}">
            <div class="text-primary font-semibold text-xl mb-1 sm:mb-2">
              {{ column.title }}
            </div>
            <ul class="flex flex-col gap-1">
              <li
                v-for="item in column.links"
                :key="item._uid"
                v-editable="item"
                role="listitem">
                <NuxtLink
                  :to="storyblokUrl(item.link)"
                  :target="item.link.target"
                  class="text-white underline decoration-white">
                  {{ item.title }}
                </NuxtLink>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div
        class="container border-t border-white/15 pt-6 md:pt-8"
        :class="{'border-b pb-6 md:pb-8': notice}">
        <div class="flex flex-col md:flex-row md:gap-x-5">
          <div class="flex-1 mb-2 md:mb-0">
            <BaseLogomark class="size-9 text-primary" />
          </div>

          <div class="flex-1 mb-2 md:mb-0">
            <ul class="flex flex-col flex-wrap md:flex-row lg:items-center gap-y-1 md:gap-x-5 h-full">
              <li
                v-for="item in footerLinkItems"
                :key="item._uid"
                v-editable="item"
                role="listitem">
                <NuxtLink
                  :to="storyblokUrl(item.link)"
                  :target="item.link.target"
                  class="text-xs text-white underline decoration-0 decoration-white">
                  {{ item.title }}
                </NuxtLink>
              </li>
              <li>
                <button
                  role="button"
                  :title="$t('components.app.general.labels.cookieSettings')"
                  class="text-xs text-white underline decoration-0 decoration-white underline-offset-4 transition-default hover:text-primary hover:decoration-primary">
                  {{ $t('components.app.general.labels.cookieSettings') }}
                </button>
              </li>
            </ul>
          </div>

          <div class="flex-1">
            <div class="flex h-full items-center text-white/50 text-xs">
              &copy; 2012 – {{ currentYear }} by {{ config.name }}
            </div>
          </div>
        </div>
      </div>
      <div
        v-if="notice"
        class="container pt-6 md:pt-8">
        <div class="col-span-12">
          <BaseText
            :text="notice"
            class="text-white/50 text-xs [&>p]:mb-1 [&>p]:leading-4 [&_a]:underline [&_a]:decoration-0 [&_a]:decoration-white/50 [&_a:hover]:decoration-primary" />
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>

</style>
