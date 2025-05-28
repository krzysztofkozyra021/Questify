<template>
  <div class="flex flex-col min-h-screen">
    <Preloader />
    <Header :showPlayerPanel="false" />
    <main class="flex-1 max-w-4xl mx-auto p-12">
      <h1 class="text-4xl font-bold text-center mb-8">{{ trans('Contact') }}</h1>
      <div v-if="messageSuccessfulySent" class="text-stone-500 text-center p-20">
        <h1 class="text-3xl font-bold">{{ trans('Thank you for your message!') }}</h1>
        <p class="text-stone-400 text-xl">{{ trans('We will reply to your message as soon as possible.') }}</p>
      </div>
      <ErrorModal v-if="errorMessage" :message="errorMessage" />
      <div class="bg-white rounded-lg shadow-md p-6" v-if="!messageSuccessfulySent">
        <form @submit.prevent="submitForm" class="space-y-6">
          <div>
            <label for="name" class="block text-m font-bold text-stone-700">
              {{ trans('Name and surname') }} <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              id="name"
              v-model="supportForm.name"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
            />
          </div>

          <div>
            <label for="email" class="block text-m font-bold text-stone-700">
              {{ trans('Email') }} <span class="text-red-500">*</span>
            </label>
            <input
              type="email"
              id="email"
              v-model="supportForm.email"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
            />
          </div>

          <div>
            <label for="subject" class="block text-m font-bold text-stone-700">
              {{ trans('Subject') }} <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              id="subject"
              v-model="supportForm.subject"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
            />
          </div>

          <div>
            <label for="message" class="block text-m font-bold text-stone-700">
              {{ trans('Message') }} <span class="text-red-500">*</span>
            </label>
            <textarea
              id="message"
              v-model="supportForm.message"
              rows="4"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
            ></textarea>
          </div>

          <div class="flex justify-end">
            <button
              type="submit"
              class="inline-block bg-stone-600 text-white px-6 py-3 font-bold rounded-lg hover:bg-stone-700 transition-colors"
            >
              {{ trans('Send message') }}
            </button>
          </div>
        </form>
        <div v-if="messageSuccessfulySent" class="text-green-500 text-center">
          {{ trans('Thank you for your message. We will get back to you as soon as possible.') }}
        </div>
      </div>

      <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">{{ trans('Contact information') }}</h2>
          <div class="space-y-4">
            <p class="flex items-center text-stone-600">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              support@questify.com
            </p>
            <p class="flex items-center text-stone-600">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              +48 123 456 789
            </p>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">{{ trans('Working hours') }}</h2>
          <div class="space-y-2">
            <p class="text-stone-600">{{ trans('Monday - Friday: 9:00 - 17:00') }}</p>
            <p class="text-stone-600">{{ trans('Saturday: 10:00 - 14:00') }}</p>
            <p class="text-stone-600">{{ trans('Sunday: Closed') }}</p>
          </div>
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
import { router } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'
import ErrorModal from '@/Components/ErrorModal.vue'
import Preloader from '@/Components/Preloader.vue'

const { trans } = useTranslation()

useHead({
  title: trans('Contact') + ' | Questify'
})


const supportForm = ref({
  name: '',
  email: '',
  subject: '',
  message: ''
})

const errorMessage = ref(null)
const messageSuccessfulySent = ref(false)

const submitForm = () => {
  router.post(route('support.contact'), supportForm.value, {
    onSuccess: () => {
      messageSuccessfulySent.value = true
    },
    onError: () => {
      errorMessage.value = trans('Failed to send message. Please try again later.')
    }
  })
}
</script>
