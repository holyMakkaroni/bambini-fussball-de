<script setup lang="ts">
import emblaCarouselVue from 'embla-carousel-vue'
import type { EmblaCarouselType } from 'embla-carousel'
import type { BaseCarousel } from '~/types/components/base'

const props = withDefaults(defineProps<BaseCarousel>(), {
  pagination: false,
  navigation: false
})

const emit = defineEmits(['select'])

const [carousel, emblaApi] = emblaCarouselVue(props.options)
const { t } = useI18n()

const selectedIndex = ref<number>(0)
const canScrollNext = ref<boolean>(false)
const canScrollPrev = ref<boolean>(false)
const totalPages = ref<number>(0)

const scrollPrev = () => {
  emblaApi.value?.scrollPrev()
}

const scrollNext = () => {
  emblaApi.value?.scrollNext()
}

const scrollTo = (index: number) => {
  emblaApi.value?.scrollTo(index)
}

const onSelect = (api: EmblaCarouselType) => {
  canScrollNext.value = api?.canScrollNext() || false
  canScrollPrev.value = api?.canScrollPrev() || false
  selectedIndex.value = api?.selectedScrollSnap() || 0

  emit('select', selectedIndex.value)
}

const updatePagination = (api: EmblaCarouselType) => {
  totalPages.value = Math.ceil(api.scrollSnapList().length / (api.slidesInView().length || 1))
}

onMounted(() => {
  if (!emblaApi.value) {
    return
  }

  if (props.initSlideIndex) {
    console.log(props.initSlideIndex)
    emblaApi.value.scrollTo(props.initSlideIndex)
  }

  onSelect(emblaApi.value)
  updatePagination(emblaApi.value)

  emblaApi.value?.on('select', onSelect)
  emblaApi.value?.on('reInit', onSelect)

  emblaApi.value?.on('resize', updatePagination)
  emblaApi.value?.on('reInit', updatePagination)
})
</script>

<template>
  <div class="c-base-carousel">
    <div
      ref="carousel"
      class="c-base-carousel-viewport">
      <div
        class="c-base-carousel-items"
        :class="props.itemsClass">
        <slot :item-class="props.itemClass" />
      </div>
    </div>
    <div
      v-if="props.navigation && totalPages > 1"
      class="c-base-carousel-navigation"
      :class="[props.navigationClass]">
      <slot
        name="navigation"
        :can-scroll-prev="canScrollPrev"
        :can-scroll-next="canScrollNext"
        :scroll-prev="scrollPrev"
        :scroll-next="scrollNext">
        <button
          :title="t('components.base.carousel.navigation.prev')"
          :disabled="!canScrollPrev"
          class="size-12 border border-secondary-light bg-white rounded-full flex justify-center items-center -translate-y-1/2 transition-default rotate-180 absolute -left-3 xl:-left-6"
          :class="{ 'opacity-100': canScrollPrev, 'opacity-0': !canScrollPrev }"
          @click="scrollPrev">
          <BaseIcon
            class="size-3"
            name="chevron" />
        </button>
        <button
          :title="t('components.base.carousel.navigation.next')"
          :disabled="!canScrollNext"
          class="size-12 border border-secondary-light bg-white rounded-full flex justify-center items-center -translate-y-1/2 transition-default absolute -right-3 xl:-right-6"
          :class="{ 'opacity-100': canScrollNext, 'opacity-0': !canScrollNext }"
          @click="scrollNext">
          <BaseIcon
            name="chevron"
            class="size-3" />
        </button>
      </slot>
    </div>
    <div
      v-if="props.pagination && totalPages > 1"
      class="c-base-carousel-pagination"
      :class="[props.paginationClass]">
      <slot
        name="pagination"
        :total-pages="totalPages"
        :scroll-to="scrollTo">
        <button
          v-for="(_, index) in totalPages"
          :key="index"
          :title="t('components.base.carousel.pagination.item', {
            item: index + 1
          })"
          class="flex justify-center items-center cursor-pointer size-6 rounded-full group transition-all duration-500 border-2 hover:border-primary"
          :class="{ 'border-primary': index === selectedIndex, 'border-transparent': index !== selectedIndex }"
          @click="scrollTo(index)">
          <span
            class="size-1.5 bg-default/75 rounded-full group-hover:bg-primary"
            :class="{ 'bg-primary': index === selectedIndex }" />
        </button>
      </slot>
    </div>
    <div v-if="props.thumbnails">
      <slot
        name="thumbnails"
        :scroll-to="scrollTo">
        Thumbnails
      </slot>
    </div>
  </div>
</template>

<style scoped></style>
