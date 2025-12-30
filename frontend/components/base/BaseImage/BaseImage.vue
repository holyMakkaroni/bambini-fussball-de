<script setup lang="ts">
import type { StoryblokFocalPointCoordinates } from '~/types/storyblok.config'

const props = defineProps({
  image: {
    type: Object,
    required: true
  },
  provider: {
    type: String,
    default: 'storyblok',
    validator (value: string) {
      return ['', 'gumlet', 'storyblok'].includes(value)
    }
  },
  width: {
    type: Number,
    default: null
  },
  height: {
    type: Number,
    default: null
  },
  fit: {
    type: String,
    default: null
  },
  objectFit: {
    type: String,
    default: null
  },
  imgClass: {
    type: String,
    default: ''
  },
  sizes: {
    type: String,
    default: '300px sm:640px md:768px lg:1024px'
  },
  showCaption: {
    type: Boolean
  },
  lazy: {
    type: Boolean,
    default: false
  },
  clipPath: {
    type: String,
    default: null
  },
  fetchPriority: {
    type: String,
    default: null
  },
  decoding: {
    type: String,
    default: 'async'
  }
})

const { focalPosition } = useStoryblokHelper()
const style = ref({})
const modifiers = ref({})
const objectPosition = ref<StoryblokFocalPointCoordinates>({
  x: 0,
  y: 0
})

switch (props.provider) {
  case 'storyblok':
    if (props.image?.focus) {
      objectPosition.value = focalPosition({
        filename: props.image.filename,
        focus: props.image.focus
      })
    }

    if (props.image?.focus) {
      modifiers.value = {
        filters: {
          focal: props.image?.focus
        },
        smart: true
      }
    }
    break
  default:
    break
}

style.value = {
  'object-fit': props.objectFit ?? 'contain'
}

if (props.clipPath) {
  style.value = {
    ...style.value,
    'clip-path': `url(#clip-${props.clipPath})`
  }
}

if (props.objectFit === 'cover') {
  style.value = {
    ...style.value,
    'object-position': `${objectPosition.value.x}% ${objectPosition.value.y}%`,
    width: '100%',
    height: '100%'
  }
}

const showCaption = computed(() => {
  return props.showCaption
})

</script>

<template>
  <div
    v-if="props.image"
    class="c-base-image">
    <figure v-if="props.image.filename">
      <NuxtPicture
        v-bind="{ ...(props.provider && { provider: props.provider }), ...(props.lazy && { loading: 'lazy' }), ...(props.fit && { fit: props.fit }) }"
        :class="{'flex-1': !showCaption}"
        :width="props.width"
        :height="props.height"
        :sizes="props.sizes"
        :modifiers="modifiers"
        :src="props.image.filename"
        :img-attrs="{ class: props.imgClass, style: style, title: props.image.title ?? props.image.name, alt: props.image.alt ?? props.image.name, draggable: false, fetchpriority: props.fetchPriority, decoding: props.decoding }" />
      <figcaption v-if="showCaption">
        <span
          v-if="props.image.name"
          class="block">
          {{ props.image.name }}
        </span>
      </figcaption>
    </figure>
    <BasePlaceholderImage v-else />
  </div>
</template>
