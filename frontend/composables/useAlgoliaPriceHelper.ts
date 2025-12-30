import type { AlgoliaItem } from '~/types/algolia'

export function useAlgoliaPriceHelper () {
  const { sessionContext } = useSessionContext()
  // @ts-ignore
  const productRules = sessionContext.value?.areaRuleIds.product

  const getPrice = (item: AlgoliaItem) => {
    const advancedPricing = item?.advancedPricing

    if (advancedPricing && productRules && productRules.length > 0) {
      const pricing = advancedPricing[productRules[0]]

      if (pricing) {
        const price = pricing.gross
        const listPrice = pricing?.listPrice ? pricing.listPrice.gross : null
        const percentage = pricing?.percentage ? pricing.percentage.gross : null
        const regulationPrice = pricing?.regulationPrice ? pricing.regulationPrice.gross : null

        return {
          price,
          listPrice,
          percentage,
          regulationPrice
        }
      }
    }

    return {
      price: item.defaultPricing?.gross,
      listPrice: item.defaultPricing?.listPrice?.gross,
      percentage: item.defaultPricing?.percentage?.gross,
      regulationPrice: item.defaultPricing?.regulationPrice?.gross
    }
  }

  return {
    getPrice
  }
}
