<template>
  <div class="flex flex-col min-h-screen">
    <Header :showPlayerPanel="false" />
    <main class="flex-1 max-w-4xl mx-auto p-12">
      <h1 class="text-4xl font-bold text-center mb-8">{{ trans('Report a feature') }}</h1>
      
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
              :placeholder="trans('Short description of the proposed feature')"
            />
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-stone-700">{{ trans('Description') }}</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="6"
              class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
              required
              :placeholder="trans('Detailed description of the feature, its purpose and potential benefits')"
            ></textarea>
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
            <li>{{ trans('Describe the proposed feature in detail') }}</li>
            <li>{{ trans('Explain its value to users') }}</li>
            <li>{{ trans('Provide examples of usage') }}</li>
            <li>{{ trans('Determine the priority based on the actual need') }}</li>
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
  { value: 'general', label: 'General' },
  { value: 'ui', label: 'Ui' },
  { value: 'functionality', label: 'Functionality' },
  { value: 'performance', label: 'Performance' },
  { value: 'other', label: 'Other' }
]

const priorities = [
  { value: 'low', label: 'Low' },
  { value: 'medium', label: 'Medium' },
  { value: 'high', label: 'High' }
]

const submitForm = () => {
  // TODO: Implement form submission
  console.log('Form submitted:', form.value)
}
</script>
