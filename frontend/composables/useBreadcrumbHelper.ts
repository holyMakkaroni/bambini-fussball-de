import type { Breadcrumb } from '~/types/shopware'

export const useBreadcrumbHelper = (path: string|false = false) => {
  const { path: routePath } = useRoute()
  const { cleanUrl } = useStoryblokHelper()
  const _path = path || routePath
  const breadcrumbs: Breadcrumb[] = []
  const getName = (url: string) => {
    const titleWord = (str: string) => {
      if (!str.length) {
        return str
      }
      return decodeURIComponent(`${str[0].toUpperCase()}${str.slice(1)}`)
    }
    const kebabToTitle = (str: string) => {
      if (!str.length) {
        return str
      }
      return str.split('-').map(titleWord).join(' ')
    }

    return kebabToTitle(url)
  }

  const generateBreadcrumb = (currentPath: string) => {
    const cleanPath = cleanUrl(currentPath)
    if (cleanPath === '/') {
      return breadcrumbs
    }

    const params = cleanPath.split('/')
    let breadcrumbPath = ''

    params.map((param: string) => {
      if (!param) {
        return true
      }

      breadcrumbPath = `${breadcrumbPath}/${param}`
      const match = breadcrumbPath === _path

      if (match) {
        breadcrumbs.push({
          name: getName(param),
          path: ''
        })
      } else {
        breadcrumbs.push({
          name: getName(param),
          path: breadcrumbPath
        })
      }

      return breadcrumbs
    })
  }

  const isPenultimate = (breadcrumb: Breadcrumb) => {
    const length = breadcrumbs.length

    if (length > 1) {
      return breadcrumb.name === breadcrumbs[length - 2]?.name
    }

    if (length === 1) {
      return breadcrumb.name === breadcrumbs[0]?.name
    }
  }

  const removePath = (segments: string[]) => {
    breadcrumbs.forEach((item) => {
      if (segments.includes(item.name)) {
        item.path = ''
      }
    })

    return breadcrumbs
  }

  const replaceLastElement = (breadcrumbs: Breadcrumb[], lastElement: Breadcrumb) => {
    breadcrumbs.pop()
    breadcrumbs.push(lastElement)
  }

  generateBreadcrumb(_path)

  return {
    isPenultimate,
    breadcrumbs,
    removePath,
    replaceLastElement
  }
}
