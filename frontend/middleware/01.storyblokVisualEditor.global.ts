export default defineNuxtRouteMiddleware((to, from) => {
  const { isPreview, removeTrailingSlash } = useStoryblokHelper()
  const { public: config } = useRuntimeConfig()

  if ((config.baseWebsiteHandle || config.baseWebsiteHandle !== '') && isPreview(to.query)) {
    let url = ''
    const previewUrl = to.fullPath.replace(`/${config.baseWebsiteHandle}`, '')

    if (previewUrl.startsWith('/category/')) {
      url = removeTrailingSlash(previewUrl.replace('/category/', '/'))
    } else {
      url = previewUrl
    }

    if (to.fullPath !== url) {
      return navigateTo(url)
    }
  }
})
