// Import necessary types and composables
import type { RouteLocationNormalized } from 'vue-router'
import type { PropertyMapping } from '~/types/shopware'

// eslint-disable-next-line @typescript-eslint/no-unused-vars
export default defineNuxtRouteMiddleware(async (to: RouteLocationNormalized, from: RouteLocationNormalized) => {
  // TODO: Change logic to storyblok component type e.g: type-blog
  if (to.path.includes('/magazin')) {
    setPageLayout('blog')
  }
})
