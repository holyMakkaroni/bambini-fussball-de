<script setup lang="ts">
import { useConfigStore } from '@/stores/config'

const nuxtApp = useNuxtApp()

useHead({
  htmlAttrs: {
    lang: nuxtApp.$i18n.locale
  }
})

const { locale } = useI18n()
const { apiClient } = useShopwareContext()
const { fetchConfig } = useConfigStore()
const { refreshCart } = useCart()

const { data: sessionContextData } = await useAsyncData(
  'sessionContext',
  async () => {
    const { data } = await apiClient.invoke('readContext get /context')

    return data
  }
)

await callOnce(fetchConfig)

if (sessionContextData.value) {
  usePrice({
    currencyCode: sessionContextData.value?.currency?.isoCode || '',
    localeCode: locale.value
  })
  useSessionContext(sessionContextData.value)
}

onMounted(() => {
  refreshCart()
})
</script>

<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>
