<template>
  <div class="flex flex-col min-h-screen">
    <Header :showPlayerPanel="false" />
    <main class="flex-1 max-w-4xl mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-center mb-8">Zgłoś błąd</h1>
      
      <div class="bg-white rounded-lg shadow-md p-6">
        <form @submit.prevent="submitForm" class="space-y-6">
          <div>
            <label for="title" class="block text-sm font-medium text-stone-700">Tytuł</label>
            <input
              type="text"
              id="title"
              v-model="form.title"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
              placeholder="Krótki opis błędu"
            />
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-stone-700">Opis</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
              placeholder="Szczegółowy opis błędu"
            ></textarea>
          </div>

          <div>
            <label for="steps" class="block text-sm font-medium text-stone-700">Kroki do odtworzenia</label>
            <textarea
              id="steps"
              v-model="form.steps"
              rows="4"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
              placeholder="1. Otwórz stronę...&#10;2. Kliknij przycisk...&#10;3. Wprowadź dane..."
            ></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="expected" class="block text-sm font-medium text-stone-700">Oczekiwane zachowanie</label>
              <textarea
                id="expected"
                v-model="form.expected"
                rows="3"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                required
                placeholder="Co powinno się stać?"
              ></textarea>
            </div>

            <div>
              <label for="actual" class="block text-sm font-medium text-stone-700">Rzeczywiste zachowanie</label>
              <textarea
                id="actual"
                v-model="form.actual"
                rows="3"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                required
                placeholder="Co się stało?"
              ></textarea>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="browser" class="block text-sm font-medium text-stone-700">Przeglądarka</label>
              <input
                type="text"
                id="browser"
                v-model="form.browser"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                placeholder="np. Chrome 91.0.4472.124"
              />
            </div>

            <div>
              <label for="os" class="block text-sm font-medium text-stone-700">System operacyjny</label>
              <input
                type="text"
                id="os"
                v-model="form.os"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                placeholder="np. Windows 10 Pro"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="category" class="block text-sm font-medium text-stone-700">Kategoria</label>
              <select
                id="category"
                v-model="form.category"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                required
              >
                <option v-for="category in categories" :key="category.value" :value="category.value">
                  {{ category.label }}
                </option>
              </select>
            </div>

            <div>
              <label for="priority" class="block text-sm font-medium text-stone-700">Priorytet</label>
              <select
                id="priority"
                v-model="form.priority"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                required
              >
                <option v-for="priority in priorities" :key="priority.value" :value="priority.value">
                  {{ priority.label }}
                </option>
              </select>
            </div>
          </div>

          <div class="flex justify-end">
            <button
              type="submit"
              class="bg-stone-600 text-white px-6 py-3 rounded-lg hover:bg-stone-700 transition-colors"
            >
              Wyślij zgłoszenie
            </button>
          </div>
        </form>
      </div>

      <div class="mt-8 bg-stone-50 rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Wskazówki</h2>
        <div class="prose prose-stone max-w-none">
          <p>Aby Twoje zgłoszenie zostało rozpatrzone jak najszybciej:</p>
          <ul>
            <li>Opisz dokładnie kroki prowadzące do błędu</li>
            <li>Podaj informacje o środowisku (przeglądarka, system operacyjny)</li>
            <li>Dołącz zrzuty ekranu lub nagrania, jeśli to możliwe</li>
            <li>Określ priorytet zgodnie z wpływem błędu na działanie aplikacji</li>
          </ul>
        </div>
      </div>
    </main>
    <Footer />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useTranslation } from '@/Composables/useTranslation'
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'

const { trans } = useTranslation()

const form = ref({
  title: '',
  description: '',
  steps: '',
  expected: '',
  actual: '',
  browser: '',
  os: '',
  priority: 'medium',
  category: 'general'
})

const categories = [
  { value: 'general', label: 'Ogólne' },
  { value: 'ui', label: 'Interfejs użytkownika' },
  { value: 'functionality', label: 'Funkcjonalność' },
  { value: 'performance', label: 'Wydajność' },
  { value: 'security', label: 'Bezpieczeństwo' },
  { value: 'other', label: 'Inne' }
]

const priorities = [
  { value: 'low', label: 'Niski' },
  { value: 'medium', label: 'Średni' },
  { value: 'high', label: 'Wysoki' },
  { value: 'critical', label: 'Krytyczny' }
]

const submitForm = () => {
  // TODO: Implement form submission
  console.log('Form submitted:', form.value)
}
</script>
