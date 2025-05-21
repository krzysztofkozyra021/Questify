<template>
  <div class="flex flex-col min-h-screen">
    <Header :showPlayerPanel="false" />
    <main class="flex-1 max-w-4xl mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-center mb-8">Wsparcie</h1>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- FAQ -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Często Zadawane Pytania</h2>
          <p class="text-stone-600 mb-4">
            Znajdź odpowiedzi na najczęściej zadawane pytania dotyczące Questify.
          </p>
          <Link
            :href="route('faq')"
            class="inline-block bg-stone-600 text-white px-4 py-2 rounded-lg hover:bg-stone-700 transition-colors"
          >
            Przejdź do FAQ
          </Link>
        </div>

        <!-- Kontakt -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Kontakt</h2>
          <p class="text-stone-600 mb-4">
            Potrzebujesz pomocy? Skontaktuj się z naszym zespołem wsparcia.
          </p>
          <Link
            :href="route('contact')"
            class="inline-block bg-stone-600 text-white px-4 py-2 rounded-lg hover:bg-stone-700 transition-colors"
          >
            Skontaktuj się z nami
          </Link>
        </div>

        <!-- Zgłoś błąd -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Zgłoś błąd</h2>
          <p class="text-stone-600 mb-4">
            Znalazłeś błąd w aplikacji? Pomóż nam go naprawić.
          </p>
          <Link
            :href="route('report.bug')"
            class="inline-block bg-stone-600 text-white px-4 py-2 rounded-lg hover:bg-stone-700 transition-colors"
          >
            Zgłoś błąd
          </Link>
        </div>

        <!-- Zaproponuj funkcję -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Zaproponuj funkcję</h2>
          <p class="text-stone-600 mb-4">
            Masz pomysł na nową funkcję? Daj nam znać!
          </p>
          <Link
            :href="route('report.feature')"
            class="inline-block bg-stone-600 text-white px-4 py-2 rounded-lg hover:bg-stone-700 transition-colors"
          >
            Zaproponuj funkcję
          </Link>
        </div>
      </div>

      <!-- Dokumentacja -->
      <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Dokumentacja</h2>
        <div class="prose prose-stone max-w-none">
          <p class="text-stone-600 mb-4">
            Zapoznaj się z naszą dokumentacją, aby lepiej zrozumieć jak korzystać z Questify:
          </p>
          <ul class="space-y-2">
            <li>
              <a href="#" class="text-stone-600 hover:text-stone-800">Przewodnik dla początkujących</a>
            </li>
            <li>
              <a href="#" class="text-stone-600 hover:text-stone-800">System poziomów i doświadczenia</a>
            </li>
            <li>
              <a href="#" class="text-stone-600 hover:text-stone-800">Zarządzanie zadaniami</a>
            </li>
            <li>
              <a href="#" class="text-stone-600 hover:text-stone-800">Ustawienia konta</a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Status usługi -->
      <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Status usługi</h2>
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 bg-green-500 rounded-full"></div>
          <span class="text-stone-600">Wszystkie usługi działają poprawnie</span>
        </div>
        <p class="text-sm text-stone-500 mt-2">
          Ostatnia aktualizacja: {{ new Date().toLocaleString() }}
        </p>
      </div>
    </main>
    <Footer />
  </div>
</template>

<script setup>
import { useTranslation } from '@/Composables/useTranslation'
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import { Link } from '@inertiajs/vue3'

const { trans } = useTranslation()
</script>
