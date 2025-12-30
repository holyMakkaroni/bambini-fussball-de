export const useMainNavigation = () => {
  const route = useRoute()
  const mainNavigationDisplayStatus = useState<boolean>('mainNavigationDisplayStatus', () => false)

  const open = () => {
    mainNavigationDisplayStatus.value = true
  }

  const close = () => {
    mainNavigationDisplayStatus.value = false
  }

  watch(() => route.fullPath, () => {
    close()
  })

  return {
    mainNavigationDisplayStatus,
    open,
    close
  }
}
