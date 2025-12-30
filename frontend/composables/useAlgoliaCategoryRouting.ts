import { ref } from 'vue'
import { useRouter } from 'vue-router'
import type { RouterProps } from 'instantsearch.js/es/middlewares'
import type { IndexUiState, UiState } from 'instantsearch.js/es/types/ui-state'

export const useAlgoliaCategoryRouting = (indexPrefix: string, categoryPath: string) => {
  const app = useNuxtApp()
  const router = useRouter()
  const { REFINEMENT_PATH_PREFIX, createRefinementUrl, getRefinementList, getQueryParams, transformSortString } = useAlgoliaHelper()
  const latestRead = ref<any>(null)

  const read = () => {
    const refinementList = getRefinementList(router.currentRoute.value.fullPath)
    const queryParams = getQueryParams(router.currentRoute.value.fullPath)

    const readPayload = {
      refinementList,
      queryParams
    }
    latestRead.value = readPayload
    return readPayload
  }

  const write = (routeState: IndexUiState) => {
    const { refinementList, sortBy, page, toggle } = routeState
    const urlSegments: string[] = []
    const queryParams: Record<string, string> = {}
    const cleanRead = JSON.stringify({ refinementList: {}, queryParams: {} })
    // ignoring dissonant writing requests
    if (latestRead.value && JSON.stringify(latestRead.value) !== cleanRead) {
      // there were some filters applied previously, do we have any filters here?
      if (typeof routeState?.refinementList === 'undefined') {
        // if we do not have any filters, this is a dissonant request, we should not update the written url
        return
      }
    }
    if (categoryPath) {
      urlSegments.push(categoryPath)
    }

    if (refinementList) {
      const filterUrl = createRefinementUrl(refinementList)

      if (filterUrl.length) {
        urlSegments.push(`${REFINEMENT_PATH_PREFIX}/${filterUrl.join('/')}`)
      }
    }

    if (sortBy) {
      queryParams.sortBy = sortBy
    }

    if (toggle?.['defaultPricing.inSale']) {
      queryParams.inSale = '1'
    }

    if (page) {
      queryParams.p = String(page)
    }

    const newPath = `${urlSegments.join('/')}`
    const newQuery = new URLSearchParams(queryParams).toString()
    const newUrl = newQuery ? `${newPath}?${newQuery}` : newPath

    if (router.currentRoute.value.fullPath !== newUrl) {
      // actually push the new url if we are handling the same category
      if (router.currentRoute.value.path.includes(categoryPath)) {
        router.push(newUrl)
      }
    }
  }

  const createURL = (routeState: IndexUiState) => {
    const { refinementList, sortBy, page, toggle } = routeState
    const urlSegments: string[] = []
    const queryParams: Record<string, string> = {}

    if (categoryPath) {
      urlSegments.push(categoryPath)
    }

    if (refinementList) {
      const filterUrl = createRefinementUrl(refinementList)

      if (filterUrl.length) {
        urlSegments.push(`${REFINEMENT_PATH_PREFIX}/${filterUrl.join('/')}`)
      }
    }

    if (sortBy) {
      queryParams.sortBy = sortBy
    }

    if (toggle?.['defaultPricing.inSale']) {
      queryParams.inSale = '1'
    }

    if (page) {
      queryParams.p = String(page)
    }

    return router.resolve({
      path: `${urlSegments.join('/')}`,
      query: queryParams
    }).href
  }

  return {
    algoliaListingRouter: ref<Pick<Required<RouterProps>, 'router' | 'stateMapping'>>({
      router: {
        read,
        write,
        createURL,
        onUpdate (cb) {
          if (typeof window === 'undefined') { return }
          // @ts-ignore
          this._removeAfterEach = router.afterEach((to, from) => {
            if (router.currentRoute.value.path.includes(categoryPath)) {
              cb(this.read())
            }
          })
          app.hook('page:finish', () => {
            if (router.currentRoute.value.path.includes(categoryPath)) {
              cb(this.read())
            }
          })
        },
        dispose () {
          if (typeof window === 'undefined') {
            return
          }
          // @ts-ignore
          if (this._removeAfterEach) {
            // @ts-ignore
            this._removeAfterEach()
          }
        }
      },
      stateMapping: {
        stateToRoute (uiState: UiState) {
          const indexProductUiState = uiState[`${indexPrefix}_product`] || {}
          return {
            refinementList: indexProductUiState.refinementList,
            page: indexProductUiState.page ?? null,
            sortBy: indexProductUiState.sortBy ? transformSortString(indexProductUiState.sortBy) : null,
            toggle: {
              'defaultPricing.inSale': indexProductUiState.toggle?.['defaultPricing.inSale'] ? indexProductUiState.toggle['defaultPricing.inSale'] : null
            }
          }
        },
        routeToState (routeState: ReturnType<typeof this.stateToRoute>): UiState {
          const state: UiState = {
            [`${indexPrefix}_product`]: {
              refinementList: routeState.refinementList,
              page: routeState.queryParams?.p ?? null,
              sortBy: routeState.queryParams?.sortBy ? `${indexPrefix}_product_${routeState.queryParams.sortBy}` : null,
              toggle: {
                'defaultPricing.inSale': routeState.queryParams?.inSale ? routeState.queryParams?.inSale : null
              }
            }
          } as const
          return state
        }
      }
    })
  }
}
