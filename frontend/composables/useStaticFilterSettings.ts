import type { Filter, FilterGroup } from '~/types/filter'

export const useStaticFilterSettings = (group: FilterGroup) => {
  const { t } = useI18n()

  const filter : Filter = {
    default: [
      {
        name: 'manufacturer',
        label: t('filter.manufacturer.label'),
        seoKey: t('filter.manufacturer.seoKey'),
        displayType: 'text'
      }
    ],
    toggle: [
      {
        name: 'defaultPricing.inSale',
        label: t('filter.inSale.label'),
        seoKey: t('filter.inSale.seoKey'),
        displayType: 'toggle'
      }
    ],
    range: [
      {
        name: 'defaultPricing.gross',
        label: t('filter.priceRange.label'),
        seoKey: t('filter.priceRange.seoKey'),
        displayType: 'range'
      }
    ]
  }

  return filter[group]
}
