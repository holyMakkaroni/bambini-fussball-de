<script setup lang="ts">
import { computed } from 'vue'

const { t } = useI18n()
const { apiClient } = useShopwareContext()
const route = useRoute()
const router = useRouter()

const limit = 15
const currentPage = computed(() => route.query.p ? Number(route.query.p) : 1)

const changePage = async (page: number) => {
  await router.push({
    query: {
      ...route.query,
      p: page
    }
  })

  await refresh()
}

const { data, refresh } = await useAsyncData(
  `load-customer-orders-p${currentPage.value}`, async () => {
    const { data } = await apiClient.invoke('readOrder post /order', {
      body: {
        page: currentPage.value,
        limit,
        associations: {
          lineItems: {
            associations: {
              cover: {}
            }
          },
          transactions: {
            associations: {
              paymentMethod: {}
            }
          },
          stateMachineState: {}
        },
        sort: [
          {
            field: 'createdAt',
            order: 'DESC'
          }
        ],
        'total-count-mode': 'exact'
      }
    })
    return data.orders || []
  }, {
    // TODO: Remove if bug https://github.com/nuxt/nuxt/issues/18405 is fixed in Vue 3.5
    getCachedData (key, nuxt) {
      return nuxt.payload.data[key] || nuxt.static.data[key]
    }
  }
)

const orders = computed(() => data.value?.elements || [])
const openOrders = computed(() => orders.value.filter(order => order.stateMachineState?.technicalName !== 'closed' && order.stateMachineState?.technicalName !== 'cancelled'))
const closedOrders = computed(() => orders.value.filter(order => order.stateMachineState?.technicalName === 'closed'))
const totalPages = computed(() => Math.ceil(data.value?.total || 0) / limit)
</script>

<template>
  <div class="type-my-account-orders">
    <BaseAccountHeader
      :title="t('pages.myAccount.orders.headline')"
      :description="t('pages.myAccount.orders.description')" />

    <div class="flex flex-col">
      <div>
        <div v-if="openOrders.length > 0">
          <BaseAccountSubHeader :title="t('pages.myAccount.orders.openOrdersHeadline')" />
          <div class="flex flex-col gap-y-5">
            <BaseAccountOrderCard
              v-for="order in openOrders"
              :key="order.id"
              :order="order" />
          </div>
        </div>

        <div v-if="closedOrders.length > 0">
          <BaseAccountSubHeader :title="t('pages.myAccount.orders.closedOrdersHeadline')" />
          <div class="flex flex-col gap-y-5">
            <BaseAccountOrderCard
              v-for="order in closedOrders"
              :key="order.id"
              :order="order" />
          </div>

          <div class="flex justify-center mt-10">
            <BasePagination
              :total="totalPages"
              :length="3"
              :current="route.query.p ? Number(route.query.p) : Number(currentPage)"
              @change-page="changePage" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
