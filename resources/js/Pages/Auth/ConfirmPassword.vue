<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import Preloader from '@/Components/Preloader.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import { useTranslation } from '@/Composables/useTranslation'
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import { useHead } from '@vueuse/head'

const { trans } = useTranslation()

useHead({
  title: trans('Confirm Password') + ' | Questify'
})

const form = useForm({
  password: '',
})

const submit = () => {
  form.post(route('password.confirm'), {
    onFinish: () => form.reset(),
  })
}
</script>

<template>
  <Preloader />
  <div class="min-h-screen bg-stone-900 flex flex-col">
    <Header :showPlayerPanel="false" />

    <div class="container mx-auto px-4 sm:px-6 py-8 sm:py-12">
      <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <!-- Left Column - Motivation Text -->
        <div class="flex-1 text-center lg:text-left">
          <h2 class="text-4xl sm:text-5xl font-bold text-amber-100 mb-4 sm:mb-6 leading-tight">{{ trans('Secure Your Journey') }}</h2>
          <p class="text-xl sm:text-2xl text-stone-100 leading-relaxed">
            {{ trans('Safety first, adventurer.') }}<br>
            {{ trans('Confirm your identity to continue.') }}<br>
            {{ trans('Your quest awaits.') }}
          </p>
        </div>

        <!-- Right Column - Form -->
        <div class="flex-1 w-full max-w-md mx-auto">
          <div class="bg-slate-600 rounded-2xl p-6 sm:p-8 shadow-2xl">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-stone-100 mb-6 sm:mb-8">{{ trans('Confirm Password') }}</h2>

            <div class="mb-4 text-sm text-stone-100">
              {{ trans('This is a secure area of the application. Please confirm your password before continuing.') }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <InputLabel for="password" :value="trans('Password')" class="text-stone-100" />
                <TextInput
                  id="password"
                  v-model="form.password"
                  type="password"
                  class="mt-1 block w-full bg-slate-700 text-stone-100 placeholder-slate-400 focus:border-amber-600 focus:ring-amber-600"
                  required
                  autocomplete="current-password"
                  autofocus
                />
                <InputError class="mt-2" :message="form.errors.password" />
              </div>

              <PrimaryButton
                class="w-full justify-center py-3 bg-amber-600 hover:bg-amber-700 text-stone-100 font-bold rounded-lg transition-colors"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                {{ trans('Confirm') }}
              </PrimaryButton>
            </form>
          </div>
        </div>
      </div>
    </div>
    <Footer class="mt-auto" />
  </div>
</template>
