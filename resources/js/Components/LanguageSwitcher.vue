<template>
  <div class="flex space-x-3">
    <a
      v-for="(code, name) in availableLocales"
      :key="code"
      href="#"
      @click.prevent="onLanguageChange(code)"
      class="px-2 py-1 rounded transition-colors duration-200"
      :class="currentLocale === code ? 'bg-indigo-100 text-indigo-800 font-medium' : 'text-gray-600 hover:text-indigo-600'"
    >
      {{ trans(name) }}
    </a>
  </div>
</template>

<script setup>
import { useTranslation } from '@/Composables/useTranslation';
const { trans } = useTranslation();
const { currentLocale, availableLocales, switchLanguage } = useTranslation();

function onLanguageChange(code) {
  switchLanguage(code);
}
</script>
