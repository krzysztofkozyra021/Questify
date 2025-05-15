<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useTranslation } from '@/Composables/useTranslation'
import { router } from '@inertiajs/vue3';

const { trans } = useTranslation()
const page = usePage()

// Obliczamy tytuł na podstawie aktualnej ścieżki
const currentTitle = computed(() => {
  const path = window.location.pathname
  if (path.includes('/settings')) {
    return trans('Settings')
  } else if (path.includes('/dashboard')) {
    return trans('Dashboard')
  } 
  return trans('Dashboard') // domyślny tytuł
})

const isMenuOpen = ref(false);

function logout() {
  router.post(route('logout'));
}

function toggleMenu() {
  isMenuOpen.value = !isMenuOpen.value;
}

const closeMenu = () => {
  isMenuOpen.value = false;
}
</script>

<template>
  <div class="bg-slate-800 border-b border-slate-700">
    <div class="py-1 mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center">
          <img src="/images/logo.png" alt="Logo" class="hidden sm:flex w-18 h-16 mr-3">
          <h1 class="text-xl font-bold text-slate-200">{{ currentTitle }}</h1>
        </div>

        <!-- Menu button -->
        <div class="flex items-center">
          <div class="relative">
            <button
              id="menu-button"
              @click="toggleMenu"
              class="text-slate-400 hover:text-slate-200 transition-colors p-2 rounded-lg hover:bg-slate-700/50"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
              </svg>
            </button>

            <!-- Dropdown menu -->
            <div
              id="menu"
              v-if="isMenuOpen"
              class="absolute right-0 mt-2 w-48 bg-slate-800 rounded-lg shadow-lg border border-slate-700 py-1 z-50"
            >
              <a
                :href="route('dashboard')"
                class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700 transition-colors"
              >
                {{ trans('Dashboard') }}
              </a>
              <a
                :href="route('settings')"
                class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700 transition-colors"
              >
                {{ trans('Settings') }}
              </a>
              <button
                @click="logout"
                class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-slate-700 transition-colors"
              >
                {{ trans('Log Out') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--
<aside class="w-80 min-h-screen bg-slate-800/95 border-r border-slate-700">
    <div class="p-4 h-full flex flex-col">

      <div class="mb-6">

      </div>
      <div class="flex-1 space-y-4">
        <ul class="space-y-2">
          <li>
            <OptionButton 
              optionText="Quests" 
              route="/dashboard"
              class="w-full px-4 py-2 text-slate-200 hover:bg-slate-700/50 rounded-lg transition-colors"
            />
          </li>
          <li>
            <OptionButton 
              optionText="Settings" 
              route="/settings"
              class="w-full px-4 py-2 text-slate-200 hover:bg-slate-700/50 rounded-lg transition-colors"
            />
          </li>
          <li>
            <OptionButton 
              optionText="Log out" 
              route="/logout"
              method="post"
              class="w-full px-4 py-2 text-slate-200 hover:bg-slate-700/50 rounded-lg transition-colors"
            />
          </li>
        </ul>
      </div>
    </div>
</aside>
-->
</template>