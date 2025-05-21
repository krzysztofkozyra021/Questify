<template>
  <div class="flex flex-col min-h-screen">
    <Header :showPlayerPanel="false" />
    <main class="flex-1 max-w-4xl mx-auto p-12">
      <h1 class="text-4xl font-bold text-center mb-8">{{ trans('Report a bug') }}</h1>
      
      <div class="bg-white rounded-lg shadow-md p-6">
        <form @submit.prevent="submitForm" class="space-y-6">
          <div>
            <label for="title" class="block text-sm font-medium text-stone-700">{{ trans('Title') }}</label>
            <input
              type="text"
              id="title"
              v-model="form.title"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
              :placeholder="trans('Short description of the bug')"
            />
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-stone-700">{{ trans('Description') }}</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
              :placeholder="trans('Detailed description of the bug')"
            ></textarea>
          </div>

          <div>
            <label for="steps" class="block text-sm font-medium text-stone-700">{{ trans('Steps to reproduce') }}</label>
            <textarea
              id="steps"
              v-model="form.steps"
              rows="4"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
              :placeholder="trans('1. Open page...\n2. Click button...\n3. Enter data...')"
            ></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="expected" class="block text-sm font-medium text-stone-700">{{ trans('Expected behavior') }}</label>
              <textarea
                id="expected"
                v-model="form.expected"
                rows="3"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                required
                :placeholder="trans('What should happen?')"
              ></textarea>
            </div>

            <div>
              <label for="actual" class="block text-sm font-medium text-stone-700">{{ trans('Actual behavior') }}</label>
              <textarea
                id="actual"
                v-model="form.actual"
                rows="3"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                required
                :placeholder="trans('What happened?')"
              ></textarea>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="browser" class="block text-sm font-medium text-stone-700">{{ trans('Browser') }}</label>
              <input
                type="text"
                id="browser"
                v-model="form.browser"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                :placeholder="trans('e.g. Chrome 91.0.4472.124')"
              />
            </div>

            <div>
              <label for="os" class="block text-sm font-medium text-stone-700">{{ trans('Operating system') }}</label>
              <input
                type="text"
                id="os"
                v-model="form.os"
                class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                :placeholder="trans('e.g. Windows 10 Pro')"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="category" class="block text-sm font-medium text-stone-700">{{ trans('Category') }}</label>
              <select
                id="category"
                v-model="form.category"
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
                v-model="form.priority"
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

      <div class="mt-8 bg-stone-50 rounded-lg p-6">
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
  { value: 'general', label: 'General' },
  { value: 'ui', label: 'User interface' },
  { value: 'functionality', label: 'Functionality' },
  { value: 'performance', label: 'Performance' },
  { value: 'security', label: 'Security' },
  { value: 'other', label: 'Other' }
]

const priorities = [
  { value: 'low', label: 'Low' },
  { value: 'medium', label: 'Medium' },
  { value: 'high', label: 'High' },
  { value: 'critical', label: 'Critical' }
]

const submitForm = () => {
  // TODO: Implement form submission
  console.log('Form submitted:', form.value)
}
</script>
