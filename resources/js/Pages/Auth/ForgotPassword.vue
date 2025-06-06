<script setup>
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import Preloader from '@/Components/Preloader.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import { useTranslation } from '@/Composables/useTranslation'
import Footer from '@/Components/Footer.vue'
import Header from '@/Components/Header.vue'
import { useHead } from '@vueuse/head'

const { trans } = useTranslation()

useHead({
  title: () => trans('Forgot Password') + ' | Questify',
})

defineProps({
  status: {
    type: String,
  },
})

const form = useForm({
  email: '',
})

const submit = () => {
  form.post(route('password.email'))
}

</script>

<template>
  <Preloader />
  <div class="min-h-screen bg-stone-900 flex flex-col">
    <Header :show-player-panel="false" />

    <div class="flex-1 container mx-auto px-4 sm:px-6 flex items-center">
      <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center w-full">
        <!-- Left Column - Motivation Text -->
        <div class="flex-1 text-center lg:text-left">
          <h2 class="text-4xl sm:text-5xl font-bold text-amber-100 mb-4 sm:mb-6 leading-tight">{{ trans('Lost Your Way?') }}</h2>
          <p class="text-xl sm:text-2xl text-amber-50 leading-relaxed">
            {{ trans('No worries, adventurer.') }}<br>
            {{ trans('We\'ll help you get back on track.') }}<br>
            {{ trans('Your quest continues soon.') }}
          </p>
        </div>

        <!-- Right Column - Form -->
        <div class="flex-1 w-full max-w-md mx-auto">
          <div class="bg-stone-800 rounded-2xl p-6 sm:p-8 shadow-2xl">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-amber-50 mb-6 sm:mb-8">{{ trans('Reset Password') }}</h2>

            <div class="mb-4 text-sm text-amber-50">
              {{ trans('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            <div
              v-if="status"
              class="mb-4 text-sm font-medium text-green-400"
            >
              {{ status }}
            </div>

            <form class="space-y-6" @submit.prevent="submit">
              <div>
                <InputLabel for="email" :value="trans('Email')" class="text-amber-50" />
                <TextInput
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="mt-1 block w-full bg-stone-700 text-amber-50 placeholder:text-stone-400 focus:border-amber-600 focus:ring-amber-600"
                  required
                  autofocus
                  autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
              </div>

              <PrimaryButton
                class="w-full justify-center py-3 bg-amber-600 hover:bg-amber-700 text-amber-50 font-bold rounded-lg transition-colors"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                {{ trans('Email Password Reset Link') }}
              </PrimaryButton>
            </form>
          </div>
        </div>
      </div>
    </div>
    <Footer class="mt-auto" />
  </div>
</template>
