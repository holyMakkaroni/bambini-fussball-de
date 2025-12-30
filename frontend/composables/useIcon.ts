export const useIcon = () => {
  const { data: icons } = useFetch('/api/icons')

  return {
    icons
  }
}
