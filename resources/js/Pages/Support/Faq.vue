<script setup>
import { ref } from 'vue'
import { useTranslation } from '@/Composables/useTranslation'
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import Preloader from '@/Components/Preloader.vue'
import { useHead } from '@vueuse/head'

const { trans } = useTranslation()

useHead({
  title: trans('FAQ') + ' | Questify',
})

const faqs = ref([
  {
    question: 'What is Questify?',
    answer: 'Questify is an innovative task management tool that combines elements of gamification with effective time management. It helps users achieve goals by transforming everyday tasks into an exciting adventure.',
  },
  {
    question: 'How does the level system work?',
    answer: 'The level system in Questify is based on gaining experience by completing tasks. Each task gives a specific amount of XP, and when the required experience threshold is reached, your level increases.',
  },
  {
    question: 'What are the types of tasks?',
    answer: 'In Questify, we have three main types of tasks: Habits (habits), Dailies (daily tasks) and To Do\'s (tasks to do). Each type has its own unique features and mechanics.',
  },
  {
    question: 'How does the health and energy system work?',
    answer: 'Health and energy are key statistics in Questify. Completing tasks costs energy, and some tasks can affect health. It is important to maintain these statistics at the appropriate level.',
  },
  {
    question: 'Can I collaborate with other users?',
    answer: 'At this moment, Questify is a single-player game. You can invite your friends to join you in the game, but you cannot collaborate with other users.',
  },
  {
    question: 'How can I report a bug or suggest a new feature?',
    answer: 'You can do this in two ways: by using the (Report a bug) or (Report a feature) form available in the footer of the page, or by contacting our support team.',
  },
])

const openFaq = ref(null)

const toggleFaq = (index) => {
  openFaq.value = openFaq.value === index ? null : index
}
</script>

<template>
  <div class="flex flex-col min-h-screen">
    <Preloader />
    <Header :show-player-panel="false" />
    <main class="flex-1 max-w-4xl mx-auto p-12">
      <h1 class="text-3xl font-bold text-center mb-8">{{ trans('Frequently Asked Questions') }}</h1>
      
      <div class="space-y-4"> 
        <div v-for="(faq, index) in faqs" :key="index" class="bg-white rounded-lg shadow-md overflow-hidden">
          <button
            class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-stone-50 transition-colors"
            @click="toggleFaq(index)"
          >
            <span class="font-medium text-lg">{{ trans(faq.question) }}</span>
            <svg
              class="size-5 transition-transform"
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
            <p class="text-stone-600">{{ trans(faq.answer) }}</p>
          </div>
        </div>
      </div>

      <div class="mt-12 text-center">
        <p class="text-stone-600 mb-4">{{ trans('If you have any questions') }}</p>
        <a
          href="/contact"
          class="inline-block bg-stone-600 text-white px-6 py-3 font-bold rounded-lg hover:bg-stone-700 transition-colors"
        >
          {{ trans('Contact us') }}
        </a>
      </div>
    </main>
    <Footer />
  </div>
</template>
