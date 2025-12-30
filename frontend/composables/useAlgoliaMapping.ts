import slugify from 'slugify'
import type { Refinements } from '~/types/filter'
import type { Schemas } from '~/api-types/storeApiTypes'

export function useAlgoliaMapping (category?: Schemas['Category']) {
  const defaultFilter = useStaticFilterSettings('default')
  const filters = useState<{ [key: string]: Refinements }>(`filters-${category?.id}`, () => ({}))

  const extractPropertyMap = () => {
    const result: { [key: string]: Refinements } = {}

    if (!category?.cmsPage?.sections) { return result }

    for (const section of category.cmsPage.sections) {
      if (!section.blocks) { continue }

      for (const block of section.blocks) {
        if (block.type === 'product-listing' && block.slots) {
          for (const slot of block.slots) {
            const entities = slot.data?.listing?.aggregations?.properties?.entities

            if (Array.isArray(entities)) {
              for (const entity of entities) {
                if (entity.name && entity.displayType) {
                  const name = entity.name
                  const seoKey = slugify(name, {
                    lower: true,
                    strict: true,
                    locale: 'de'
                  })

                  result[name] = {
                    label: name,
                    name,
                    seoKey: entity.customFields?.seoKey ?? seoKey,
                    displayType: entity.displayType
                  }
                }
              }
            }
          }
        }
      }
    }

    return result
  }

  // Apply default filters
  for (const { name, label, seoKey, displayType } of defaultFilter) {
    filters.value[name] = { label, name, seoKey, displayType }
  }

  // Merge dynamic filters
  const dynamicFilters = extractPropertyMap()
  filters.value = { ...filters.value, ...dynamicFilters }

  return { filters }
}
