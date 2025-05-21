<script setup>
import { useTranslation } from '@/Composables/useTranslation'
import { usePage, Link } from '@inertiajs/vue3'

const { trans } = useTranslation()
const { socialLinks } = usePage().props
</script>

<template>
  <footer class="bg-stone-800 text-white py-4">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Logo i opis -->
      <div class="text-center mb-4">
        <p class="text-lg font-bold mb-1">Questify</p>
        <p class="text-xs text-stone-400">Questify to narzędzie do zarządzania zadaniami, które pomaga Ci osiągać cele.</p>
      </div>
      <!-- Linki -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        <div class="flex flex-col items-center gap-1">
          <Link :href="route('about')" class="text-stone-400 hover:text-white transition-colors text-sm">O nas</Link>
          <Link :href="route('faq')" class="text-stone-400 hover:text-white transition-colors text-sm">FAQ</Link>
        </div>
        <div class="flex flex-col items-center gap-1">
          <Link :href="route('terms')" class="text-stone-400 hover:text-white transition-colors text-sm">Regulamin</Link>
          <Link :href="route('policy')" class="text-stone-400 hover:text-white transition-colors text-sm">Polityka prywatności</Link>
        </div>
        <div class="flex flex-col items-center gap-1">
          <Link :href="route('contact')" class="text-stone-400 hover:text-white transition-colors text-sm">Kontakt</Link>
          <Link :href="route('support')" class="text-stone-400 hover:text-white transition-colors text-sm">Wsparcie</Link>
        </div>
        <div class="flex flex-col items-center gap-1">
          <Link :href="route('report.feature')" class="text-stone-400 hover:text-white transition-colors text-sm">Zgłoś funkcję</Link>
          <Link :href="route('report.bug')" class="text-stone-400 hover:text-white transition-colors text-sm">Zgłoś błąd</Link>
        </div>
      </div>

      <!-- Dolna sekcja -->
      <div class="border-t border-stone-700 pt-4">
        <div class="flex flex-col md:flex-row justify-between items-center gap-2">
          <!-- Logo -->
          <div>
            <img src="/images/logo.png" alt="Questify Logo" class="h-6">
          </div>

          <!-- Copyright -->
          <div class="text-xs text-stone-400">
            © {{ new Date().getFullYear() }} Questify. {{ trans('All rights reserved.') }}
          </div>

          <!-- Social Links -->
          <div class="flex space-x-4">
            <a
              v-if="socialLinks.github"
              :href="socialLinks.github"
              target="_blank"
              rel="noopener noreferrer"
              class="text-stone-400 hover:text-white transition-colors"
            >
              <svg
                class="w-5 h-5"
                fill="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                  clip-rule="evenodd"
                />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>



