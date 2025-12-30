<script setup lang="ts">
import type { TypeAuthorStoryblok } from '~/types/storyblok'

const props = defineProps({
  author: {
    type: Object as PropType<TypeAuthorStoryblok>,
    required: true
  }
})

const { t } = useI18n()

const name = computed(() => props.author.name)
const notice = computed(() => props.author.content.notice)
const description = computed(() => props.author.content.description)
const image = computed(() => props.author.content.image)
</script>

<template>
  <div class="flex flex-col md:flex-row gap-10 py-10 md:py-14 px-5 bg-secondary-light">
    <div class="flex-1 flex items-center justify-center">
      <div
        v-if="image"
        class="size-[200px] rounded-full overflow-hidden">
        <BaseImage
          v-if="image?.filename"
          :lazy="true"
          :width="200"
          :height="200"
          sizes="100px"
          object-fit="cover"
          provider="storyblok"
          :image="image" />
      </div>
    </div>
    <div class="flex-1">
      <div class="flex flex-col mb-[30px]">
        <div
          v-if="notice"
          class="text-sm">
          {{ notice }}
        </div>
        <div class="text-xl font-semibold">
          {{ name }}
        </div>
      </div>
      <div
        v-if="description"
        class="line-clamp-4 leading-8">
        {{ description }}
      </div>
    </div>
    <div class="flex-1 flex md:justify-center md:items-center">
      <div class="w-full md:max-w-[300px]">
        <ul class="space-y-10">
          <li>
            <NuxtLink
              to="#"
              :title="t('components.base.authorBox.contact', { name: name })"
              class="flex items-center text-primary hover:text-black hover:decoration-black">
              <BaseIcon
                name="chat-2"
                class="size-8" />
              <span class="flex-1 ml-5">
                {{ t('components.base.authorBox.contact', {
                  name: name
                }) }}
              </span>
            </NuxtLink>
          </li>
          <li>
            <NuxtLink
              :to="`/${props.author.full_slug}`"
              :title="t('components.base.authorBox.posts', { name: name })"
              class="flex items-center text-primary hover:text-black hover:decoration-black">
              <BaseIcon
                name="document-2"
                class="size-8" />
              <span class="flex-1 ml-5">
                {{ t('components.base.authorBox.posts', {
                  name: name
                }) }}
              </span>
            </NuxtLink>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
