import { useCleverReach } from '~/composables/useCleverReach'

export default defineEventHandler(async (): Promise<{ statusCode: number; body: any }> => {
  const { fetchMailingGroups } = useCleverReach()
  const result = await fetchMailingGroups()

  if (result.error) {
    return {
      statusCode: 500,
      body: {
        error: result.error
      }
    }
  }

  return {
    statusCode: 200,
    body: result.data
  }
})
