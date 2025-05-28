<script setup>
import { ref, onMounted } from 'vue'

const isLoading = ref(true)

onMounted(() => {
  const minimumLoadTime = 800 // minimum time in milliseconds
  const loadPromise = new Promise((resolve) => {
    if (document.readyState === 'complete') {
      resolve()
    } else {
      window.addEventListener('load', resolve)
    }
  })

  const timeoutPromise = new Promise((resolve) => {
    setTimeout(resolve, minimumLoadTime)
  })

  // Wait for both the load event AND the minimum time
  Promise.all([loadPromise, timeoutPromise]).then(() => {
    isLoading.value = false
  })
})
</script>

<template>
  <Transition name="fade">
    <div v-if="isLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900">
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
        <!-- Loading bar -->
        <div class="w-64 h-2 mt-4 bg-stone-800 rounded-full overflow-hidden">
          <div class="h-full bg-amber-600 rounded-full animate-[loading_1s_ease-in-out]"></div>
        </div>
      </div>
    </div>
  </Transition>
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

.fade-enter-active,
.fade-leave-active {
  transition: all 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style> 