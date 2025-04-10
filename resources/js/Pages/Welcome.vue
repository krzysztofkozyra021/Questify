<template>
  <Head title="Welcome" />
  <div class="min-h-screen flex flex-col items-center justify-center relative overflow-hidden bg-white  dark:text-white">
    <!-- RPG Background -->
    <div class="pattern-background" />

    <!-- Language switcher -->
    <div class="absolute top-4 right-4 z-10">
      <LanguageSwitcher />
    </div>

    <!-- Main content -->
    <div class="z-10 px-6 py-8 text-center">
      <h1 class="text-5xl md:text-6xl font-bold text-indigo-800 dark:text-indigo-400 mb-4">Questify</h1>
      <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 mb-8 max-w-md mx-auto">
        {{ trans('Transform your productivity into an epic adventure!') }}
      </p>

      <!-- Primary Button -->
      <button
        v-if="$page?.props?.auth?.user"
        class="px-8 py-3 bg-indigo-600 text-white rounded-lg shadow-lg hover:bg-indigo-700 transition-colors duration-300 text-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-opacity-50 mb-4"
        @click="startAdventure"
      >
        {{ trans('Begin Your Quest') }}
      </button>

      <!-- Login/Register Buttons for guests -->
      <div v-else class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
        <Link
          :href="route('login')"
          class="px-8 py-3 bg-indigo-600 text-white rounded-lg shadow-lg hover:bg-indigo-700 transition-colors duration-300 text-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-opacity-50"
        >
          {{ trans('Log in') }}
        </Link>

        <Link
          v-if="canRegister"
          :href="route('register')"
          class="px-8 py-3 bg-indigo-600 bg-opacity-80 text-white rounded-lg shadow-lg hover:bg-indigo-700 transition-colors duration-300 text-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-opacity-50"
        >
          {{ trans('Register') }}
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useTranslation } from '@/composables/useTranslation.js'
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'
import {route} from 'ziggy-js'

defineProps({
  canLogin: {
    type: Boolean,
    default: false,
  },
  canRegister: {
    type: Boolean,
    default: false,
  },
})

const { trans } = useTranslation()

function startAdventure() {
  try {
    window.location.href = route('dashboard')
  } catch (error) {
    console.error('Error navigating to dashboard:', error)
    // Fallback jeśli route() nie działa
    window.location.href = '/dashboard'
  }
}

</script>

<style scoped>
.pattern-background {
  position: absolute;
  inset: 0;
  background-image: url('/images/gaming-pattern.webp');
  background-repeat: repeat;
  opacity: 0.3;
  z-index: 0;
}
</style>
