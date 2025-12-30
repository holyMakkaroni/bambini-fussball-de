export const useSidebarFilterMobile = () => {
  const route = useRoute()
  const sidebarFilterMobileDisplayStatus = useState<boolean>('sidebarFilterMobileDisplayStatus', () => false)

  const open = () => {
    sidebarFilterMobileDisplayStatus.value = true
  }

  const close = () => {
    sidebarFilterMobileDisplayStatus.value = false
  }

  watch(() => route.fullPath, () => {
    close()
  })

  return {
    sidebarFilterMobileDisplayStatus,
    open,
    close
  }
}
