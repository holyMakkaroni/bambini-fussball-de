<script setup lang="ts">
defineProps({
  isEmpty: {
    type: Boolean,
    default: false
  },
  totalPrice: {
    type: Number,
    default: 0
  },
  actionButtonLabel: {
    type: String,
    required: true
  }
})

const emit = defineEmits([
  'actionButton'
])

const { t } = useI18n()
const { getFormattedPrice } = usePrice()
</script>

<template>
  <div
    class="h-full"
    :class="{'flex justify-center pt-[40%] px-5 md:px-10': isEmpty, 'overflow-y-scroll': !isEmpty}">
    <slot name="default" />
  </div>
  <div
    v-if="!isEmpty"
    class="box-shadow py-4 px-10">
    <slot name="content">
      <i18n-t
        tag="div"
        class="text-center"
        scope="global"
        keypath="components.base.sidebarCart.total">
        <template #total>
          <span class="font-bold">{{ getFormattedPrice(totalPrice) }}</span>
        </template>
      </i18n-t>
    </slot>
    <BaseButton
      :title="t('components.base.sidebarCart.checkout')"
      class="!w-full my-4"
      icon="cart"
      icon-class="size-5"
      icon-position="left"
      variant="green"
      size="big"
      @click="emit('actionButton')">
      {{ actionButtonLabel }}
    </BaseButton>
    <div class="text-xs text-center nl2br">
      {{ t('components.base.sidebarCart.tax') }}
    </div>
  </div>
</template>

<style scoped>

</style>
