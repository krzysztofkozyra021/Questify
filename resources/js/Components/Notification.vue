<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useTranslation } from '@/Composables/useTranslation';

const { trans } = useTranslation();

const props = defineProps({
  message: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'success',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  duration: {
    type: Number,
    default: 3000
  }
});

const emit = defineEmits(['close']);
const isVisible = ref(true);
let timeout;

const colors = {
  success: {
    bg: 'bg-green-100',
    text: 'text-green-800',
    border: 'border-green-200',
    icon: 'text-green-600'
  },
  error: {
    bg: 'bg-red-100',
    text: 'text-red-800',
    border: 'border-red-200',
    icon: 'text-red-600'
  },
  warning: {
    bg: 'bg-yellow-100',
    text: 'text-yellow-800',
    border: 'border-yellow-200',
    icon: 'text-yellow-600'
  },
  info: {
    bg: 'bg-blue-100',
    text: 'text-blue-800',
    border: 'border-blue-200',
    icon: 'text-blue-600'
  }
};

const icons = {
  success: `<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
  </svg>`,
  error: `<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
  </svg>`,
  warning: `<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
  </svg>`,
  info: `<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
  </svg>`
};

onMounted(() => {
  timeout = setTimeout(() => {
    isVisible.value = false;
    emit('close');
  }, props.duration);
});

onUnmounted(() => {
  if (timeout) clearTimeout(timeout);
});
</script>

<template>
  <Transition
    enter-active-class="transform ease-out duration-300 transition-all"
    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition-all duration-300 ease-in-out"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-2"
  >
    <div
      v-if="isVisible"
      class="fixed top-4 right-4 z-50 max-w-sm w-full"
    >
      <div 
        class="rounded-lg shadow-lg overflow-hidden border transform transition-all duration-300"
        :class="[colors[type].bg, colors[type].border]"
      >
        <div class="p-2">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <div 
                class="rounded-full p-0.5"
                :class="colors[type].bg"
              >
                <div 
                  class="h-5 w-5 flex items-center justify-center"
                  :class="colors[type].icon"
                  v-html="icons[type]"
                ></div>
              </div>
            </div>
            <div class="ml-2 w-0 flex-1">
              <p 
                class="text-sm font-medium"
                :class="colors[type].text"
              >
                {{ trans(message) }}
              </p>
            </div>
            <div class="ml-2 flex-shrink-0 flex">
              <button
                @click="isVisible = false; emit('close')"
                class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stone-500 rounded-md"
                :class="colors[type].text"
              >
                <span class="sr-only">{{ trans('Close') }}</span>
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>