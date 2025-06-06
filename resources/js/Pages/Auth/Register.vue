<script setup>
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import Preloader from '@/Components/Preloader.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useTranslation } from '@/Composables/useTranslation'
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import { useHead } from '@vueuse/head'

const { trans } = useTranslation()

useHead({
<<<<<<< HEAD
  title: () => trans('Register') + ' | Questify',
=======
  title: trans('Register') + ' | Questify',
>>>>>>> c7ca75c539f7e4ab3c65cee96c4289a234ae7390
})

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('register'), {
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
          <h2 class="text-4xl sm:text-5xl lg:text-5xl font-bold text-amber-100 mb-4 sm:mb-6 leading-tight">{{ trans('Begin Your Epic Journey') }}</h2>
          <p class="text-xl sm:text-2xl text-amber-50 leading-relaxed">
            {{ trans('Join the ranks of legendary adventurers.') }}<br>
            {{ trans('Every quest begins with a single step.') }}<br>
            {{ trans('Your story awaits, hero.') }}
          </p>
        </div>

        <!-- Right Column - Form -->
        <div class="flex-1 w-full max-w-md mx-auto">
          <div class="bg-stone-800 rounded-2xl p-6 sm:p-8 shadow-2xl">
            <h2 class="text-3xl font-bold text-center text-amber-50 mb-8">{{ trans('Sign up') }}</h2>
            <form class="space-y-6" @submit.prevent="submit">
              <div>
                <InputLabel for="name" :value="trans('Username')" class="text-amber-50" />
                <TextInput
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full bg-stone-700 text-amber-50 placeholder:text-stone-400 focus:border-amber-600 focus:ring-amber-600"
                  required
                  autofocus
                  autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
              </div>

              <div>
                <InputLabel for="email" :value="trans('Email')" class="text-amber-50" />
                <TextInput
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="mt-1 block w-full bg-stone-700 text-amber-50 placeholder:text-stone-400 focus:border-amber-600 focus:ring-amber-600"
                  required
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
                <InputLabel for="password_confirmation" :value="trans('Confirm Password')" class="text-amber-50" />
                <TextInput
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  class="mt-1 block w-full bg-stone-700 text-amber-50 placeholder:text-stone-400 focus:border-amber-600 focus:ring-amber-600"
                  required
                  autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
              </div>

              <div class="text-center text-sm text-amber-50 mb-4">
                {{ trans('By clicking the button below, you are indicating that you have read and agree to the') }}
                <Link :href="route('terms')" class="text-amber-600 hover:text-amber-500">{{ trans('Terms of Service') }}</Link>
                {{ trans('and') }}
                <Link :href="route('policy')" class="text-amber-600 hover:text-amber-500">{{ trans('Privacy Policy') }}</Link>.
              </div>

              <PrimaryButton
                class="w-full justify-center py-3 bg-amber-600 hover:bg-amber-700 text-amber-50 font-bold rounded-lg transition-colors"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                {{ trans('Sign up') }}
              </PrimaryButton>
            </form>
          </div>
        </div>
      </div>
    </div>
    <Footer class="mt-auto" />
  </div>
</template>
