<script setup lang="ts">
defineProps({
  blok: {
    type: Object,
    default: null
  },
  container: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <BaseSectionWrapper
    v-editable="blok"
    identifier="c-base-top-brands"
    :title="blok.title"
    :link="blok.link"
    :container="container"
    :label="blok.label">
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4">
      <NuxtLink
        v-for="(brand, index) in 12"
        :key="index"
        draggable="false"
        to="#"
        title="Aimpoint"
        class="flex-shrink-0 text-nowrap transition-default hover:scale-110 hover:box-shadow">
        <BaseBorderedCard padding="p-0">
          <figure class="c-base-image">
            <picture>
              <img
                width="420"
                height="210"
                alt="Aimpoint"
                loading="lazy"
                style="object-fit: contain;"
                title="Aimpoint"
                data-nuxt-pic=""
                src="https://a.storyblok.com/f/277886/420x210/441ee21057/brand-aimpoint.png/m/smart/filters:quality(80)"
                sizes="120px">
            </picture>
          </figure>
        </BaseBorderedCard>
      </NuxtLink>
    </div>
  </BaseSectionWrapper>
</template>
