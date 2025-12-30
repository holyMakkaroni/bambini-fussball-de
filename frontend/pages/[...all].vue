<script setup lang="ts">
import type { Ref } from 'vue'
import { resolveComponent, defineAsyncComponent } from 'vue'
import { pascalCase } from 'scule'
import type { ISbResponseData, ISbStoryData } from 'storyblok-js-client'
import type { Schemas } from '#shopware'

defineOptions({
  name: 'PageResolver'
})

const routePath = ref<string>('')
const storyblokRenderComponent = ref<ISbStoryData | null>(null)

const routeKey = computed(() => route.path.split('/f/')[0])

definePageMeta({
  middleware: 'change-layout',
  key (route) {
    return route.path.split('/f/')[0]
  }
})

const ERROR_ROUTING_NOT_FOUND = 'ErrorRoutingNotFound'
const { resolvePath } = useNavigationSearch()
const route = useRoute()
const { locale } = useI18n()
const { isPreview, version, removeTrailingSlash } = useStoryblokHelper()
const { public: config } = useRuntimeConfig()
const storyblokApi = useStoryblokApi()
const { getCategoryUrl } = useAlgoliaHelper()

routePath.value = removeTrailingSlash(route.path.replace(`${locale.value}`, '').replace('//', '/'))

const routeSlug = computed((): string[] => {
  const route = routePath.value.replace(/^\/+/, '')

  if (!config.baseWebsiteHandle || config.baseWebsiteHandle === '') {
    return routePath.value === '/' ? ['home'] : [route]
  }

  switch (routePath.value) {
    case '/': {
      return [`${config.baseWebsiteHandle}/home`, 'home']
    }
    default: {
      const websiteRoute = `${config.baseWebsiteHandle}/${route}`
      return [websiteRoute, `${websiteRoute}/`, route, `${route}/`]
    }
  }
})

/**
 * Load both Storyblok CMS and Shopware 6 resolvers in parallel
 */
const [
  { data: _storyblokData },
  { data: seoResult }
] = await Promise.all([
  // Storyblok CMS resolver
  useCachedAsyncData(routeKey.value, async () => {
    return await storyblokApi.get(
      'cdn/stories',
      {
        version: isPreview(route.query) ? 'draft' : version.value,
        by_slugs: routeSlug.value.join(','),
        resolve_relations: [
          'type-blog.author',
          'type-blog.categories'
        ],
        resolve_links: 'url',
        language: locale.value
      }
    )
  }),
  // Shopware 6 page resolver
  useAsyncData(
    'cmsResponse' + getCategoryUrl(route.path),
    async () => {
      const result = await resolvePath(getCategoryUrl(route.path))
      return result ?? null
    }
  )
])

// TODO: casting type, should find a better way to instruct storyblok api response
const data = _storyblokData.value?.data as unknown as ISbResponseData

storyblokRenderComponent.value = data?.stories?.find((story: ISbStoryData) => story.full_slug.startsWith(config.baseWebsiteHandle)) || data?.stories?.[0] || null

/**
 * Process navigation context after seoResult is available
 * This depends on seoResult so it must run after the parallel requests
 */
const { routeName, foreignKey } = useNavigationContext(
  seoResult as Ref<Schemas['SeoUrl']>
)

onMounted(() => {
  if (storyblokRenderComponent.value?.id) {
    useStoryblokBridge(
      storyblokRenderComponent.value.id,
      evStory => (storyblokRenderComponent.value = evStory),
      {
        resolveRelations: [
          'type-blog.author',
          'type-blog.categories'
        ],
        resolveLinks: 'url'
      }
    )
  }
})

function render () {
  /**
   * Load Storyblok component if exist
   */
  if (storyblokRenderComponent.value) {
    const storyblokComponentNameToResolve = pascalCase(storyblokRenderComponent.value?.content.component as string)
    const storyblokComponent = storyblokComponentNameToResolve && defineAsyncComponent(() => import(`~/storyblok/type/${storyblokComponentNameToResolve}.vue`))

    return h('div', h(storyblokComponent, {
      // eslint-disable-next-line camelcase
      story: (({ id, uuid, name, slug, full_slug }) => ({ id, uuid, name, slug, full_slug }))(storyblokRenderComponent.value),
      blok: storyblokRenderComponent.value?.content
    }))
  }

  /**
   * Load shopware components if exist
   */
  const componentNameToResolve = pascalCase(routeName.value as string)
  const cmsView = routeName.value && defineAsyncComponent(() => import(`~/components/shopware/${componentNameToResolve}/${componentNameToResolve}.vue`))

  if (cmsView) {
    if (cmsView === componentNameToResolve) {
      return h('div', {}, 'Problem resolving component: ' + routeName.value)
    }

    return h('div', h(cmsView, {
      navigationId: foreignKey.value
    }))
  }

  /**
   * Load 404 error component
   */
  if (!routeName.value) {
    return h('div', h(resolveComponent(ERROR_ROUTING_NOT_FOUND)))
  }

  /**
   * Default loading state
   */
  return h('div', {}, 'Loading...')
}

</script>

<template>
  <render :key="route.path.split('/f/')[0]" />
</template>
