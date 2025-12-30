<script setup lang="ts">
import type { Schemas } from '#shopware'

const quantity = defineModel<number>()

const props = defineProps({
  productId: {
    type: String,
    required: true
  },
  imagePath: {
    type: String,
    default: null
  },
  label: {
    type: String,
    default: null
  },
  itemTotalPrice: {
    type: Number,
    default: null
  },
  itemOptions: {
    type: Object as PropType<Schemas['LineItem']['payload']['options']>,
    default: null
  },
  deliveryTimeUnit: {
    type: String,
    default: 'day',
    validator: (value: string) => ['day', 'week'].includes(value)
  },
  deliveryTimeMin: {
    type: Number,
    default: 1
  },
  deliveryTimeMax: {
    type: Number,
    default: 3
  },
  showQuantityInput: {
    type: Boolean,
    default: false
  },
  showDeliveryTime: {
    type: Boolean,
    default: true
  },
  isRemovable: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits([
  'removeItem'
])

const { removeFromWishlist } = useProductWishlist(
  props.productId
)

const { getFormattedPrice } = usePrice()
const itemImage = computed(() => {
  if (!props.imagePath) {
    return null
  }

  return {
    name: props.label,
    title: props.label,
    alt: props.label,
    filename: props.imagePath
  }
})
</script>

<template>
  <div class="base-sidebar-product-item flex flex-col xs:flex-row w-full gap-3">
    <div class="flex gap-3">
      <div class="w-[64px] flex items-center">
        <BaseImage
          v-if="itemImage"
          provider="gumlet"
          :width="64"
          :height="64"
          object-fit="contain"
          fit="contain"
          sizes="64px"
          :lazy="true"
          img-class="mix-blend-multiply"
          :image="itemImage" />
      </div>
      <div
        v-if="showQuantityInput"
        class="w-[56px] flex items-center">
        <FormSelect
          v-model="quantity"
          name="quantity-select"
          class="max-h-[25px]"
          select-class="!border !border-secondary-light"
          rounded
          variant="small"
          :options="Array.from({ length: 10 }, (_, i) => i + 1).map((key) => {
            return {
              label: key,
              value: key
            }
          })" />
      </div>
    </div>
    <div class="flex flex-1 flex-col xs:flex-row gap-3">
      <div class="flex flex-col flex-1 gap-y-1">
        <div class="line-clamp-2 text-ellipsis text-xs">
          {{ label }}
        </div>
        <div class="font-semibold text-sm">
          {{ getFormattedPrice(itemTotalPrice as number) }}
        </div>
        <ul
          v-if="itemOptions"
          class="font-light text-xxs flex flex-col gap-y-1 leading-tight gap-x-3">
          <li
            v-for="option in itemOptions"
            :key="option.group">
            {{ option.group }}: {{ option.option }}
          </li>
        </ul>
        <div
          v-if="showDeliveryTime"
          class="flex items-center text-success">
          <BaseIcon
            name="truck"
            class="size-4 mr-1.5" />
          <div class="text-xxs font-light pt-0.5 leading-tight">
            <i18n-t
              v-if="deliveryTimeUnit === 'day'"
              scope="global"
              tag="div"
              keypath="components.base.productBox.deliveryLabelDay">
              <template #deliveryTime>
                {{ deliveryTimeMin }} - {{ deliveryTimeMax }}
              </template>
            </i18n-t>
            <i18n-t
              v-if="deliveryTimeUnit === 'week'"
              scope="global"
              tag="div"
              keypath="components.base.productBox.deliveryLabelWeek">
              <template #deliveryTime>
                {{ deliveryTimeMin }} - {{ deliveryTimeMax }}
              </template>
            </i18n-t>
          </div>
        </div>
      </div>
      <div
        v-if="isRemovable"
        class="flex items-center">
        <BaseIcon
          name="garbage"
          class="size-3 cursor-pointer transition-default hover:text-primary"
          @click="emit('removeItem', removeFromWishlist)" />
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
