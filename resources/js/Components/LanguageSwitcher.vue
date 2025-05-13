<script setup>
import { ref, computed } from 'vue'
import { useTranslation } from '@/Composables/useTranslation'

const { trans, currentLocale, availableLocales, switchLanguage } = useTranslation()
const isOpen = ref(false)

const currentLanguage = computed(() => {
  return Object.entries(availableLocales.value).find(([_, code]) => code === currentLocale.value)?.[0] || 'English'
})
</script>

<template>
  <div class="relative">
    <button
      @click="isOpen = !isOpen"
      class="flex items-center space-x-2 px-4 py-2 bg-slate-700 text-stone-100 rounded-lg hover:bg-slate-600 transition-colors"
    >
      <span>{{ trans(currentLanguage) }}</span>
      <svg
        class="w-4 h-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M19 9l-7 7-7-7"
        />
      </svg>
    </button>

    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-48 bg-slate-700 rounded-lg shadow-lg py-1 z-50"
    >
      <button
        v-for="(code, name) in availableLocales"
        :key="code"
        @click="switchLanguage(code)"
        class="w-full text-left px-4 py-2 text-stone-100 hover:bg-slate-600 transition-colors"
        :class="{ 'bg-slate-600': currentLocale === code }"
      >
        {{ trans(name) }}
      </button>
    </div>
  </div>
</template>
