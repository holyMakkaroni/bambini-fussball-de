import type { AppNotificationState, AppNotification } from '~/types/components/app'

export const useNotificationStore = defineStore('notifications', {
  state: (): AppNotificationState => ({
    notifications: []
  }),
  actions: {
    addNotification (notification: Omit<AppNotification, 'id'>) {
      const id = Date.now()
      this.notifications.unshift({ ...notification, id })
      return id
    },
    removeNotification (id: number) {
      const index = this.notifications.findIndex(n => n.id === id)
      if (index !== -1) {
        this.notifications.splice(index, 1)
      }
    },
    clearAllNotifications () {
      this.notifications = []
    }
  }
})
