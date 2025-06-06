<script setup>
import { computed } from 'vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Preloader from '@/Components/Preloader.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useTranslation } from '@/Composables/useTranslation'
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import { useHead } from '@vueuse/head'

const { trans } = useTranslation()

useHead({
  title: () => trans('Verify Email') + ' | Questify',
})

const props = defineProps({
  status: {
    type: String,
  },
})

const form = useForm({})

const submit = () => {
  form.post(route('verification.send'))
}

const verificationLinkSent = computed(
  () => props.status === 'verification-link-sent',
)
</script>

<template>
  <Preloader />
  <div class="min-h-screen bg-stone-900 flex flex-col">
    <Header :show-player-panel="false" />

    <div class="flex-1 container mx-auto px-6 py-12">
      <div class="max-w-6xl mx-auto flex gap-12 items-center">
        <!-- Left Column - Motivation Text -->
        <div class="flex-1">
          <h2 class="text-5xl font-bold text-amber-100 mb-6 leading-tight">{{ trans('One Last Step') }}</h2>
          <p class="text-2xl text-amber-50 leading-relaxed">
            {{ trans('Verify your identity.') }}<br>
            {{ trans('Complete your registration.') }}<br>
            {{ trans('Your adventure awaits.') }}
          </p>
        </div>

        <!-- Right Column - Form -->
        <div class="flex-1">
          <div class="bg-stone-800 rounded-2xl p-8 shadow-2xl">
            <h2 class="text-3xl font-bold text-center text-amber-50 mb-8">{{ trans('Email Verification') }}</h2>

            <div class="mb-4 text-sm text-amber-50">
              {{ trans('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </div>

            <div
              v-if="verificationLinkSent"
              class="mb-4 text-sm font-medium text-green-400"
            >
              {{ trans('A new verification link has been sent to the email address you provided during registration.') }}
            </div>

            <form class="space-y-6" @submit.prevent="submit">
              <div class="flex items-center justify-between">
                <PrimaryButton
                  class="w-full justify-center py-3 bg-amber-600 hover:bg-amber-700 text-amber-50 font-bold rounded-lg transition-colors"
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                >
                  {{ trans('Resend Verification Email') }}
                </PrimaryButton>

                <Link
                  :href="route('logout')"
                  method="post"
                  as="button"
                  class="ml-4 text-sm text-amber-50 hover:text-amber-400 transition-colors"
                >
                  {{ trans('Log Out') }}
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <Footer class="mt-auto" />
  </div>
</template>
