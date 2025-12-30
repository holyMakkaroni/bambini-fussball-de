import { useRoute } from 'nuxt/app'
import type { Ref } from 'vue'
import { ref } from 'vue'
import type { RouterProps } from 'instantsearch.js/es/middlewares'
import type { IndexUiState } from 'instantsearch.js/es/types/ui-state'

function generateUrl (basePath: string, data: IndexUiState): string {
  const { query, refinementList, ...queryParams } = data
  const { REFINEMENT_PATH_PREFIX, createRefinementUrl, buildQueryParams } = useAlgoliaHelper()
  const urlSegments: string[] = []

  if (!query) {
    return '/'
  }

  urlSegments.push(basePath)
  urlSegments.push(encodeURIComponent(query))

  if (refinementList) {
    const filterUrl = createRefinementUrl(refinementList)

    if (filterUrl.length) {
      urlSegments.push(`${REFINEMENT_PATH_PREFIX}/${filterUrl.join('/')}`)
    }
  }

  const queryParamsString = buildQueryParams(queryParams)

  urlSegments.push(queryParamsString)

  return `/${urlSegments.join('/')}`
}

export const useAlgoliaSearchRouting = (indexPrefix: string, categoryPath?: string, categoryBreadcrumb?: string[]) => {
  const route = useRoute()
  const { filters } = useAlgoliaMapping()
  const { getRefinementList, getQueryParams, mapToOriginalKeys, mapToSeoKeys, transformSortString } = useAlgoliaHelper()

  const getBasePath = () => route.path.split('/')[1] // Dynamically get translated route

  const algoliaSearchRouter: Ref<Pick<Required<RouterProps>, 'router' | 'stateMapping'>> = ref({
    router: {
      write (routeState: IndexUiState) {
        if (process.client) {
          window.history.pushState(
            routeState,
            '',
            generateUrl(getBasePath(), routeState)
          )
        }
      },
      read () {
        const query = route.params.all
        const refinementList = getRefinementList(route.fullPath)
        const queryParams = getQueryParams(route.fullPath)

        return {
          query: Array.isArray(query) ? query[0] : query,
          refinementList,
          queryParams
        }
      },
      createURL (routeState: IndexUiState) {
        return generateUrl(getBasePath(), routeState)
      },
      onUpdate (cb: any) {
        if (process.client) {
          window.addEventListener('popstate', cb)
        }
      }
    },
    stateMapping: {
      stateToRoute (uiState) {
        const indexProductUiState = uiState[`${indexPrefix}_product`] || {}

        return {
          query: indexProductUiState.configure?.query || '',
          refinementList: mapToSeoKeys(indexProductUiState.refinementList || {}, filters.value),
          p: indexProductUiState.page ?? null, // Ensure `null` if missing
          sortBy: indexProductUiState.sortBy ? transformSortString(indexProductUiState.sortBy) : null // Ensure `null` if missing
        }
      },
      routeToState (routeState) {
        return {
          [`${indexPrefix}_product`]: {
            configure: {
              query: routeState.query || ''
            },
            refinementList: mapToOriginalKeys(routeState.refinementList || {}, filters.value),
            page: routeState.queryParams?.p ?? null, // Ensure `null` if missing
            sortBy: routeState.queryParams?.sortBy
              ? `${indexPrefix}_product_${routeState.queryParams.sortBy}`
              : null // Ensure `null` if missing
          }
        }
      }
    }
  })

  return {
    algoliaSearchRouter
  }
}
