import { ref } from 'vue';

type NotificationType = 'success' | 'error' | 'info' | 'warning';

interface Notification {
  id: number;
  message: string;
  type: NotificationType;
  duration: number;
}

const notifications = ref<Notification[]>([]);

export function useNotification() {
  const addNotification = (message: string, type: NotificationType = 'success', duration: number = 3000): void => {
    const id = Date.now();
    notifications.value.push({
      id,
      message,
      type,
      duration
    });

    // Remove notification after duration
    setTimeout(() => {
      removeNotification(id);
    }, duration);
  };

  const removeNotification = (id: number): void => {
    notifications.value = notifications.value.filter(notification => notification.id !== id);
  };

  return {
    notifications,
    addNotification,
    removeNotification
  };
}