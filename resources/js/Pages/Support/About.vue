<script setup>
import { useTranslation } from '@/Composables/useTranslation'
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import Preloader from '@/Components/Preloader.vue'
import { useHead } from '@vueuse/head'

const { trans } = useTranslation()

useHead({
  title: () => trans('About') + ' | Questify',
})

</script>

<template>
  <div class="flex flex-col h-screen">
    <Preloader />
    <Header :show-player-panel="false" />
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden sm:rounded-lg p-12">
        <div class="p-6 text-gray-900">
          <h1 class="text-4xl font-bold mb-8 text-center">{{ trans('About Questify') }}</h1>
          
          <div class="space-y-8">
            <!-- Mission -->
            <section>
              <h2 class="text-2xl font-semibold mb-4">{{ trans('Our mission') }}</h2>
              <p class="text-gray-600">
                {{ trans('Questify is an innovative task management tool that combines elements of gamification with effective time management. Our goal is to help users achieve their goals by transforming everyday tasks into an exciting adventure.') }}
              </p>
            </section>

            <!-- History -->
            <section>
              <h2 class="text-2xl font-semibold mb-4">{{ trans('History') }}</h2>
              <p class="text-gray-600">
                {{ trans('Questify was born out of a passion for effective time management and a love for games. We noticed that traditional task management methods often feel boring and do not motivate people to take action. We decided to change this, creating a platform that makes achieving goals an enjoyable experience.') }}
              </p>
            </section>

            <!-- Values -->
            <section>
              <h2 class="text-2xl font-semibold mb-4">{{ trans('Our values') }}</h2>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 bg-gray-50 rounded-lg">
                  <div class="flex items-center gap-2 mb-2">
                    <svg class="size-6 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <h3 class="text-xl font-medium">{{ trans('Motivation') }}</h3>
                  </div>
                  <p class="text-gray-600">
                    {{ trans('We believe in the power of positive motivation and gamification in achieving goals.') }}
                  </p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                  <div class="flex items-center gap-2 mb-2">
                    <svg class="size-6 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xl font-medium">{{ trans('Development') }}</h3>
                  </div>
                  <p class="text-gray-600">
                    {{ trans('We support continuous personal development through systematic achievement of small goals.') }}
                  </p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                  <div class="flex items-center gap-2 mb-2">
                    <svg class="size-6 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="text-xl font-medium">{{ trans('Collaboration') }}</h3>
                  </div>
                  <p class="text-gray-600">
                    {{ trans('We promote collaboration and support of the community in achieving common goals.') }}
                  </p>  
                </div>
              </div>
            </section>

            <!-- Team -->
            <section>
              <h2 class="text-2xl font-semibold mb-4">{{ trans('Our team') }}</h2>
              <p class="text-gray-600 mb-4">
                {{ trans('We are a team of enthusiasts who believe in the power of gamification and effective time management. Each of us brings unique experience and perspective, creating together a platform that helps people achieve their goals.') }}
              </p>
            </section>

            <!-- Join us -->
            <section class="text-center">
              <h2 class="text-2xl font-semibold mb-4">{{ trans('Join us') }}</h2>
              <p class="text-gray-600 mb-6">
                {{ trans('Start your journey with Questify today and discover how enjoyable it can be to achieve goals!') }}
              </p>
              <a 
                href="/register" 
                class="inline-block bg-stone-600 text-white px-6 py-3 font-bold rounded-lg hover:bg-stone-700 transition-colors"
              >
                {{ trans('Start now') }}
              </a>
            </section>
          </div>
        </div>
      </div>
    </div>
    <Footer />
  </div>
</template>
