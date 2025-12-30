export const useSidebarWishlist = () => {
  const route = useRoute()
  const sidebarWishlistDisplayStatus = useState<boolean>('sidebarWishlistDisplayStatus', () => false)

  const open = () => {
    sidebarWishlistDisplayStatus.value = true
  }

  const close = () => {
    sidebarWishlistDisplayStatus.value = false
  }

  watch(() => route.fullPath, () => {
    close()
  })

  return {
    sidebarWishlistDisplayStatus,
    open,
    close
  }
}
