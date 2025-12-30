export interface AddSubscriberRequest {
  email: string,
  listId: string,
  attributes: [key:string]
}

export interface MailingGroupResponse {
  id: string,
  name: string,
  locked: boolean,
  backup: boolean,
  receiver_info: string,
  stamp: number,
  last_mailing: number,
  last_changed: number
}

export interface CleverReachResponse<T> {
  data?: T,
  error?: string,
}

export interface TokenResponse {
  access_token: string,
  expires_in: number,
  scope: string,
  refresh_token: string
}

export interface SubscriberResponse {
  success: boolean
}