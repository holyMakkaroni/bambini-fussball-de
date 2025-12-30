<script setup lang="ts">
import type { PropType } from 'vue'
import type { Schemas } from '#shopware'
import { useProductReviewHelper } from '~/composables/useProductReviewHelper'

const props = defineProps({
  product: {
    type: Object as PropType<Schemas['Product']>,
    required: true
  },
  productReviews: {
    type: Array as PropType<Schemas['ProductReview'][]|null>,
    required: true
  }
})

const ratingAverage = computed(() => props.product?.ratingAverage ?? 0)
const { locale, t } = useI18n()

const { productReviewMatrix } = useProductReviewHelper(props.productReviews)

const format: Intl.DateTimeFormatOptions = {
  year: 'numeric',
  month: '2-digit',
  day: '2-digit'
}

const formatDate = (date: string) =>
  new Date(date).toLocaleDateString(locale.value, format)

const newestReview = computed(() => {
  if (props.productReviews && props.productReviews.length === 1) {
    return null
  }

  return props.productReviews ? props.productReviews[0] : null
})
</script>

<template>
  <div
    v-if="props.productReviews?.length"
    id="reviews"
    class="container mt-8 md:mt-28">
    <div class="flex flex-col text-center">
      <BaseHeadline
        tag="div"
        custom-class="h1-styling !mb-0"
        :title="t('components.base.productReviews.headline', {
          count: props.productReviews?.length
        })" />
      <div>{{ product.translated.name }}</div>
    </div>

    <div
      v-if="newestReview"
      class="flex justify-center items-center mt-9">
      <div class="text-xl md:text-[40px] font-light w-full max-w-5xl md:leading-normal">
        <q>{{ newestReview.content }}</q>
      </div>
    </div>

    <div class="my-4 md:my-14">
      <BaseDivider variant="solid" />
    </div>

    <div class="flex flex-col justify-center">
      <div>
        <BaseReviewStars
          class="text-amber-300"
          :average="ratingAverage"
          icon-class="w-8 md:w-12"
          suffix-class="ml-4 mt-2">
          <div class="text-secondary text-4xl md:text-5xl">
            <span class="font-bold">{{ parseFloat(ratingAverage.toFixed(2)) }}</span>
            <span class="text-xl md:text-2xl pl-1.5">/ 5</span>
          </div>
        </BaseReviewStars>
        <BaseDivider
          class="my-4 md:my-8"
          variant="dashed" />
      </div>

      <ul class="flex flex-col gap-y-5">
        <li
          v-for="(matrix, index) in productReviewMatrix"
          :key="index"
          class="flex-1 flex gap-x-3 items-center">
          <div class="min-w-20">
            {{ t('components.base.productReviews.star', { count: matrix.points }) }}
          </div>
          <div class="flex-1 relative border border-secondary-light h-full max-h-5">
            <div
              v-if="matrix.percent"
              class="bg-amber-300 absolute left-[-1px] top-[-1px] bottom-[-1px]"
              :style="`right: calc(100% - ${matrix.percent}%)`" />
            <div class="absolute right-1 top-1/2 -translate-y-1/2 text-amber-300 mix-blend-screen text-xs">
              {{ matrix.elements.length ?? 0 }}
            </div>
          </div>
        </li>
      </ul>
    </div>

    <div class="col-start-1 col-end-13 md:col-start-8 md:col-end-12">
      <BaseHeadline
        tag="div"
        custom-class="h5-styling flex items-end min-h-[56.5px]"
        :title="t('components.base.productReviews.rateProduct')" />
      <BaseDivider
        class="my-4 md:my-8"
        variant="dashed" />
      <div class="flex flex-col">
        <span class="font-bold">
          {{ t('components.base.productReviews.rateProductHeadline') }}
        </span>
        <span>
          {{ t('components.base.productReviews.rateProductDescription') }}
        </span>

        <BaseButton
          class="mt-3"
          variant="secondary"
          :outline="true">
          {{ t('components.base.productReviews.rateProductButton') }}
        </BaseButton>
      </div>
    </div>

    <div class="flex flex-col space-y-5 mt-8 md:mt-16">
      <div
        v-for="review in newestReview ? props.productReviews.slice(1) : props.productReviews"
        :key="review.id"
        class="w-full border border-secondary-light p-5 md:p-10">
        <div class="flex flex-col sm:flex-row">
          <BaseReviewStars
            icon-class="w-5 text-amber-300"
            :average="review.points"
            suffix-class="ml-4 mt-2">
            <div class="text-secondary md:text-3xl">
              <span class="font-bold">{{ review.points }}</span>
              <span class="text-base pl-1.5">/ 5</span>
            </div>
          </BaseReviewStars>
          <div class="flex flex-1 items-end md:pb-0.5 sm:pl-8">
            {{ t('components.base.productReviews.writtenAt', {
              date: formatDate(review.createdAt)
            }) }}
          </div>
        </div>
        <div class="flex flex-col mt-4">
          <BaseHeadline
            v-if="review.title"
            tag="div"
            custom-class="h6-styling !mb-2"
            :title="review.title" />
          <div>{{ review.content }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
