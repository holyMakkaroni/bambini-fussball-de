import type { Option, Properties, Property, PropertyMapping } from '~/types/shopware'
import type { Schemas } from '#shopware'

const parseUrlForFilters = (url: string) => {
  const segments = url.split('/').filter(Boolean)

  const filters: Record<string, { group: string; options: string[] }> = {}

  segments.forEach((segment) => {
    if (segment.includes('--')) {
      const [group, option] = segment.split('--')
      if (!filters[group]) {
        filters[group] = { group, options: [] }
      }
      filters[group].options.push(option)
    }
  })

  return filters
}

const mergePropertiesByGroupId = (propertyGroupOptions: Schemas['PropertyGroupOption'][]) => {
  const result: Properties = {}

  propertyGroupOptions.forEach((item) => {
    const { groupId, id, customFields, group } = item

    if (!result[groupId] && group.customFields && group.customFields.filterName) {
      result[groupId] = {
        id: groupId,
        filterName: group.customFields.filterName,
        filterPriority: group.customFields.filterPriority || 0,
        options: []
      }
    }

    if (customFields?.optionValue) {
      result[groupId].options.push({
        id,
        optionValue: customFields?.optionValue
      })
    }
  })

  return result
}

export const useSeoFilterUrl = (propertyMapping: Ref<PropertyMapping>, path: string) => {
  const { apiClient } = useShopwareContext()
  const router = useRouter()

  const hasSeoFilterPattern = (path: string): boolean => {
    return path.includes('--')
  }

  const removeSeoFilterSegments = (path: string): string => {
    const cleanSegments = path?.split('/')
      .filter(segment => !segment.includes('--'))
      .join('/')

    return cleanSegments?.startsWith('/') ? cleanSegments : `/${cleanSegments}`
  }

  const fetchPropertiesById = async (optionIds: string[]) => {
    if (!optionIds || optionIds.length === 0) {
      return null
    }

    const { data: propertyData } = await useAsyncData(
      'fetchPropertyGroupOptions-' + optionIds.join('-'),
      async () => {
        return await apiClient.invoke('searchPropertyGroupOption post /search/property-group-option', {
          body: {
            ids: optionIds,
            associations: {
              group: {}
            }
          }
        })
      },
      {
        // TODO: Remove if bug https://github.com/nuxt/nuxt/issues/18405 is fixed in Vue 3.5
        getCachedData (key, nuxt) {
          return nuxt.payload.data[key] || nuxt.static.data[key]
        }
      })

    if (!propertyData.value) {
      return null
    }

    return mergePropertiesByGroupId(propertyData.value.data.elements)
  }

  const fetchPropertyByOptions = async (group: string, options: string[]) => {
    try {
      const { data: groupData } = await useAsyncData(
        'fetchPropertyGroup-' + options.join('-'),
        async () => {
          const response = await apiClient.invoke('searchPropertyGroup post /search/property-group', {
            body: {
              filter: [
                {
                  type: 'multi',
                  operator: 'AND',
                  queries: [
                    {
                      type: 'equals',
                      field: 'customFields.filterName',
                      value: group
                    },
                    {
                      type: 'equals',
                      field: 'filterable',
                      value: true
                    }
                  ]
                }
              ],
              associations: {
                options: {
                  filter: [
                    {
                      type: 'multi',
                      operator: 'OR',
                      queries: options.map(option => ({
                        type: 'equals' as const,
                        field: 'customFields.optionValue',
                        value: option
                      }))
                    }
                  ]
                }
              }
            }
          })

          return response ?? null
        },
        {
          getCachedData (key, nuxt) {
            return nuxt.payload.data[key] || nuxt.static.data[key]
          }
        }
      )

      if (!groupData.value) {
        return null
      }

      const groupId = groupData.value.data.elements[0].id
      const filterName = groupData.value.data.elements[0].customFields?.filterName || ''
      const filterPriority = groupData.value.data.elements[0].customFields?.filterPriority || 0

      const optionsData = groupData.value.data.elements?.flatMap((element: any) => {
        return element.options.map((option: any) => ({
          id: option.id,
          optionValue: option.customFields?.optionValue || option.name
        }))
      })

      return {
        id: groupId,
        filterName,
        filterPriority,
        options: optionsData
      }
    } catch (err: any) {
      return null
    }
  }

  const fetchFiltersFromUrl = async () => {
    const filters = parseUrlForFilters(path)
    const result: Properties = {}

    for (const group in filters) {
      const filterData = await fetchPropertyByOptions(filters[group].group, filters[group].options)
      if (filterData) {
        result[filterData.id] = {
          id: filterData.id,
          filterName: filterData.filterName,
          filterPriority: filterData.filterPriority,
          options: filterData.options
        }
      }
    }

    return result
  }

  const addOrUpdateOption = (groupId: string, filterName: string, filterPriority: number, option: Option) => {
    if (!propertyMapping.value.properties) {
      propertyMapping.value.properties = {}
    }

    const group = propertyMapping.value.properties[groupId] as Property

    if (group) {
      const existingOptionIndex = group.options.findIndex(o => o.id === option.id)

      if (existingOptionIndex === -1) {
        group.options.push(option)
      } else {
        removeOption(groupId, option.id)
      }

      if (group.options.length === 0) {
        delete propertyMapping.value?.properties[groupId]
      }
    } else {
      propertyMapping.value.properties[groupId] = {
        id: groupId,
        filterName,
        filterPriority,
        options: [option]
      }
    }
  }

  const removeGroup = (groupId: string) => {
    if (propertyMapping.value.properties && propertyMapping.value.properties[groupId]) {
      delete propertyMapping.value.properties[groupId]
    }
  }

  const removeOption = (groupId: string, optionId: string) => {
    if (propertyMapping.value?.properties && propertyMapping.value.properties[groupId]) {
      const group = propertyMapping.value.properties[groupId] as Property

      group.options = group.options.filter(option => option.id !== optionId)

      if (group.options.length === 0) {
        delete propertyMapping.value.properties[groupId]
      }
    }
  }

  const getSelectedOptionIds = (groupId: string): string[] => {
    if (propertyMapping.value?.properties && propertyMapping.value.properties[groupId]) {
      const group = propertyMapping.value.properties[groupId] as Property

      return group.options.map(option => option.id)
    }

    return []
  }

  const getAllSelectedOptionIds = (): string[] => {
    const selectedOptionIds: string[] = []

    // Loop through all groups in the properties
    for (const groupId in propertyMapping.value?.properties) {
      if (propertyMapping.value?.properties[groupId] && (propertyMapping.value?.properties[groupId] as Property).options) {
        const group = propertyMapping.value?.properties[groupId] as Property

        const optionIds = group.options.map(option => option.id)

        selectedOptionIds.push(...optionIds)
      }
    }

    return selectedOptionIds
  }

  const generateSeoUrl = (properties: Properties) => {
    if (!properties) {
      return ''
    }

    const sortedProperties = Object.values(properties).sort((a, b) => b.filterPriority - a.filterPriority)

    const urlSegments = sortedProperties.flatMap((property) => {
      return property.options.map((option) => {
        return `${property.filterName}--${option.optionValue}`
      })
    })

    return urlSegments.join('/')
  }

  watch(propertyMapping.value?.properties, async () => {
    const seoUrl = generateSeoUrl(propertyMapping.value?.properties)
    const url = [removeSeoFilterSegments(path), seoUrl].filter(Boolean).join('/')

    await router.push(url)
  })

  return {
    removeGroup,
    addOrUpdateOption,
    removeOption,
    getSelectedOptionIds,
    getAllSelectedOptionIds,
    generateSeoUrl,
    fetchPropertiesById,
    fetchFiltersFromUrl,
    removeSeoFilterSegments,
    hasSeoFilterPattern
  }
}
