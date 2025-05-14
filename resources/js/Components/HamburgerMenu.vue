<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'
import { useTranslation } from '@/Composables/useTranslation'

const { trans } = useTranslation()
const isOpen = ref(false)
const menuRef = ref(null)
const buttonRef = ref(null)

const toggleMenu = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
}

// Close menu when clicking outside
const handleClickOutside = (event) => {
  const menu = menuRef.value
  const button = buttonRef.value
  if (isOpen.value && menu && !menu.contains(event.target) && !button.contains(event.target)) {
    isOpen.value = false
    document.body.style.overflow = ''
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.body.style.overflow = ''
})
</script>

<template>
  <div class="relative">
    <!-- Hamburger Button - Only visible on mobile -->
    <button
      id="hamburger-button"
      ref="buttonRef"
      @click="toggleMenu"
      class="sm:hidden p-2 rounded-lg hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-600"
      aria-label="Toggle menu"
    >
      <svg
        class="w-6 h-6 text-stone-100"
        :class="{ 'hidden': isOpen }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
      <svg
        class="w-6 h-6 text-stone-100"
        :class="{ 'hidden': !isOpen }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <!-- Backdrop - Only visible on mobile -->
    <div
      v-show="isOpen"
      class="sm:hidden fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out z-40"
      @click="toggleMenu"
    ></div>

    <!-- Mobile Menu - Only visible on mobile -->
    <div
      id="mobile-menu"
      ref="menuRef"
      v-show="isOpen"
      class="sm:hidden fixed top-0 left-0 h-full w-64 bg-slate-800 shadow-xl transform transition-transform duration-300 ease-in-out z-50"
      :class="{ 'translate-x-0': isOpen, '-translate-x-full': !isOpen }"
    >
      <div class="flex flex-col h-full">
        <!-- Menu Header -->
        <div class="p-4 border-b border-slate-700">
          <img src="/images/logo.png" alt="Questify Logo" class="h-8 w-auto" />
        </div>

        <!-- Menu Content -->
        <div class="flex-1 overflow-y-auto">
          <div class="p-4">
            <LanguageSwitcher />
          </div>
          <div class="border-t border-slate-700">
            <slot></slot>
          </div>
        </div>

        <!-- Menu Footer -->
        <div class="p-4 border-t border-slate-700">
          <div class="text-stone-100 text-sm">
            © {{ new Date().getFullYear() }} Questify
          </div>
        </div>
      </div>
    </div>

    <!-- Tablet/Desktop Menu -->
    <div class="hidden sm:flex items-center space-x-4">
      <LanguageSwitcher />
      <slot></slot>
    </div>
  </div>
</template>

<style scoped>
.transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

/* Ensure mobile menu is completely hidden on tablet and desktop */
@media (min-width: 640px) {
  #mobile-menu {
    display: none !important;
  }
}
</style> 