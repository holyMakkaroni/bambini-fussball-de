<script setup lang="ts">
import type { ManufacturerLexicon } from '~/types/manufacturers'

defineProps<{ title: string, manufacturerLexicon: ManufacturerLexicon }>()

const localePath = useLocalePath()
</script>

<template>
  <div class="c-base-lexicon-item">
    <BaseDivider
      variant="solid"
      class="mt-12" />
    <div class="flex flex-col pt-10">
      <div class="font-semibold text-3xl mb-8">
        {{ title }}
      </div>
      <div
        v-if="manufacturerLexicon.media?.length"
        class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4 mb-8">
        <NuxtLink
          v-for="(media, index) in manufacturerLexicon.media"
          :key="index"
          draggable="false"
          :to="localePath({
            name: 'brand-slug',
            params: {
              slug: (media.name).toLowerCase()
            }
          })"
          title="Aimpoint"
          class="flex-shrink-0 text-nowrap transition-default hover:scale-110 hover:box-shadow">
          <BaseBorderedCard padding="p-0">
            <figure class="c-base-image">
              <picture>
                <img
                  width="420"
                  height="210"
                  loading="lazy"
                  style="object-fit: contain;"
                  :src="media.media.url"
                  :alt="media.media.alt"
                  :title="media.media.title"
                  sizes="120px">
              </picture>
            </figure>
          </BaseBorderedCard>
        </NuxtLink>
      </div>

      <ul class="columns-2 sm:columns-3 md:columns-4">
        <li
          v-for="(brand, index) in manufacturerLexicon.brands"
          :key="index"
          class="leading-8">
          <NuxtLink
            :to="localePath({
              name: 'brand-slug',
              params: {
                slug: (brand.name).toLowerCase()
              }
            })">
            {{ brand.name }}
          </NuxtLink>
        </li>
      </ul>
    </div>
  </div>
</template>

<style scoped>

</style>
