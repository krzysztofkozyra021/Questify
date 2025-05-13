
<script setup>
import { useTranslation } from '@/Composables/useTranslation';

const props = defineProps({
  showNotification: Boolean,
  textToDisplay: String,
});

const { trans } = useTranslation();
</script>


<template>
 <div v-if="showNotification" 
 class="fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 ease-in-out z-50"
 :class="{ 'translate-x-0 opacity-100': showNotification, 'translate-x-full opacity-0': !showNotification }">
<div class="flex items-center space-x-2">
<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
</svg>
<span class="font-bold"></span>
<span>{{ trans(textToDisplay) }}</span>
</div>
</div>
</template>