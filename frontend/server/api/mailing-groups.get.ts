import { useCleverReach } from '~/composables/useCleverReach'

export default defineEventHandler(async () => {
  const { fetchMailingGroups } = useCleverReach()
  const result = await fetchMailingGroups()

  return result.data?.map((group) => {
    return {
      name: group.name,
      value: group.id
    }
  })
})
