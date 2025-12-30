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
  <div
    v-editable="blok"
    class="c-feature bg-secondary-light"
    :class="{'margin-default': blok.margin}">
    <div
      class="px-0 lg:px-4 xl:px-0"
      :class="{'container': container}">
      <BaseUsp
        class="py-5 h-[92px]"
        :step-interval="3000"
        :responsive="{ 600: 1, 768: 2, 1024: 3, default: 4 }">
        <template
          v-for="(feature, index) in blok.features"
          :key="feature.uid"
          #[`step-${index}`]>
          <div
            v-editable="feature"
            class="flex items-center">
            <div
              v-if="feature.icon"
              class="w-12 h-12 bg-white rounded-full flex justify-center items-center">
              <BaseIcon
                :name="feature.icon"
                class="w-4 h-4 text-success" />
            </div>
            <BaseText
              class="flex-1 text-nowrap text-xs ml-3"
              :text="feature.text" />
          </div>
        </template>
      </BaseUsp>
    </div>
  </div>
</template>
