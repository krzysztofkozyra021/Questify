<script setup>
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
  title: () => trans('Reset Password') + ' | Questify',
})

const props = defineProps({
  email: {
    type: String,
    required: true,
  },
  token: {
    type: String,
    required: true,
  },
})

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('password.store'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<template>
  <Preloader />
  <div class="min-h-screen bg-stone-900 flex flex-col">
    <Header :show-player-panel="false" />

    <div class="flex-1 container mx-auto px-4 sm:px-6 py-8 sm:py-12">
      <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <!-- Left Column - Motivation Text -->
        <div class="flex-1 text-center lg:text-left">
          <h2 class="text-4xl sm:text-5xl font-bold text-amber-100 mb-4 sm:mb-6 leading-tight">{{ trans('New Beginnings') }}</h2>
          <p class="text-xl sm:text-2xl text-amber-50 leading-relaxed">
            {{ trans('Choose your new path wisely.') }}<br>
            {{ trans('A fresh start awaits.') }}<br>
            {{ trans('Your journey continues.') }}
          </p>
        </div>

        <!-- Right Column - Form -->
        <div class="flex-1 w-full max-w-md mx-auto">
          <div class="bg-stone-800 rounded-2xl p-6 sm:p-8 shadow-2xl">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-amber-50 mb-6 sm:mb-8">{{ trans('Reset Password') }}</h2>

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

              <div>
                <InputLabel for="password" :value="trans('Password')" class="text-amber-50" />
                <TextInput
                  id="password"
                  v-model="form.password"
                  type="password"
                  class="mt-1 block w-full bg-stone-700 text-amber-50 placeholder:text-stone-400 focus:border-amber-600 focus:ring-amber-600"
                  required
                  autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
              </div>

              <div>
                <InputLabel
                  for="password_confirmation"
                  :value="trans('Confirm Password')"
                  class="text-amber-50"
                />
                <TextInput
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  class="mt-1 block w-full bg-stone-700 text-amber-50 placeholder:text-stone-400 focus:border-amber-600 focus:ring-amber-600"
                  required
                  autocomplete="new-password"
                />
                <InputError
                  class="mt-2"
                  :message="form.errors.password_confirmation"
                />
              </div>

              <PrimaryButton
                class="w-full justify-center py-3 bg-amber-600 hover:bg-amber-700 text-amber-50 font-bold rounded-lg transition-colors"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                {{ trans('Reset Password') }}
              </PrimaryButton>
            </form>
          </div>
        </div>
      </div>
    </div>
    <Footer class="mt-auto" />
  </div>
</template>
