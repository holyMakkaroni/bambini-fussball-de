import slugify from 'slugify'
import type {
  BaseSidebarNavigationItem,
  BaseSidebarNavigationItemRaw
} from '~/types/components/base'
export const useUtilities = () => {
  const { locale } = useI18n()
  const formatDate = (timestamp: string) => {
    return new Date(timestamp).toLocaleDateString(locale.value, {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  }

  const dayNumber = (timestamp: string) => {
    return new Date(timestamp).toLocaleDateString(locale.value, {
      day: 'numeric'
    })
  }

  const monthName = (timestamp: string) => {
    return new Date(timestamp).toLocaleDateString(locale.value, {
      month: 'long'
    })
  }

  const time = (timestamp: string) => {
    return new Date(timestamp).toLocaleTimeString(locale.value, {
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  const stripHtml = (html: string) => {
    return html.replace(/(<([^>]+)>)/ig, '')
  }

  const generateUrl = (title: string) => {
    return slugify(title, {
      lower: true,
      strict: true
    })
  }

  const buildHierarchy = (links: BaseSidebarNavigationItemRaw[], parentUrl = false) => {
    const tree: BaseSidebarNavigationItem[] = []
    const nodesById: Record<number, BaseSidebarNavigationItem> = {}

    links.forEach((item) => {
      if (item.id) {
        nodesById[item.id] = {
          id: item.id,
          parent_id: item.parent_id,
          name: item.name,
          url: item.url,
          children: []
        }
      }
    })

    links.forEach((item) => {
      if (item.id) {
        const node = nodesById[item.id]

        if (item.parent_id && nodesById[item.parent_id]) {
          nodesById[item.parent_id].children.push(node)

          if (!parentUrl) {
            nodesById[item.parent_id].url = ''
          }
        } else {
          tree.push(node)
        }
      }
    })

    return tree
  }

  const buildHierarchyFolders = (data: BaseSidebarNavigationItem[]) => {
    const urlMap = new Map()
    const result: BaseSidebarNavigationItem[] = []

    if (!data) {
      return []
    }

    // First pass: create a map of URL to item
    data.forEach((item) => {
      item.children = []
      urlMap.set(item.url, item)
    })

    // Second pass: build the hierarchy
    data.forEach((item) => {
      const urlParts = item.url.split('/').filter(Boolean)

      if (urlParts.length === 1) {
        result.push(item)
      } else if (urlParts.length > 1) {
        const parentUrl = `/${urlParts.slice(0, -1).join('/')}`
        const parent = urlMap.get(parentUrl)

        if (parent) {
          parent.children.push(item)
        } else {
          result.push(item)
        }
      }
    })

    if (!result?.[0]) {
      return []
    }

    const childElement = result[0].children || []
    result[0].children = []
    const rootElement = result[0]

    return [rootElement, ...childElement]
  }

  return {
    formatDate,
    dayNumber,
    monthName,
    time,
    stripHtml,
    generateUrl,
    buildHierarchy,
    buildHierarchyFolders
  }
}
