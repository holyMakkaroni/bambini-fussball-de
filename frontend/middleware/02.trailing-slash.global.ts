export default defineNuxtRouteMiddleware((to, from) => {
  const { path, query, hash } = to

  if (path !== '/' && path.endsWith('/')) {
    const nextPath = path.replace(/\/+$/, '') || '/'
    const nextRoute = {
      path: nextPath,
      query,
      hash
    }

    return navigateTo(nextRoute, {
      redirectCode: 301
    })
  }
})
