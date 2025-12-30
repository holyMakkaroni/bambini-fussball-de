import { AddSubscriberRequest } from '~/types/cleverreach'
import { useCleverReach } from '~/composables/useCleverReach'

export default defineEventHandler(async (event): Promise<{ statusCode: number; body: { message?: string; error?: string } }> => {
  const body: AddSubscriberRequest = await readBody(event)
  const { addSubscriber } = useCleverReach()

  if (!body.email || !body.listId) {
    return {
      statusCode: 400,
      body: {
        error: 'Email and listId are required'
      }
    }
  }

  const result = await addSubscriber(body.email, body.listId, body.attributes)

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
    body: {
      message: 'Subscriber added successfully'
    }
  }
})
