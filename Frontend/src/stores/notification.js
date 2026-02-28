import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useNotificationStore = defineStore('notification', () => {
  const notifications = ref([])
  const confirmData = ref(null)

  const add = (notification) => {
    const id = Date.now()
    const newNotification = {
      id,
      type: notification.type || 'success',
      title: notification.title || 'Berhasil!',
      message: notification.message || '',
      duration: notification.duration || 5000
    }
    
    notifications.value.push(newNotification)

    if (newNotification.duration > 0) {
      setTimeout(() => {
        remove(id)
      }, newNotification.duration)
    }
  }

  const remove = (id) => {
    notifications.value = notifications.value.filter(n => n.id !== id)
  }

  const confirm = (data) => {
    return new Promise((resolve) => {
      confirmData.value = {
        title: data.title || 'Konfirmasi',
        message: data.message || '',
        confirmText: data.confirmText || 'Ya, Lanjutkan',
        cancelText: data.cancelText || 'Batal',
        resolve
      }
    })
  }

  const resolveConfirm = (value) => {
    if (confirmData.value) {
      confirmData.value.resolve(value)
      confirmData.value = null
    }
  }

  const clearAll = () => {
    notifications.value = []
    confirmData.value = null
  }

  return { notifications, add, remove, confirm, confirmData, resolveConfirm, clearAll }
})
