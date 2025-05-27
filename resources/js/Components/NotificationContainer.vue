<script setup>
import { useNotification } from '@/Composables/useNotification';
import Notification from '@/Components/Notification.vue';

const { notifications, removeNotification } = useNotification();
</script>

<template>
  <div class="fixed top-0 right-0 z-50 p-4 space-y-4">
    <Notification
      v-for="notification in notifications"
      :key="notification.id"
      :message="notification.message"
      :type="notification.type"
      :duration="notification.duration"
      @close="removeNotification(notification.id)"
    />
  </div>
</template> 