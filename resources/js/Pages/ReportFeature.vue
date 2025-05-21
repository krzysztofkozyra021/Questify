<template>
  <div class="flex flex-col min-h-screen">
    <Header :showPlayerPanel="false" />
    <main class="flex-1 max-w-4xl mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-center mb-8">Zgłoś funkcję</h1>
      
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
              placeholder="Krótki opis proponowanej funkcji"
            />
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-stone-700">Opis</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="6"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
              placeholder="Szczegółowy opis funkcji, jej celu i potencjalnych korzyści"
            ></textarea>
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
            <li>Opisz dokładnie proponowaną funkcję</li>
            <li>Wyjaśnij, jaką wartość wniesie dla użytkowników</li>
            <li>Podaj przykłady użycia</li>
            <li>Określ priorytet zgodnie z rzeczywistą potrzebą</li>
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
  priority: 'medium',
  category: 'general'
})

const categories = [
  { value: 'general', label: 'Ogólne' },
  { value: 'ui', label: 'Interfejs użytkownika' },
  { value: 'functionality', label: 'Funkcjonalność' },
  { value: 'performance', label: 'Wydajność' },
  { value: 'other', label: 'Inne' }
]

const priorities = [
  { value: 'low', label: 'Niski' },
  { value: 'medium', label: 'Średni' },
  { value: 'high', label: 'Wysoki' }
]

const submitForm = () => {
  // TODO: Implement form submission
  console.log('Form submitted:', form.value)
}
</script>
