import { useNotificationStore } from '~/stores/notification'
import type { AppNotification } from '~/types/components/app'

export function useNotification () {
  const store = useNotificationStore()

  const addNotification = (
    title: string,
    message: string,
    type: AppNotification['type'] = 'info',
    duration: number = 5000
  ) => {
    const id = store.addNotification({ title, message, type, duration })
    if (duration > 0) {
      setTimeout(() => {
        store.removeNotification(id)
      }, duration)
    }
    return id
  }

  const removeNotification = (id: number) => {
    store.removeNotification(id)
  }

  const clearAllNotifications = () => {
    store.clearAllNotifications()
  }

  return {
    notifications: store.notifications,
    addNotification,
    removeNotification,
    clearAllNotifications
  }
}
