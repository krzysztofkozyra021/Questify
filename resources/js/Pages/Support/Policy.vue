<template>
  <div class="flex flex-col min-h-screen">
    <Preloader />
    <Header :showPlayerPanel="false" />
    <main class="flex-1 max-w-4xl mx-auto p-12">
      <h1 class="text-3xl font-bold text-center mb-8">{{ trans('Privacy Policy') }}</h1>
      
      <div class="bg-white rounded-lg shadow-md p-6 space-y-8">
        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('1. General information') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('This privacy policy outlines the rules for processing and protecting personal data in the Questify service.') }}</p>
            <p>{{ trans('The administrator of personal data is ') }} Questify</p>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('2. Scope of processed data') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('We process the following personal data:') }}</p>
            <ul class="list-disc list-inside">
              <li>{{ trans('Email address') }}</li>
              <li>{{ trans('Name and surname') }}</li>
              <li>{{ trans('Login data') }}</li>
              <li>{{ trans('Data about activity on the service') }}</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('3. Purpose of data processing') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('Personal data is processed for the following purposes:') }}</p>
            <ul class="list-disc list-inside">
              <li>{{ trans('Providing services through the service') }}</li>
              <li>{{ trans('Communication with users') }}</li>
              <li>{{ trans('Analysis and improvement of the service') }}</li>
              <li>{{ trans('Ensuring security') }}</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('4. Legal basis for processing') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('Processing of data is based on:') }}</p>
            <ul class="list-disc list-inside">
              <li>{{ trans('User consent') }}</li>
              <li>{{ trans('Performance of a contract') }}</li>
              <li>{{ trans('Legally justified interests of the administrator') }}</li>
              <li>{{ trans('Legal obligation') }}</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('5. Period of data storage') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('Personal data is stored for a period of:') }}</p>
            <ul class="list-disc list-inside">
              <li>{{ trans('Required to achieve the purposes') }}</li>
              <li>{{ trans('Required by law') }}</li>
              <li>{{ trans('Until the user withdraws consent') }}</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('6. User rights') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('The user has the right to:') }}</p>
            <ul class="list-disc list-inside">
              <li>{{ trans('Access to their data') }}</li>
              <li>{{ trans('Rectification of data') }}</li>
              <li>{{ trans('Deletion of data') }}</li>
              <li>{{ trans('Restriction of processing') }}</li>
              <li>{{ trans('Data transfer') }}</li>
              <li>{{ trans('Opposition to processing') }}</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('7. Data security') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('We ensure data security through:') }}</p>
            <ul class="list-disc list-inside">
              <li>{{ trans('Encryption of transmission') }}</li>
              <li>{{ trans('Regular backups') }}</li>
              <li>{{ trans('Access control') }}</li>
              <li>{{ trans('Monitoring the system') }}</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('8. Cookies') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('The service uses cookies for:') }}</p>
            <ul class="list-disc list-inside">
              <li>{{ trans('Maintaining user sessions') }}</li>
              <li>{{ trans('Remembering preferences') }}</li>
              <li>{{ trans('Traffic analysis') }}</li>
              <li>{{ trans('Personalization of content') }}</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">{{ trans('9. Contact') }}</h2>
          <div class="prose prose-stone max-w-none">
            <p>{{ trans('For matters related to data protection, you can contact:') }}</p>
            <ul>
              <li>{{ trans('Email:') }} <a href="mailto:questify@gmail.com">questify@gmail.com</a></li>
              <li>{{ trans('Phone:') }} <a href="tel:+48123456789">+48 123 456 789</a></li>
              <li>{{ trans('Address:') }} <a href="https://goo.gl/maps/1234567890">ul. Przykładowa 123, 00-000 Warszawa</a></li>
            </ul>
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

const { trans } = useTranslation()
</script>
