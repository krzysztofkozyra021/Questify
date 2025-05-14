<script setup>
import { Link } from '@inertiajs/vue3'
import HamburgerMenu from '@/Components/HamburgerMenu.vue'
import { useTranslation } from '@/Composables/useTranslation'

const { trans } = useTranslation()

defineProps({
  showSignUp: {
    type: Boolean,
    default: true
  }
})
</script>

<template>
  <header class="bg-slate-800 shadow-lg">
    <div class="container mx-auto px-4 sm:px-6 py-4 flex justify-between items-center">
      <div class="flex items-center">
        <Link :href="route('dashboard')">
          <img src="/images/logo.png" alt="Questify Logo" class="h-10 w-auto" />
        </Link>
      </div>
      <HamburgerMenu>
        <Link
          v-if="showSignUp"
          :href="route('register')"
          class="block px-4 py-2 text-stone-100 hover:bg-slate-600 transition-colors sm:px-6 sm:py-2 sm:bg-amber-900 sm:hover:bg-amber-800 sm:rounded-lg sm:font-bold"
        >
          {{ trans('Sign up') }}
        </Link>
      </HamburgerMenu>
    </div>
  </header>
</template> 