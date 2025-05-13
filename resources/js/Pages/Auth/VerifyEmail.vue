<script setup>
import { computed } from 'vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Preloader from '@/Components/Preloader.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useTranslation } from '@/Composables/useTranslation'
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'

const { trans } = useTranslation()

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
  <div class="min-h-screen bg-slate-800">
    <!-- Header -->
    <header class="bg-slate-800 shadow-lg">
      <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center">
          <img src="/images/logo.png" alt="Questify Logo" class="h-10 w-auto" />
        </div>
        <div class="flex items-center space-x-4">
          <LanguageSwitcher />
        </div>
      </div>
    </header>

    <div class="container mx-auto px-6 py-12">
      <div class="max-w-6xl mx-auto flex gap-12 items-center">
        <!-- Left Column - Motivation Text -->
        <div class="flex-1">
          <h2 class="text-5xl font-bold text-amber-100 mb-6 leading-tight">{{ trans('One Last Step') }}</h2>
          <p class="text-2xl text-stone-100 leading-relaxed">
            {{ trans('Verify your identity.') }}<br>
            {{ trans('Complete your registration.') }}<br>
            {{ trans('Your adventure awaits.') }}
          </p>
        </div>

        <!-- Right Column - Form -->
        <div class="flex-1">
          <div class="bg-slate-600 rounded-2xl p-8 shadow-2xl">
            <h2 class="text-3xl font-bold text-center text-stone-100 mb-8">{{ trans('Email Verification') }}</h2>

            <div class="mb-4 text-sm text-stone-100">
              {{ trans('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </div>

            <div
              v-if="verificationLinkSent"
              class="mb-4 text-sm font-medium text-green-400"
            >
              {{ trans('A new verification link has been sent to the email address you provided during registration.') }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
              <div class="flex items-center justify-between">
                <PrimaryButton
                  class="w-full justify-center py-3 bg-amber-600 hover:bg-amber-700 text-stone-100 font-bold rounded-lg transition-colors"
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                >
                  {{ trans('Resend Verification Email') }}
                </PrimaryButton>

                <Link
                  :href="route('logout')"
                  method="post"
                  as="button"
                  class="ml-4 text-sm text-stone-100 hover:text-amber-600 transition-colors"
                >
                  {{ trans('Log Out') }}
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-800 mt-12">
      <div class="container mx-auto px-6 py-4">
        <div class="flex flex-col items-center justify-center space-y-4">
          <div class="flex space-x-6">
            <a href="https://github.com/krzysztofkozyra021/Questify" target="_blank" class="text-stone-100 hover:text-amber-600 transition-colors">
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
              </svg>
            </a>
          </div>
          <div class="text-stone-100 text-sm">
            © {{ new Date().getFullYear() }} Questify. {{ trans('All rights reserved.') }}
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
