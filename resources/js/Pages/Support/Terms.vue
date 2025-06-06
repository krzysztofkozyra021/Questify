<template>
  <div class="flex flex-col min-h-screen">
    <Preloader />
    <Header :show-player-panel="false" />
    <main class="flex-1 max-w-4xl mx-auto p-12 ">
      <h1 class="text-3xl font-bold text-center mb-8">{{ trans('Terms of Service') }}</h1>
      
      <div class="bg-white rounded-lg shadow-md p-6 space-y-8">
        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('1. General Terms') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('This Terms of Service outlines the rules for using the Questify service and the rights and obligations of users.') }}</p>
            <p>{{ trans('Questify is a platform for managing tasks and tracking progress in games.') }}</p>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('2. Definitions') }}</h2>
          <div class="prose prose-stone max-w-none">
            <ul>
              <li>{{ trans('Service - Questify platform available at questify.com') }}</li>
              <li>{{ trans('User - person using the Service') }}</li>
              <li>{{ trans('Account - individual user account in the Service') }}</li>
              <li>{{ trans('Task - single task or quest added by the user') }}</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('3. Registration and account') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('Registration conditions:') }}</p>
            <ul class="list-disc list-inside">
              <li>{{ trans('Providing true data') }}</li>
              <li>{{ trans('Acceptance of the Terms of Service') }}</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('4. Rules of using the Service') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('Prohibited:') }}</p>
            <ul class="list-disc list-inside">
              <li>{{ trans('Publishing content not compliant with the law') }}</li>
              <li>{{ trans('Violation of copyright') }}</li>
              <li>{{ trans('Spam and unwanted messages') }}</li>
              <li>{{ trans('Harmful actions against other users') }}</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('5. Protection of personal data') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('The administrator of personal data is') }} Questify.</p>
            <p>{{ trans('Detailed information about data processing is available in the') }} <a href="{{ route('policy') }}" class="text-stone-500 hover:text-stone-800">{{ trans('Privacy Policy') }}</a>.</p>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('6. Responsibility') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('The Service is not responsible for:') }}</p>
            <ul class="list-disc list-inside">
              <li>{{ trans('Content added by users') }}</li>
              <li>{{ trans('Actions of other users') }}</li>
              <li>{{ trans('Loss of data due to failure') }}</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('7. Changes to the Terms of Service') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('The Service reserves the right to change the Terms of Service at any time.') }}</p>
            <p>{{ trans('Users will be notified of changes in advance.') }}</p>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('8. Final provisions') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('The Terms of Service come into effect on the date of publication. Last update:') }} <span class=" font-bold text-stone-500">21.05.2025</span></p>
            <p>{{ trans('For matters not covered by these Terms, the laws of Poland apply.') }}</p>
          </div>
        </section>
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
import { useHead } from '@vueuse/head'

const { trans } = useTranslation()

useHead({
  title: trans('Terms of Service') + ' | Questify',
})
</script>
