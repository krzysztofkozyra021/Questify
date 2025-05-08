<script setup>
import { ref, onMounted } from 'vue'

const isLoading = ref(true)

onMounted(() => {
  setTimeout(() => {
    isLoading.value = false
  }, 1000) // Show preloader for 1 second
})
</script>

<template>
  <div v-if="isLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-800">
    <div class="text-center">
      <!-- Animated shield icon -->
      <div class="mb-8 animate-bounce">
        <svg class="w-16 h-16 mx-auto text-amber-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- Main shield shape -->
          <path d="M12 2C12 2 4 4 4 8V12C4 16 8 20 12 22C16 20 20 16 20 12V8C20 4 12 2 12 2Z" 
                stroke="currentColor" 
                stroke-width="1.5" 
                fill="none"/>
          
          <!-- Shield border -->
          <path d="M12 2C12 2 4 4 4 8V12C4 16 8 20 12 22C16 20 20 16 20 12V8C20 4 12 2 12 2Z" 
                stroke="currentColor" 
                stroke-width="0.5" 
                fill="none"
                class="opacity-50"/>
          
          <!-- Cross design -->
          <path d="M12 6L12 18" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
          <path d="M8 12L16 12" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
          
          <!-- Decorative circles -->
          <circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="0.5" fill="none"/>
          <circle cx="12" cy="12" r="1" stroke="currentColor" stroke-width="0.5" fill="none"/>
          
          <!-- Corner decorations -->
          <path d="M6 6L8 8" stroke="currentColor" stroke-width="0.5" stroke-linecap="round"/>
          <path d="M16 6L18 8" stroke="currentColor" stroke-width="0.5" stroke-linecap="round"/>
          <path d="M6 16L8 18" stroke="currentColor" stroke-width="0.5" stroke-linecap="round"/>
          <path d="M16 16L18 18" stroke="currentColor" stroke-width="0.5" stroke-linecap="round"/>
        </svg>
      </div>
      
      <!-- Loading text with typing animation -->
      <div class="text-2xl font-bold text-amber-100">
        <span class="inline-block animate-pulse">Preparing your quest...</span>
      </div>
      
      <!-- Loading bar -->
      <div class="w-64 h-2 mt-4 bg-slate-700 rounded-full overflow-hidden">
        <div class="h-full bg-amber-600 rounded-full animate-[loading_1s_ease-in-out]"></div>
      </div>
    </div>
  </div>
</template>

<style>
@keyframes loading {
  0% {
    width: 0%;
  }
  100% {
    width: 100%;
  }
}
</style> 