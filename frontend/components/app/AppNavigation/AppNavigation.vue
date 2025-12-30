<script setup lang="ts">
import { getCategoryUrl } from '@shopware-pwa/helpers-next'
import type { Properties, PropertyMapping } from '~/types/shopware'
import type { Schemas } from '#shopware'
import { useConfigStore } from '~/stores/config'
import type { StoryblokDefaultConfig } from '~/types/storyblok.config'
import type { BaseSidebarNavigationItem } from '~/types/components/base'

defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

const configStore = useConfigStore()
const configDefault: StoryblokDefaultConfig | null = configStore.defaultConfig
const { storyblokUrl } = useStoryblokHelper()
const { apiClient } = useShopwareContext()
const { t, locale } = useI18n()
const { close, mainNavigationDisplayStatus } = useMainNavigation()
const mainItems = ref<BaseSidebarNavigationItem[]>([])

const { data: navigationElements } = await useCachedAsyncData(
  `main-navigation-${locale.value}`,
  async () => {
    const navigationResponse = await apiClient.invoke('readNavigation post /navigation/{activeId}/{rootId}', {
      headers: {
        'sw-include-seo-urls': true
      },
      pathParams: {
        activeId: 'main-navigation',
        rootId: 'main-navigation'
      },
      body: {
        depth: 3
      }
    })

    return navigationResponse.data || []
  },
  {
    serverMaxAge: 3600,
    serverCacheTags: [
      'navigation:main'
    ],
    transform: (categories) => {
      const transformCategories = (category: Schemas['Category']) => {
        return {
          id: category.id,
          parentId: category.parentId,
          type: category.type,
          linkType: category.linkType,
          linkNewTab: category.linkNewTab,
          internalLink: category.internalLink,
          externalLink: category.externalLink,
          name: category.translated.name,
          seoUrls: category.seoUrls,
          breadcrumb: category.breadcrumb,
          translated: category.translated,
          customFields: category.translated.customFields,
          children: category.children.map((category: Schemas['Category']) => {
            return transformCategories(category)
          })
        }
      }

      return categories.map((category: Schemas['Category']) => {
        return transformCategories(category)
      })
    }
  })

const collectPropertyIds = (data: Schemas['NavigationRouteResponse']): string[] => {
  const propertyIds = new Set<string>()

  const recurse = (items: Schemas['NavigationRouteResponse']) => {
    items.forEach((item) => {
      if (item.customFields && item.customFields.properties) {
        item.customFields.properties.forEach((prop: string) => propertyIds.add(prop))
      }

      if (item.children) {
        recurse(item.children)
      }
    })
  }

  recurse(data)

  return Array.from<string>(propertyIds)
}

if (!navigationElements.value) {
  throw new Error('No navigation found')
}

const mainNavigationPropertyIds = collectPropertyIds(navigationElements.value)

const mainNavigationPropertyMapping = useState<PropertyMapping>('mainNavigationPropertyMapping', () => ({
  properties: {}
}))

const { path } = useRoute()
const { generateSeoUrl, fetchPropertiesById } = useSeoFilterUrl(mainNavigationPropertyMapping, path)

mainNavigationPropertyMapping.value.properties = await fetchPropertiesById(mainNavigationPropertyIds) ?? {}

const filterGroupsByOptionIds = (properties: Properties, optionIds: string[]) => {
  const result: Properties = {}

  for (const groupId in properties) {
    if (Object.prototype.hasOwnProperty.call(properties, groupId)) {
      const group = properties[groupId]

      const matchingOptions = group.options.filter(option =>
        optionIds.includes(option.id)
      )

      if (matchingOptions.length > 0) {
        result[groupId] = { ...group, options: matchingOptions }
      }
    }
  }

  return result
}

const transformCategory: (menuItems: Schemas['NavigationRouteResponse']) => BaseSidebarNavigationItem[] = (menuItems: Schemas['NavigationRouteResponse']) => {
  return menuItems.map((item) => {
    const seoFilterUrl = generateSeoUrl(filterGroupsByOptionIds(mainNavigationPropertyMapping.value.properties, item.customFields?.properties || []))

    return {
      id: item.id,
      parent_id: item.parentId,
      name: item.name,
      url: seoFilterUrl ? getCategoryUrl(item) + '/' + seoFilterUrl : getCategoryUrl(item),
      children: item.children ? transformCategory(item.children) : []
    }
  })
}

