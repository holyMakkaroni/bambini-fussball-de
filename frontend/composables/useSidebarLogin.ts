export const useSidebarLogin = () => {
  const route = useRoute()
  const sidebarLoginDisplayStatus = useState<boolean>('sidebarLoginDisplayStatus', () => false)

  const open = () => {
    sidebarLoginDisplayStatus.value = true
  }

  const close = () => {
    sidebarLoginDisplayStatus.value = false
  }

  watch(() => route.fullPath, () => {
    close()
  })

  return {
    sidebarLoginDisplayStatus,
    open,
    close
  }
}
