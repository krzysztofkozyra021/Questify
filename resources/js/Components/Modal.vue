<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  maxWidth: {
    type: String,
    default: '2xl',
  },
  closeable: {
    type: Boolean,
    default: true,
  },
})

const emit = defineEmits(['close'])
const showSlot = ref(props.show)

watch(
  () => props.show,
  () => {
    if (props.show) {
      document.body.style.overflow = 'hidden'
      showSlot.value = true
    } else {
      document.body.style.overflow = ''
      setTimeout(() => {
        showSlot.value = false
      }, 200)
    }
  },
)

const close = () => {
  if (props.closeable) {
    emit('close')
  }
}

const closeOnEscape = (e) => {
  if (e.key === 'Escape') {
    e.preventDefault()
    if (props.show) {
      close()
    }
  }
}

onMounted(() => document.addEventListener('keydown', closeOnEscape))

onUnmounted(() => {
  document.removeEventListener('keydown', closeOnEscape)
  document.body.style.overflow = ''
})

const maxWidthClass = computed(() => {
  return {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
  }[props.maxWidth]
})
</script>

<template>
  <Teleport to="body">
    <div
      v-show="show"
      class="fixed inset-0 z-[9999] overflow-y-auto bg-stone-900/50"
    >
      <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <Transition
          enter-active-class="ease-out duration-150"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="ease-in duration-100"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div
            v-show="show"
            class="relative overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:p-6"
            :class="maxWidthClass"
          >
            <div
              v-if="closeable"
              class="absolute right-0 top-0 pr-4 pt-4"
            >
              <button
                type="button"
                class="rounded-md bg-white text-stone-400 hover:text-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-500 focus:ring-offset-2 transition-colors"
                @click="close"
              >
                <span class="sr-only">Close</span>
                <svg
                  class="size-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>

            <slot v-if="showSlot" />
          </div>
        </Transition>
      </div>
    </div>
  </Teleport>
</template>
