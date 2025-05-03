<template>
<div class="bg-purple-100 flex flex-col w-screen h-screen justify-center items-center">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 w-96">
    <h1 class="text-2xl font-bold mb-4">Zaloguj się</h1>
    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
    <input type="email" id="email" v-model="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-4" placeholder="Wprowadź swój email" />
    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Hasło:</label>
    <input type="password" id="password" v-model="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-4" placeholder="Wprowadź swoje hasło" />
    <button @click="login" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Zaloguj się</button>
    <p v-if="error" class="text-red-500 text-xs italic mt-4">{{ error }}</p>
    <p class="text-gray-600 text-xs italic mt-4">Nie masz konta? <a href="/register" class="text-blue-500 hover:text-blue-800">Zarejestruj się</a></p>
    <p class="text-gray-600 text-xs italic mt-4">Zapomniałeś hasła? <a href="/password/reset" class="text-blue-500 hover:text-blue-800">Zresetuj hasło</a></p>
    <p class="text-gray-600 text-xs italic mt-4">Nie możesz się zalogować? <a href="/login" class="text-blue-500 hover:text-blue-800">Skontaktuj się z nami</a></p>
    </div>
</div>
</template>
<script>
import { router } from '@inertiajs/vue3'

export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
    }
  },
  methods: {
    login() {
      // Przykład logiki logowania
      if (this.email === 'admin@example.com' && this.password === 'password') {
        router.visit('/welcome') // Przejście do Welcome.vue po zalogowaniu
      } else {
        alert('Nieprawidłowe dane logowania')
      }
    },
  },
}
</script>