<style scoped>
.pattern-background {
  position: absolute;
  inset: 0;
  background-image: url('/images/gaming-pattern.webp'); /* If using public directory */
  background-repeat: repeat;
  opacity: 0.3;
  z-index: 0;
}
</style>
<template>
  <div class="min-h-screen flex flex-col items-center justify-center relative overflow-hidden bg-white">

    <!-- RPG Background -->
    <div class="pattern-background"></div>

    <!-- Language switcher -->
    <div class="absolute top-4 right-4 z-10">
      <LanguageSwitcher />
    </div>

    <!-- Main content -->
    <div class="z-10 px-6 py-8 text-center">
      <h1 class="text-5xl md:text-6xl font-bold text-indigo-800 mb-4">Questify</h1>
      <p class="text-xl md:text-2xl text-gray-600 mb-8 max-w-md mx-auto">{{ trans('Transform your productivity into an epic adventure!') }}</p>
      <button
        @click="startAdventure"
        class="px-8 py-3 bg-indigo-600 text-white rounded-lg shadow-lg hover:bg-indigo-700 transition-colors duration-300 text-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-opacity-50"
      >
        {{ trans('Begin Your Quest') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'; // Add this import

const page = usePage();
const translations = computed(() => page.props.translations || {});

function trans(key) {
  return translations.value[key] || key;
}

function startAdventure() {
  window.location.href = '/dashboard';
}
</script>
