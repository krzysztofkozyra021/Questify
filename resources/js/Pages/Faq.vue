<script setup>
import { ref } from 'vue'
import { useTranslation } from '@/Composables/useTranslation'
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'

const { trans } = useTranslation()

const faqs = ref([
  {
    question: 'Czym jest Questify?',
    answer: 'Questify to innowacyjne narzędzie do zarządzania zadaniami, które łączy w sobie elementy grywalizacji z efektywnym zarządzaniem czasem. Pomaga użytkownikom osiągać cele poprzez przekształcenie codziennych zadań w ekscytującą przygodę.'
  },
  {
    question: 'Jak działa system poziomów?',
    answer: 'System poziomów w Questify opiera się na zdobywaniu doświadczenia poprzez wykonywanie zadań. Każde zadanie daje określoną ilość XP, a po osiągnięciu wymaganego progu doświadczenia, Twój poziom wzrasta.'
  },
  {
    question: 'Jakie są typy zadań?',
    answer: 'W Questify mamy trzy główne typy zadań: Habits (nawyki), Dailies (zadania dzienne) i To Do\'s (zadania do wykonania). Każdy typ ma swoje unikalne cechy i mechaniki.'
  },
  {
    question: 'Jak działa system zdrowia i energii?',
    answer: 'Zdrowie i energia to kluczowe statystyki w Questify. Wykonywanie zadań kosztuje energię, a niektóre zadania mogą wpływać na zdrowie. Ważne jest utrzymanie tych statystyk na odpowiednim poziomie.'
  },
  {
    question: 'Czy mogę współpracować z innymi użytkownikami?',
    answer: 'Tak! Questify oferuje funkcje społecznościowe, które pozwalają na współpracę z innymi użytkownikami, dzielenie się zadaniami i wspólne osiąganie celów.'
  },
  {
    question: 'Jak mogę zgłosić błąd lub zaproponować nową funkcję?',
    answer: 'Możesz to zrobić na dwa sposoby: poprzez formularz "Zgłoś błąd" lub "Zgłoś funkcję" dostępny w stopce strony, lub kontaktując się z naszym zespołem wsparcia.'
  }
])

const openFaq = ref(null)

const toggleFaq = (index) => {
  openFaq.value = openFaq.value === index ? null : index
}
</script>

<template>
  <div class="flex flex-col min-h-screen">
    <Header :showPlayerPanel="false" />
    <main class="flex-1 max-w-4xl mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-center mb-8">Często Zadawane Pytania</h1>
      
      <div class="space-y-4"> 
        <div v-for="(faq, index) in faqs" :key="index" class="bg-white rounded-lg shadow-md overflow-hidden">
          <button
            @click="toggleFaq(index)"
            class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-stone-50 transition-colors"
          >
            <span class="font-medium text-lg">{{ faq.question }}</span>
            <svg
              class="w-5 h-5 transform transition-transform"
              :class="{ 'rotate-180': openFaq === index }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <div
            v-show="openFaq === index"
            class="px-6 py-4 bg-stone-50 border-t border-stone-200"
          >
            <p class="text-stone-600">{{ faq.answer }}</p>
          </div>
        </div>
      </div>

      <div class="mt-12 text-center">
        <p class="text-stone-600 mb-4">Nie znalazłeś odpowiedzi na swoje pytanie?</p>
        <a
          href="/contact"
          class="inline-block bg-stone-600 text-white px-6 py-3 rounded-lg hover:bg-stone-700 transition-colors"
        >
          Skontaktuj się z nami
        </a>
      </div>
    </main>
    <Footer />
  </div>
</template>
