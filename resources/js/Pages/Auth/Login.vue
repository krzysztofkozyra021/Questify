<script setup>
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useTranslation } from '@/Composables/useTranslation'
import Preloader from '@/Components/Preloader.vue'
import { useHead } from '@vueuse/head'

const { trans } = useTranslation()
const backgroundImage = '/images/background-landscape-register.jpg'

useHead({
  title: trans('Login') + ' | Questify'
})

defineProps({
  canResetPassword: {
    type: Boolean,
  },
  status: {
    type: String,
  },
})

const form = useForm({
  login: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <Preloader />

  <div class="min-h-screen w-full lg:flex">
    <!-- Left: Login Section (always solid background) -->
    <div class="flex flex-col justify-center items-center w-full lg:w-[35%] min-h-screen bg-stone-900 p-5">
      <div class="w-full max-w-md mx-auto p-6 sm:p-8 bg-stone-800 rounded-2xl shadow-2xl flex flex-col items-center">
        <!-- Logo -->
        <img src="/images/logo.png" alt="Questify Logo" class="h-14 w-auto mb-6 mt-2 mx-auto" />

        <!-- Divider -->
        <div class="flex items-center w-full mb-5 mt-2">
          <div class="flex-grow h-px bg-stone-600"></div>
          <h3 class="mx-3 text-amber-50 font-bold">{{ trans('Adventure awaits') }}</h3>
          <div class="flex-grow h-px bg-stone-600"></div>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="submit" class="w-full space-y-4">
          <div>
            <InputLabel for="login" :value="trans('Email or Username (case-sensitive)')" class="text-amber-50 text-sm font-semibold" />
            <TextInput
              id="login"
              v-model="form.login"
              type="text"
              class="mt-1 block w-full bg-stone-700 text-amber-50 placeholder-stone-400 focus:border-amber-600 focus:ring-amber-600"
              required
              autofocus
              autocomplete="username"
              :placeholder="trans('Email or Username')"
            />
            <InputError class="mt-2" :message="form.errors.login" />
          </div>
          <div>
            <div class="flex items-center justify-between">
              <InputLabel for="password" :value="trans('Password')" class="text-amber-50 text-sm font-semibold" />
              <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="text-xs text-amber-400 hover:text-amber-300 font-semibold"
              >
                {{ trans('Forgot Password?') }}
              </Link>
            </div>
            <TextInput
              id="password"
              v-model="form.password"
              type="password"
              class="mt-1 block w-full bg-stone-700 text-amber-50 placeholder-stone-400 focus:border-amber-600 focus:ring-amber-600"
              required
              autocomplete="current-password"
              :placeholder="trans('Password')"
            />
            <InputError class="mt-2" :message="form.errors.password" />
          </div>
          <PrimaryButton
            class="w-full justify-center py-3 bg-amber-600 hover:bg-amber-700 text-amber-50 font-bold rounded-lg transition-colors"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            {{ trans('Log in') }}
          </PrimaryButton>
        </form>

        <!-- Sign up link -->
        <div class="w-full text-center mt-6 text-amber-50 text-sm">
          {{ trans("Don't have a Questify account?") }}
          <Link :href="route('register')" class="text-amber-400 hover:text-amber-300 font-semibold ml-1">
            {{ trans('Sign up') }}
          </Link>
        </div>
      </div>
    </div>
    <!-- Right: Background Image -->
    <div class="hidden lg:block lg:w-[65%] min-h-screen" :style="{ backgroundImage: `url(${backgroundImage})`, backgroundSize: 'cover', backgroundPosition: 'center' }"></div>
  </div>
</template>
