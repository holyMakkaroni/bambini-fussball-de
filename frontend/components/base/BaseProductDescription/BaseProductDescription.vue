<script setup lang="ts">
import type { PropType } from 'vue'
import type { Schemas } from '#shopware'

const props = defineProps({
  product: {
    type: Object as PropType<Schemas['Product']>,
    required: true
  }
})
const { t } = useI18n()

const productName = computed(() => props.product?.translated.name)
const manufacturerName = computed(() => props.product?.manufacturer?.translated?.name)
const productNumber = computed(() => props.product?.productNumber)
const productEan = computed(() => props.product?.ean)
const productDescription = computed(() => props.product?.translated.description)
const productProperties = computed(() => props.product?.sortedProperties)
const productDefaultProperties = computed(() => {
  const nonExist = '-'
  const unitWeight = 'kg'
  const unitSize = 'mm'

  return [
    {
      name: t('components.base.productDescription.properties.sku'),
      value: productNumber.value || nonExist
    },
    {
      name: t('components.base.productDescription.properties.ean'),
      value: productEan.value || nonExist
    },
    {
      name: t('components.base.productDescription.properties.brand'),
      value: manufacturerName.value || nonExist
    },
    {
      name: t('components.base.productDescription.properties.size'),
      value: props.product?.width && props.product?.height && props.product?.length ? `${props.product?.width} ${unitSize} x ${props.product?.height} ${unitSize} x ${props.product?.length} ${unitSize}` : nonExist
    },
    {
      name: t('components.base.productDescription.properties.weight'),
      value: props.product?.weight ? `${props.product?.weight} ${unitWeight}` : nonExist
    }
  ]
})
</script>

<template>
  <div class="c-base-description bg-secondary-light mt-8 md:mt-28 py-8 md:py-28">
    <div class="container">
      <div class="flex flex-col">
        <BaseHeadline
          v-if="productName"
          tag="h2"
          custom-class="h1-styling"
          :title="productName" />
        <div class="flex gap-5 text-xxs leading-3">
          <span>
            <b>{{ t('components.base.productDescription.properties.sku') }}:</b> {{ productNumber }}
          </span>
          <span v-if="productEan">
            <b>{{ t('components.base.productDescription.properties.ean') }}:</b> {{ productEan }}
          </span>
        </div>
      </div>
      <div
        v-if="productDescription"
        class="md:max-w-[70%] flex flex-col mt-8 md:mt-16">
        <BaseHeadline
          tag="h3"
          custom-class="h3-styling"
          :title="t('components.base.productDescription.description')" />
        <div
          class="c-base-text"
          v-html="productDescription" />
      </div>

      <BaseHeadline
        tag="h3"
        custom-class="h3-styling col-span-12 mt-6 md:mt-11"
        :title="t('components.base.productDescription.functions')" />

      <div class="flex flex-col md:flex-row gap-5">
        <div class="flex-1 flex flex-col">
          <div class="font-semibold mb-2 mt-6 md:mt-0">
            {{ t('components.base.productDescription.defaultProperties') }}
          </div>
          <BaseDivider
            class="!border-secondary/10"
            variant="dashed" />
          <ul>
            <li
              v-for="(defaultProperty, index) in productDefaultProperties"
              :key="index"
              class="flex flex-col">
              <div class="flex flex-col md:flex-row my-2">
                <span class="min-w-[300px] font-semibold md:font-normal">{{ defaultProperty.name }}</span>
                <span>{{ defaultProperty.value }}</span>
              </div>
              <BaseDivider
                class="!border-secondary/10"
                variant="dashed" />
            </li>
          </ul>
          <div v-if="productProperties && productProperties.length">
            <div class="font-semibold mb-2 mt-6 md:mt-11">
              {{ t('components.base.productDescription.productProperties') }}
            </div>
            <BaseDivider
              class="!border-secondary/10"
              variant="dashed" />
            <ul>
              <li
                v-for="property in productProperties"
                :key="property.id"
                class="flex flex-col">
                <div class="flex flex-col md:flex-row my-2">
                  <span class="min-w-[300px] font-semibold md:font-normal">{{ property.translated.name }}</span>
                  <div>
                    <span
                      v-for="(option) in property.options"
                      :key="option.id"
                      class="after:content-[',_'] last:after:content-['']">
                      {{ option.translated.name }}
                    </span>
                  </div>
                </div>
                <BaseDivider
                  class="!border-secondary/10"
                  variant="dashed" />
              </li>
            </ul>
          </div>
        </div>
        <div class="flex-1 flex flex-col">
          <div class="font-semibold mb-2">
            {{ t('components.base.productDescription.downloads') }}
          </div>
          <BaseDivider
            class="!border-secondary/10"
            variant="dashed" />
          <ul>
            <li
              v-for="n in 3"
              :key="n"
              class="flex flex-col">
              <NuxtLink
                to="#"
                target="_blank"
                external
                class="flex items-center my-4 group"
                title="Anleitung ZEISS Conquest V4">
                <div class="w-8 h-8 rounded-full bg-primary transition-default group-hover:brightness-95 flex justify-center items-center">
                  <BaseIcon
                    class="w-4 h-4 text-white"
                    name="download" />
                </div>
                <span class="ml-2">Anleitung ZEISS Conquest V4</span>
              </NuxtLink>
              <BaseDivider
                class="!border-secondary/10"
                variant="dashed" />
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
