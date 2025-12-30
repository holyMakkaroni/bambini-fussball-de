import type { Breadcrumb } from '~/types/shopware'

export const useBreadcrumbStore = defineStore('breadcrumb', () => {
  const breadcrumb = ref<Breadcrumb[]>([])

  function clearBreadcrumb () {
    breadcrumb.value = []
  }

  function setBreadcrumb (entries: Breadcrumb[]) {
    breadcrumb.value = entries
  }

  return {
    breadcrumb,
    clearBreadcrumb,
    setBreadcrumb
  }
})
