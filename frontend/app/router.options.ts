import type { RouterConfig } from '@nuxt/schema'
import qs from 'qs'

// https://router.vuejs.org/api/interfaces/routeroptions.html
export default <RouterConfig>{
  scrollBehavior (to, from, savedPosition) {
    if (savedPosition && !to.hash) {
      return savedPosition
    }

    if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth'
      }
    }

    return {
      top: 0
    }
  },
  parseQuery: qs.parse,
  stringifyQuery: qs.stringify
}
