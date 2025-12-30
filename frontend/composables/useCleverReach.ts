import type { CleverReachResponse, MailingGroupResponse, SubscriberResponse, TokenResponse } from '~/types/cleverreach'

export function useCleverReach () {
  const apiUrl = 'https://rest.cleverreach.com/v3'
  const { public: config, cleverreach } = useRuntimeConfig()

  // Function to fetch the OAuth2 token
  const getAccessToken = async (): Promise<CleverReachResponse<TokenResponse>> => {
    try {
      const response = await $fetch<TokenResponse>(config.cleverreach.tokenUrl, {
        method: 'POST',
        body: {
          grant_type: 'client_credentials',
          client_id: cleverreach.clientId,
          client_secret: cleverreach.clientSecret
        }
      })

      return {
        data: response
      }
    } catch (error) {
      return {
        error: (error as Error).message || 'Failed to get access token'
      }
    }
  }

  // Function to add a subscriber
  const addSubscriber = async (email: string, listId: string, attributes: [key:string]): Promise<CleverReachResponse<SubscriberResponse>> => {
    try {
      const token = await getAccessToken()
      const currentDate = Date.now()

      const response = await $fetch<SubscriberResponse>(`${apiUrl}/groups.json/${listId}/receivers`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token.data?.access_token}`
        },
        body: JSON.stringify({
          email,
          registered: currentDate,
          attributes
        })
      })

      return {
        data: response
      }
    } catch (error) {
      return {
        error: (error as Error).message || 'Failed to add subscriber'
      }
    }
  }

  const fetchMailingGroups = async (): Promise<CleverReachResponse<MailingGroupResponse[]>> => {
    try {
      const token = await getAccessToken()

      const response = await $fetch<MailingGroupResponse[]>(`${apiUrl}/groups.json`, {
        headers: {
          Authorization: `Bearer ${token.data?.access_token}`
        }
      })

      return {
        data: response
      }
    } catch (error) {
      return {
        error: (error as Error).message || 'Failed to fetch mailing groups'
      }
    }
  }

  return {
    addSubscriber,
    fetchMailingGroups
  }
}
