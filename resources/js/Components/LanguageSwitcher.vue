<template>
  <div class="flex space-x-3">
    <a
      v-for="(code, name) in availableLocales"
      :key="code"
      href="#"
      @click.prevent="switchLanguage(code)"
      class="px-2 py-1 rounded transition-colors duration-200"
      :class="currentLocale === code ? 'bg-indigo-100 text-indigo-800 font-medium' : 'text-gray-600 hover:text-indigo-600'"
    >
      {{ trans(name) }}
    </a>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');
const availableLocales = computed(() => page.props.availableLocales || {'English': 'en', 'Polish': 'pl'});
const translations = computed(() => page.props.translations || {});

function trans(key) {
  return translations.value[key] || key;
}
function switchLanguage(locale) {
  window.location.href = `/language/${locale}`;
}
</script>
