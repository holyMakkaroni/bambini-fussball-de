<script setup lang="ts">
import { getProductRoute } from '@shopware-pwa/helpers-next'
import { ref, computed, unref } from 'vue'
import type { ComputedRef } from 'vue'
import { useRouter } from 'vue-router'
import { pascalCase } from 'scule'
import { useProductConfigurator } from '#imports'

const props = defineProps({
  allowRedirect: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['change'])
const isLoading = ref<boolean>(false)
const router = useRouter()
const {
  handleChange,
  getOptionGroups,
  getSelectedOptions,
  findVariantForSelectedOptions
} = useProductConfigurator()

const loadComponent = (type: String) => {
  return resolveComponent(pascalCase(`BaseVariantConfiguratorType_${type}`))
}

const selectedOptions: ComputedRef<any> = computed(() =>
  Object.values(unref(getSelectedOptions))
)

const onHandleChange = async () => {
  isLoading.value = true
  const variantFound = await findVariantForSelectedOptions(
    unref(selectedOptions)
  )

  const selectedOptionsVariantPath = getProductRoute(variantFound)
  if (props.allowRedirect && selectedOptionsVariantPath) {
    try {
      router.push(selectedOptionsVariantPath)
    } catch (error) {
      // eslint-disable-next-line no-console
      console.error('incorrect URL', selectedOptionsVariantPath)
    }
  } else {
    emit('change', variantFound)
  }
  isLoading.value = false
}

const optionSelect = (arg: {
  optionGroupName: string,
  optionId: string
}) => {
  return handleChange(arg.optionGroupName, arg.optionId, onHandleChange)
}
</script>

<template>
  <div class="c-base-variant-configurator flex flex-col">
    <BaseDivider
      variant="solid"
      class="my-5" />
    <div
      v-for="optionGroup in getOptionGroups"
      :key="optionGroup.id">
      <div class="text-xs font-bold mb-4">
        {{ optionGroup.translated.name }}:
      </div>
      <component
        :is="loadComponent(optionGroup.displayType)"
        :option-group="optionGroup"
        @option-select="optionSelect" />
      <BaseDivider
        variant="solid"
        class="my-5" />
    </div>
  </div>
</template>
