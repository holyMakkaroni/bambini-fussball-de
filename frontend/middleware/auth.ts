export default defineNuxtRouteMiddleware(async (to) => {
  const isLoggedIn = ref<boolean>(false)
  const { apiClient } = useShopwareContext()

  await apiClient.invoke('readCustomer post /account/customer')
    .then(() => {
      isLoggedIn.value = true
    }).catch(() => {
      isLoggedIn.value = false
    })

  if (to.path !== '/' && !isLoggedIn.value) {
    return navigateTo('/')
  }
})
