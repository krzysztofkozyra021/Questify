<script setup>
import { useTranslation } from "@/Composables/useTranslation";

const { trans } = useTranslation();

defineProps({
  message: {
    type: String,
  },
})
</script>

<template>
  <div v-show="message">
    <p class="text-sm text-red-600 dark:text-red-400">
      {{ trans(message) }}
    </p>
  </div>
</template>
