export const useSidebarCart = () => {
  const route = useRoute()
  const sidebarCartDisplayStatus = useState<boolean>('sidebarCartDisplayStatus', () => false)

  const open = () => {
    sidebarCartDisplayStatus.value = true
  }

  const close = () => {
    sidebarCartDisplayStatus.value = false
  }

  watch(() => route.fullPath, () => {
    close()
  })

  return {
    sidebarCartDisplayStatus,
    open,
    close
  }
}
