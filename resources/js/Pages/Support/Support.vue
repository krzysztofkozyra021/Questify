<template>
  <div class="flex flex-col min-h-screen">
    <Preloader />
    <Header :show-player-panel="false" />
    <main class="flex-1 max-w-4xl mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-center mb-8">{{ trans('Support') }}</h1>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- FAQ -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">{{ trans('Frequently Asked Questions') }}</h2>
          <p class="text-stone-600 mb-4">
            {{ trans('Find answers to the most frequently asked questions about Questify.') }}
          </p>
          <Link
            :href="route('faq')"
            class="inline-block bg-stone-600 text-white font-bold  px-4 py-2 rounded-lg hover:bg-stone-700 transition-colors"
          >
            {{ trans('Go to FAQ') }}
          </Link>
        </div>

        <!-- Contact -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">{{ trans('Contact') }}</h2>
          <p class="text-stone-600 mb-4">
            {{ trans('Need help? Contact our support team.') }}
          </p>
          <Link
            :href="route('contact')"
            class="inline-block bg-stone-600 text-white font-bold  px-4 py-2 rounded-lg hover:bg-stone-700 transition-colors"
          >
            {{ trans('Contact us') }}
          </Link>
        </div>

        <!-- Report a bug -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">{{ trans('Report a bug') }}</h2>
          <p class="text-stone-600 mb-4">
            {{ trans('Found a bug in the application? Help us fix it.') }}
          </p>
          <Link
            :href="route('report.bug')"
            class="inline-block bg-stone-600 text-white font-bold  px-4 py-2 rounded-lg hover:bg-stone-700 transition-colors"
          >
            {{ trans('Report a bug') }}
          </Link>
        </div>

        <!-- Suggest a feature -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">{{ trans('Suggest a feature') }}</h2>
          <p class="text-stone-600 mb-4">
            {{ trans('Have an idea for a new feature? Let us know!') }}
          </p>
          <Link
            :href="route('report.feature')"
            class="inline-block bg-stone-600 text-white font-bold  px-4 py-2 rounded-lg hover:bg-stone-700 transition-colors"
          >
            {{ trans('Suggest a feature') }}
          </Link>
        </div>
      </div>

      <!-- Documentation -->
      <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">{{ trans('Documentation') }}</h2>
        <div class="prose prose-stone max-w-none">
          <p class="text-stone-600 mb-4">
            {{ trans('Read our documentation to better understand how to use Questify:') }}
          </p>
          <ul class="space-y-2">
            <li>
              <a href="#" class="text-stone-600 hover:text-stone-800">{{ trans('Beginner\'s guide') }}</a>
            </li>
            <li>
              <a href="#" class="text-stone-600 hover:text-stone-800">{{ trans('Level system and experience') }}</a>
            </li>
            <li>
              <a href="#" class="text-stone-600 hover:text-stone-800">{{ trans('Task management') }}</a>
            </li>
            <li>
              <a href="#" class="text-stone-600 hover:text-stone-800">{{ trans('Account settings') }}</a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Service status -->
      <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">{{ trans('Service status') }}</h2>
        <div class="flex items-center gap-2">
          <div class="size-3 bg-green-500 rounded-full" />
          <span class="text-stone-600">{{ trans('All services are working properly') }}</span>
        </div>
        <p class="text-sm text-stone-500 mt-2">
          {{ trans('Last update:') }} {{ new Date().toLocaleString() }}
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
import Preloader from '@/Components/Preloader.vue'
import { Link } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'

const { trans } = useTranslation()

useHead({
<<<<<<< HEAD
  title: () => trans('Support') + ' | Questify',
=======
  title: trans('Support') + ' | Questify',
>>>>>>> c7ca75c539f7e4ab3c65cee96c4289a234ae7390
})
</script>
