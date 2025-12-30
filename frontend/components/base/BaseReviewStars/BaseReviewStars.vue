<script setup lang="ts">
const props = defineProps({
  average: {
    type: Number,
    required: true,
    validator (value: number) {
      return value <= 5
    }
  },
  iconClass: {
    type: String,
    default: ''
  },
  suffixClass: {
    type: String,
    default: ''
  }
})

const stars = ref<number>(0)

const numbers = props.average.toFixed(2).toString().split('.')
const rating = Number(numbers[0])
const decimalRating = Number(numbers[1])

stars.value = rating

const halfStar = computed(() => decimalRating >= 50)

const emptyStars = computed(() => {
  const emptyStarsCount = 5 - rating
  if (halfStar.value) {
    return emptyStarsCount - 1
  }

  return emptyStarsCount
})

</script>

<template>
  <div class="c-base-review-stars flex items-center">
    <BaseIcon
      v-for="n in rating"
      :key="n"
      :class="props.iconClass"
      name="star" />
    <BaseIcon
      v-if="halfStar"
      :class="props.iconClass"
      name="star-half" />
    <BaseIcon
      v-for="n in emptyStars"
      :key="n"
      :class="props.iconClass"
      name="star-empty" />
    <div :class="props.suffixClass">
      <slot />
    </div>
  </div>
</template>

<style scoped>

</style>
