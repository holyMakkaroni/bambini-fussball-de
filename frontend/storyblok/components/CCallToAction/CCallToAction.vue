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

const { t } = useI18n()
const { storyblokUrl } = useStoryblokHelper()
</script>

<template>
  <div
    v-editable="blok"
    class="c-call-to-action margin-default bg-secondary-light">
    <div
      class="flex overflow-hidden relative"
      :class="{'container': container}">
      <div class="hidden lg:flex flex-1 max-w-[300px]">
        <BaseIcon
          v-if="blok.icon_left"
          :name="blok.icon_left"
          class="flex w-[200px] lg:items-center size-full text-primary" />
      </div>
      <div class="max-w-[600px] pt-14 pb-20 z-10">
        <BaseHeadline
          tag="div"
          :title="blok.headline"
          custom-class="text-primary text-2xl md:text-3xl !mb-0" />
        <div
          v-if="blok.description"
          class="nl2br">
          {{ blok.description }}
        </div>
        <BaseButton
          v-if="storyblokUrl(blok.link)"
          class="mt-4 md:mt-6"
          outline
          :href="storyblokUrl(blok.link)"
          :title="blok.label ? blok.label : t('components.app.general.labels.discoverNow')"
          :target="blok.link.target">
          {{ blok.label ? blok.label : t('components.app.general.labels.discoverNow') }}
        </BaseButton>
      </div>
      <BaseIcon
        v-if="blok.icon_right"
        :name="blok.icon_right"
        class="absolute z-0 size-96 text-primary transform opacity-15 md:opacity-100 -right-28 md:-right-24 top-0 scale-125 rotate-[15deg] origin-top-right" />
    </div>
  </div>
</template>
