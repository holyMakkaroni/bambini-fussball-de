import type { Schemas } from '#shopware'

export const useShopwareHelper = () => {
  const headlessEndpoint = 'uniqueweb-headless/set'
  const { public: config } = useRuntimeConfig()

  const getCheckoutUrl = (sessionContext: Schemas['SalesChannelContext'] | undefined) => {
    return `${config.shopwareCheckoutUrl}/${headlessEndpoint}/${sessionContext?.token}`
  }

  const isOptionSelected = (options: Object, optionId: string) =>
    Object.values(options).includes(optionId)

  return {
    getCheckoutUrl,
    isOptionSelected
  }
}
