<script setup>
import { useNotification } from '@/Composables/useNotification'
import Notification from '@/Components/Notification.vue'

const { notifications, removeNotification } = useNotification()
</script>

<template>
  <div class="fixed top-0 right-0 z-50 p-4">
    <div class="flex flex-col gap-4">
      <TransitionGroup
        enter-active-class="transition-all duration-300 ease-in-out"
        enter-from-class="opacity-0 transform translate-x-4"
        enter-to-class="opacity-100 transform translate-x-0"
        leave-active-class="transition-all duration-300 ease-in-out"
        leave-from-class="opacity-100 transform translate-x-0"
        leave-to-class="opacity-0 transform translate-x-4"
      >
        <Notification
          v-for="notification in notifications"
          :key="notification.id"
          :message="notification.message"
          :type="notification.type"
          :duration="notification.duration"
          @close="removeNotification(notification.id)"
        />
      </TransitionGroup>
    </div>
  </div>
</template> 
