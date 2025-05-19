<script setup>
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import Preloader from '@/Components/Preloader.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useTranslation } from '@/Composables/useTranslation'
import HamburgerMenu from '@/Components/HamburgerMenu.vue'
import Footer from '@/Components/Footer.vue'
const { trans } = useTranslation()

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
  <div class="min-h-screen bg-slate-800">
    <!-- Header -->
    <header class="bg-slate-800 shadow-lg">
      <div class="container mx-auto px-4 sm:px-6 py-4 flex justify-between items-center">
        <div class="flex items-center">
          <Link :href="route('register')">
            <img src="/images/logo.png" alt="Questify Logo" class="h-10 w-auto" />
          </Link>
        </div>
        <HamburgerMenu>
          <Link
            :href="route('login')"
            class="block px-4 py-2 text-stone-100 hover:bg-slate-600 transition-colors sm:px-6 sm:py-2 sm:bg-amber-900 sm:hover:bg-amber-800 sm:rounded-lg sm:font-bold"
          >
            {{ trans('Log in') }}
          </Link>
        </HamburgerMenu>
      </div>
    </header>

    <div class="container mx-auto px-4 sm:px-6 py-8 sm:py-12">
      <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <!-- Left Column - Motivation Text -->
        <div class="flex-1 text-center lg:text-left">
          <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-amber-100 mb-4 sm:mb-6 leading-tight">{{ trans('Begin Your Epic Journey') }}</h2>
          <p class="text-xl sm:text-2xl text-stone-100 leading-relaxed">
            {{ trans('Join the ranks of legendary adventurers.') }}<br>
            {{ trans('Every quest begins with a single step.') }}<br>
            {{ trans('Your story awaits, hero.') }}
          </p>
        </div>

        <!-- Right Column - Form -->
        <div class="flex-1 w-full max-w-md mx-auto">
          <div class="bg-slate-600 rounded-2xl p-6 sm:p-8 shadow-2xl">
            <h2 class="text-3xl font-bold text-center text-stone-100 mb-8">{{ trans('Sign up') }}</h2>
            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <InputLabel for="name" :value="trans('Name')" class="text-stone-100" />
                <TextInput
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full bg-slate-700 text-stone-100 placeholder-slate-400 focus:border-amber-600 focus:ring-amber-600"
                  required
                  autofocus
                  autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
              </div>

              <div>
                <InputLabel for="email" :value="trans('Email')" class="text-stone-100" />
                <TextInput
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="mt-1 block w-full bg-slate-700 text-stone-100 placeholder-slate-400 focus:border-amber-600 focus:ring-amber-600"
                  required
                  autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
              </div>

              <div>
                <InputLabel for="password" :value="trans('Password')" class="text-stone-100" />
                <TextInput
                  id="password"
                  v-model="form.password"
                  type="password"
                  class="mt-1 block w-full bg-slate-700 text-stone-100 placeholder-slate-400 focus:border-amber-600 focus:ring-amber-600"
                  required
                  autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
              </div>

              <div>
                <InputLabel for="password_confirmation" :value="trans('Confirm Password')" class="text-stone-100" />
                <TextInput
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  class="mt-1 block w-full bg-slate-700 text-stone-100 placeholder-slate-400 focus:border-amber-600 focus:ring-amber-600"
                  required
                  autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
              </div>

              <div class="text-center text-sm text-stone-100 mb-4">
                {{ trans('By clicking the button below, you are indicating that you have read and agree to the') }}
                <!-- <Link :href="route('terms')" class="text-amber-600 hover:text-amber-500">{{ trans('Terms of Service') }}</Link>
                {{ trans('and') }}
                <Link :href="route('privacy')" class="text-amber-600 hover:text-amber-500">{{ trans('Privacy Policy') }}</Link>. -->
              </div>

              <PrimaryButton
                class="w-full justify-center py-3 bg-amber-600 hover:bg-amber-700 text-stone-100 font-bold rounded-lg transition-colors"
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
  </div>
  <Footer/>
</template>
