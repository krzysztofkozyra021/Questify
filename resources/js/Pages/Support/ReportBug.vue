<template>
  <div class="flex flex-col min-h-screen">
    <Preloader />
    <Header :show-player-panel="false" />
    <main class="flex-1 max-w-4xl mx-auto p-12">
      <h1 class="text-4xl font-bold text-center mb-8">{{ trans('Report a bug') }}</h1>
      <ErrorModal v-if="errorMessage" :message="errorMessage" />
      <div v-if="messageSuccessfullySent" class="text-stone-500 text-center p-20">
        <h1 class="text-3xl font-bold">{{ trans('Thank you for reporting the bug!') }}</h1>
        <p class="text-stone-400 text-xl">{{ trans('We will review your report and get back to you soon.') }}</p>
      </div>
      <div v-if="!messageSuccessfullySent" class="bg-white rounded-lg shadow-md p-6">
        <form class="space-y-6" @submit.prevent="submitForm">
          <div>
            <label for="title" class="block text-sm font-medium text-stone-700">{{ trans('Title') }} <span class="text-red-500">*</span></label>
            <input
              id="title"
              v-model="reportBugForm.title"
              type="text"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
              :placeholder="trans('Short description of the bug')"
            >
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-stone-700">{{ trans('Description') }} <span class="text-red-500">*</span></label>
            <textarea
              id="description"
              v-model="reportBugForm.description"
              rows="4"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
              :placeholder="trans('Detailed description of the bug')"
            />
          </div>

          <div>
            <label for="steps" class="block text-sm font-medium text-stone-700">{{ trans('Steps to reproduce') }} <span class="text-red-500">*</span></label>
            <textarea
              id="steps"
              v-model="reportBugForm.steps"
              rows="4"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
              :placeholder="trans('1. Open page...\n2. Click button...\n3. Enter data...')"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="expected" class="block text-sm font-medium text-stone-700">{{ trans('Expected behavior') }} <span class="text-red-500">*</span></label>
              <textarea
                id="expected"
                v-model="reportBugForm.expected"
                rows="3"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                required
                :placeholder="trans('What should happen?')"
              />
            </div>

            <div>
              <label for="actual" class="block text-sm font-medium text-stone-700">{{ trans('Actual behavior') }} <span class="text-red-500">*</span></label>
              <textarea
                id="actual"
                v-model="reportBugForm.actual"
                rows="3"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                required
                :placeholder="trans('What happened?')"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="browser" class="block text-sm font-medium text-stone-700">{{ trans('Browser') }}</label>
              <input
                id="browser"
                v-model="reportBugForm.browser"
                type="text"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                :placeholder="trans('e.g. Chrome 91.0.4472.124')"
              >
            </div>

            <div>
              <label for="os" class="block text-sm font-medium text-stone-700">{{ trans('Operating system') }}</label>
              <input
                id="os"
                v-model="reportBugForm.os"
                type="text"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                :placeholder="trans('e.g. Windows 10 Pro')"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="category" class="block text-sm font-medium text-stone-700">{{ trans('Category') }}</label>
              <select
                id="category"
                v-model="reportBugForm.category"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                required
              >
                <option v-for="category in categories" :key="category.value" :value="category.value">
                  {{ trans(category.label) }}
                </option>
              </select>
            </div>

            <div>
              <label for="priority" class="block text-sm font-medium text-stone-700">{{ trans('Priority') }}</label>
              <select
                id="priority"
                v-model="reportBugForm.priority"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                required
              >
                <option v-for="priority in priorities" :key="priority.value" :value="priority.value">
                  {{ trans(priority.label) }}
                </option>
              </select>
            </div>
          </div>

          <div class="flex justify-end">
            <button
              type="submit"
              class="bg-stone-600 text-white font-bold px-6 py-3 rounded-lg hover:bg-stone-700 transition-colors"
            >
              {{ trans('Send report') }}
            </button>
          </div>
        </form>
      </div>

      <div v-if="!messageSuccessfullySent" class="mt-8 bg-stone-50 rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">{{ trans('Tips') }}</h2>
        <div class="prose prose-stone max-w-none">
          <p>{{ trans('To ensure your report is processed as quickly as possible:') }}</p>
          <ul class="list-disc list-inside">
            <li>{{ trans('Describe the steps to reproduce the bug in detail') }}</li>
            <li>{{ trans('Provide information about your environment (browser, operating system)') }}</li>
            <li>{{ trans('Include screenshots or recordings if possible') }}</li>
            <li>{{ trans('Set the priority according to the impact on the application') }}</li>
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
import Preloader from '@/Components/Preloader.vue'
import { router } from '@inertiajs/vue3'
import ErrorModal from '@/Components/ErrorModal.vue'
import { useHead } from '@vueuse/head'

const { trans } = useTranslation()

useHead({
  title: () => trans('Report a bug') + ' | Questify',
})

const messageSuccessfullySent = ref(false)

const getBrowserInfo = () => {
  const ua = navigator.userAgent
  return ua
}

const getOSInfo = () => {
  if (navigator.userAgentData?.platform) {
    return navigator.userAgentData.platform
  }
  const ua = navigator.userAgent
  if (ua.includes('Windows')) return 'Windows'
  if (ua.includes('Mac')) return 'MacOS'
  if (ua.includes('Linux')) return 'Linux'
  return 'Unknown'
}

const reportBugForm = ref({
  title: '',
  description: '',
  steps: '',
  expected: '',
  actual: '',
  browser: getBrowserInfo(),
  os: getOSInfo(),
  priority: 'medium',
  category: 'general',
})

const errorMessage = ref(null)

const categories = [
  { value: 'general', label: 'General' },
  { value: 'ui', label: 'User interface' },
  { value: 'functionality', label: 'Functionality' },
  { value: 'performance', label: 'Performance' },
  { value: 'security', label: 'Security' },
  { value: 'other', label: 'Other' },
]

const priorities = [
  { value: 'low', label: 'Low' },
  { value: 'medium', label: 'Medium' },
  { value: 'high', label: 'High' },
  { value: 'critical', label: 'Critical' },
]

const submitForm = () => {
  router.post(route('report.bug.send'), reportBugForm.value, {
    onSuccess: () => {
      messageSuccessfullySent.value = true
    },
    onError: () => {
      errorMessage.value = trans('Failed to send bug report. Please try again later.')
    },
  })
}
</script>
