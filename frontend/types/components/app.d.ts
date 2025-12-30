import type {Hit} from "instantsearch.js";
import type {RefinementListItem} from "instantsearch.js/es/connectors/refinement-list/connectRefinementList";
import type { Icon } from "~/types/components/base";

export interface AppFooter {
  showNewsletter: boolean
  showPreFooter: boolean
}

export interface AppNotification {
  id: number
  title?: string,
  message: string
  type: 'success' | 'error' | 'warning' | 'info'
  duration?: number
}

interface AppNotificationState {
  notifications: AppNotification[]
}