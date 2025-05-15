<script setup>
import { ref, computed } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import { useTranslation } from '@/Composables/useTranslation';
import PlayerPanel from '@/Components/PlayerPanel.vue';
import HamburgerMenu from '@/Components/HamburgerMenu.vue';

const { trans } = useTranslation();
const page = usePage();

const user = computed(() => page.props.user ?? { name: 'Player' });
const userStatistics = computed(() => page.props.userStatistics ?? { level: 1, current_health: 0, max_health: 100, current_experience: 0, next_level_experience: 100 });
const userClassName = computed(() => page.props.userClassName ?? '');

const showDropdown = ref(false);
const toggleDropdown = () => { showDropdown.value = !showDropdown.value; };
const closeDropdown = () => { showDropdown.value = false; };
const logout = () => { router.post(route('logout')); };

const isActive = (routeName) => {
  const currentUrl = page.url;
  return currentUrl.includes(routeName);
};

const profileImageUrl = '/images/default-profile.png'; // Replace with real avatar if available
</script>

<template>
  <header class="bg-stone-900 shadow-md">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between px-2 md:px-4 py-2 relative">
      <!-- Left: Logo and Navigation -->
      <div class="flex items-center gap-2 md:gap-4 w-full md:w-auto justify-between md:justify-start">
        <img src="/images/logo.png" alt="Logo" class="w-32 md:w-40 h-8 md:h-10" />
        <!-- Navigation -->
        <nav class="hidden md:flex gap-4 lg:gap-6">
          <Link 
            :href="route('dashboard')" 
            class="text-amber-50 text-base md:text-lg font-medium px-2 md:px-4 py-1 md:py-2 transition-colors hover:bg-amber-500 hover:text-amber-50" 
            :class="{ 'border-b-4 border-amber-500 font-bold': isActive('dashboard') }"
          >{{ trans('Tasks') }}</Link>
          <!-- <Link :href="route('inventory')" class="nav-link" :class="{ 'border-b-4 border-amber-600': $page.url.startsWith(route('inventory')) }">{{ trans('Inventory') }}</Link>
          <Link :href="route('shops')" class="nav-link" :class="{ 'border-b-4 border-amber-600': $page.url.startsWith(route('shops')) }">{{ trans('Shops') }}</Link>
          <Link :href="route('party')" class="nav-link" :class="{ 'border-b-4 border-amber-600': $page.url.startsWith(route('party')) }">{{ trans('Party') }}</Link>
          <Link :href="route('group')" class="nav-link" :class="{ 'border-b-4 border-amber-600': $page.url.startsWith(route('group')) }">{{ trans('Group') }}</Link>
          <Link :href="route('challenges')" class="nav-link" :class="{ 'border-b-4 border-amber-600': $page.url.startsWith(route('challenges')) }">{{ trans('Challenges') }}</Link>
          <Link :href="route('help')" class="nav-link" :class="{ 'border-b-4 border-amber-600': $page.url.startsWith(route('help')) }">{{ trans('Help') }}</Link> -->
        </nav>
        <!-- Mobile Menu -->
        <HamburgerMenu class="md:hidden" />
      </div>
      <!-- Player Dropdown -->
      <div class="hidden md:block relative min-w-[48px] flex justify-end">
        <button @click="toggleDropdown" class="focus:outline-none">
          <svg class="w-8 h-8 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 10a4 4 0 100-8 4 4 0 000 8zm0 2c-3.314 0-6 1.343-6 3v1a1 1 0 001 1h10a1 1 0 001-1v-1c0-1.657-2.686-3-6-3z" />
          </svg>
        </button>
        <div v-if="showDropdown" @click.away="closeDropdown" class="absolute top-10 right-0 mt-2 w-44 bg-stone-700 rounded-lg shadow-lg border border-stone-600 py-1 z-50">
          <Link :href="route('character')" class="block px-4 py-2 text-sm font-bold text-amber-50 hover:bg-stone-600 transition-colors">{{ trans('Stats') }}</Link>
          <Link :href="route('settings')" class="block px-4 py-2 text-sm font-bold text-amber-50 hover:bg-stone-600 transition-colors">{{ trans('Settings') }}</Link>
          <button @click="logout" class="block w-full text-left px-4 py-2 text-sm font-bold text-red-400 hover:bg-stone-600 transition-colors">{{ trans('Log Out') }}</button>
        </div>
      </div>
    </div>
    <!-- Bottom Row: PlayerPanel -->
    <div class="bg-stone-800 px-2 md:px-4 py-2">
      <div class="max-w-7xl mx-auto">
        <PlayerPanel minimal />
      </div>
    </div>
  </header>
</template>

<style scoped>
.nav-link {
  color: #fff;
  font-weight: 500;
  font-size: 1rem;
  padding: 0.25rem 0.75rem;
  border-radius: 0.375rem;
  transition: background 0.2s, color 0.2s;
}
.nav-link:hover {
  background: #e5c100;
  color: #4e3c4b;
}

@media (max-width: 768px) {
  .nav-link {
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
  }
}
</style> 