mainItems.value = transformCategory(navigationElements.value)

const route = useRoute()
const storyblokApi = useStoryblokApi()
const { version, isPreview, removeTrailingSlash } = useStoryblokHelper()
const { buildHierarchy } = useUtilities()

const { data: aboutItems } = useCachedAsyncData<BaseSidebarNavigationItem[]>(
  `folderNavigationData-${configDefault?.content?.about_navigation}-${locale.value}`,
  async () => {
    const response = await storyblokApi.get('cdn/links', {
      version: isPreview(route.query) ? 'draft' : version.value,
      starts_with: `${configDefault?.content?.about_navigation}/`,
      per_page: 100,
      language: locale.value
    })

    return response.data.links
  },
  {
    serverMaxAge: 3600,
    serverCacheTags: [
      'navigation:about-us'
    ],
    transform: (links: BaseSidebarNavigationItem[]) => {
      return buildHierarchy(Object.values(links).map((link) => {
        return {
          id: link.id,
          parent_id: link.parent_id,
          name: link.name,
          url: removeTrailingSlash(link.real_path)
        }
      }))
    }
  }
)

const { data: serviceItems } = useCachedAsyncData<BaseSidebarNavigationItem[]>(
  `folderNavigationData-${configDefault?.content?.service_navigation}-${locale.value}`,
  async () => {
    const response = await storyblokApi.get('cdn/links', {
      version: isPreview(route.query) ? 'draft' : version.value,
      starts_with: `${configDefault?.content?.service_navigation}/`,
      per_page: 100,
      language: locale.value
    })

    return response.data.links
  },
  {
    serverMaxAge: 3600,
    serverCacheTags: [
      'navigation:service'
    ],
    transform: (links: BaseSidebarNavigationItem[]) => {
      return buildHierarchy(Object.values(links).map((link) => {
        return {
          id: link.id,
          parent_id: link.parent_id,
          name: link.name,
          url: removeTrailingSlash(link.real_path)
        }
      }))
    }
  }
)

const footerLinkItems = computed(() => {
  return configDefault?.content?.footer_links
})
</script>

<template>
  <div class="c-app-navigation">
    <Teleport to="#teleports">
      <AppSidebar
        :show="mainNavigationDisplayStatus"
        @close-sidebar="close">
        <template #default>
          <div class="flex flex-col pt-[65px] pb-8 h-full">
            <BaseTabs
              :tabs="[
                {
                  label: t('components.app.navigation.tab.product'),
                  name: 'product'
                },
                {
                  label: t('components.app.navigation.tab.about'),
                  name: 'about'
                },
                {
                  label: t('components.app.navigation.tab.service'),
                  name: 'services'
                },
              ]"
              class="flex-1"
              tab-class="mb-9 px-8 h-[24px]"
              content-class="h-full overflow-y-scroll">
              <template #product>
                <BaseNavigationItem :navigation-items="mainItems" />
              </template>
              <template #about>
                <BaseNavigationItem
                  v-if="aboutItems"
                  :navigation-items="aboutItems" />
              </template>
              <template #services>
                <BaseNavigationItem
                  v-if="serviceItems"
                  :navigation-items="serviceItems" />
              </template>
            </BaseTabs>

            <ul
              v-if="footerLinkItems"
              class="pt-8 px-8 flex gap-x-8">
              <li
                v-for="item in footerLinkItems"
                :key="item._uid"
                v-editable="item"
                role="listitem">
                <NuxtLink
                  :to="storyblokUrl(item.link)"
                  :target="item.link.target"
                  class="text-xs font-medium text-secondary/50">
                  {{ item.title }}
                </NuxtLink>
              </li>
            </ul>
          </div>
        </template>
      </AppSidebar>
    </Teleport>
  </div>
</template>
