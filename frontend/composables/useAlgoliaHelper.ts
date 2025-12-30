import type { QueryParams, Refinement } from '~/types/filter'

export function useAlgoliaHelper () {
  const REFINEMENT_PATH_PREFIX = 'f'

  const splitLabel = (label: string) => {
    return getSwatchData(label)[0]
  }

  const getSwatchData = (label: string) => {
    return label.split(';')
  }

  const getCategoryUrl = (url: string) => {
    const cleanUrl = url.split('?')[0]

    const index = cleanUrl.indexOf(`/${REFINEMENT_PATH_PREFIX}/`)
    if (index !== -1) {
      return cleanUrl.substring(0, index)
    }
    return cleanUrl
  }

  const parseRefinements = (path: string): Refinement => {
    const urlParts: string[] = path.split(`/${REFINEMENT_PATH_PREFIX}/`)

    if (urlParts.length < 2) {
      return {}
    }

    const refinementPart: string = urlParts[1].split('?')[0]
    const refinements: string[] = refinementPart.split('/')

    const result: Refinement = {}

    refinements.forEach((refinement: string) => {
      const [key, value]: string[] = refinement.split('--')
      if (key && value) {
        if (!result[key]) {
          result[key] = []
        }
        result[key].push(value)
      }
    })

    return result
  }

  const parseQueryParams = (url: string): QueryParams => {
    const queryString: string = url.split('?')[1]
    if (!queryString) { return {} }

    const queryParams: QueryParams = {}
    const pairs: string[] = queryString.split('&')

    pairs.forEach((pair: string) => {
      const [key, value]: string[] = pair.split('=')
      if (key && value) {
        queryParams[decodeURIComponent(key)] = decodeURIComponent(value)
      }
    })

    return queryParams
  }

  const getRefinementList = (path: string): Refinement => {
    return parseRefinements(path)
  }

  const getQueryParams = (path: string): QueryParams => {
    return parseQueryParams(path)
  }

  const mapToSeoKeys = (input: Record<string, any>, mapping: Record<string, { seoKey?: string; label?: string }>): Record<string, any> | undefined => {
    if (!input) {
      return
    }

    return Object.keys(input).reduce((acc: Record<string, any>, key) => {
      if (mapping[key]) {
        const mappedKey = mapping[key].seoKey || (mapping[key].label ? encodeURIComponent(mapping[key].label) : encodeURIComponent(key))
        acc[mappedKey] = input[key]
      }
      return acc
    }, {})
  }

  const mapToOriginalKeys = (input: Record<string, any>, mapping: Record<string, { seoKey?: string; label?: string }>): Record<string, any> | undefined => {
    if (!input) {
      return
    }

    const reverseMapping = Object.entries(mapping).reduce((acc: Record<string, string>, [key, value]) => {
      const mappedKey = value.seoKey || (value.label ? encodeURIComponent(value.label) : encodeURIComponent(key))
      acc[mappedKey] = key
      return acc
    }, {})

    return Object.keys(input).reduce((acc: Record<string, any>, key) => {
      if (reverseMapping[key]) {
        const decodedValues = input[key].map((str: string) => decodeURIComponent(decodeURIComponent(str)))
        acc[reverseMapping[key]] = decodedValues
      }
      return acc
    }, {})
  }

  const createRefinementUrl = (refinementList: Record<string, string[]>): string[] => {
    return Object.entries(refinementList)
      .flatMap(([key, values]) =>
        values.map(value => (`${encodeURIComponent(key)}--${encodeURIComponent(splitLabel(value))}`))
      )
  }

  const transformSortString = (sortString: string) => {
    return sortString?.replace(/.*product_/, '') || sortString
  }

  const buildQueryParams = (params: Record<string, any>): string => {
    return Object.entries(params)
      .filter(([_, value]) => value !== undefined && value !== null)
      .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
      .join('&')
  }

  const mergeDynamicFilters = (filters: Record<string, string[]>, allowedKeys: string[]): string[] => {
    const selectedFilters: string[] = []

    for (const key of allowedKeys) {
      if (Array.isArray(filters[key])) {
        selectedFilters.push(...filters[key])
      }
    }

    return selectedFilters
  }

  return {
    REFINEMENT_PATH_PREFIX,
    splitLabel,
    getSwatchData,
    getCategoryUrl,
    createRefinementUrl,
    getRefinementList,
    getQueryParams,
    mapToSeoKeys,
    mapToOriginalKeys,
    transformSortString,
    buildQueryParams,
    mergeDynamicFilters
  }
}
