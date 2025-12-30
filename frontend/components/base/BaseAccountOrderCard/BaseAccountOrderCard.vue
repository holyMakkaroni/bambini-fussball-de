<script setup lang="ts">
import type { Schemas } from '#shopware'

const props = defineProps({
  order: {
    type: Object as PropType<Schemas['Order']>,
    required: true
  }
})

const { formatDate } = useUtilities()
const { getFormattedPrice } = usePrice()

const paymentMethod = computed(() => props.order.transactions && props.order.transactions[0].paymentMethod?.translated?.name)
</script>

<template>
  <div class="c-base-account-order-card p-5 bg-secondary-light">
    <div class="flex flex-col border-b border-secondary-dark pb-3 mb-5">
      <div class="font-semibold text-xl">
        {{ order.stateMachineState.translated.name }}
      </div>
      <div class="flex flex-col lg:flex-row gap-y-6 gap-x-3 text-sm">
        <div class="w-full max-w-[400px]">
          Lieferung voraussichtlich am 12.09.2024
        </div>
        <div class="flex-1 grid grid-cols-[repeat(auto-fill,minmax(160px,1fr))] gap-y-2 gap-x-10">
          <div class="flex gap-x-1">
            <span>Bestellt am:</span>
            <span class="font-semibold line-clamp-1">{{ formatDate(order.createdAt) }}</span>
          </div>
          <div class="flex gap-x-1">
            <span>Bestellnummer:</span>
            <span class="font-semibold line-clamp-1">{{ order.orderNumber }}</span>
          </div>
          <div class="flex gap-x-1">
            <span>Zahlart:</span>
            <span class="font-semibold line-clamp-1">{{ paymentMethod }}</span>
          </div>
          <div class="flex gap-x-1">
            <span>Gesamtbetrag:</span>
            <span class="font-semibold line-clamp-1">{{ getFormattedPrice(order.amountTotal) }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-col divide-y divide-secondary-dark gap-y-6">
      <div
        v-for="item in order.lineItems"
        :key="item.id"
        class="flex flex-wrap flex-col md:flex-row gap-y-6 gap-x-3">
        <div class="w-full max-w-[400px] flex justify-center">
          <BaseImage
            provider="gumlet"
            :width="160"
            :height="160"
            object-fit="contain"
            fit="contain"
            sizes="150px sm:175px"
            lazy
            img-class="mix-blend-multiply"
            :image="{
              name: item.cover?.alt,
              title: item.cover?.alt,
              alt: item.cover?.alt,
              filename: item.cover?.path
            }" />
        </div>
        <div class="flex-1 flex flex-col justify-center leading-6">
          <div class="underline font-semibold">
            {{ item.label }}
          </div>
          <div>{{ getFormattedPrice(item.unitPrice) }}</div>
          <div>Stornierung noch möglich</div>
        </div>
        <div class="w-full max-w-[200px]" />
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
