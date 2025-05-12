<template>
  <button
    class="w-full h-20 bg-zinc-600 hover:bg-zinc-400 p-2 rounded-3xl"
    @click="navigate"
  >
    {{ trans(optionText) }}
  </button>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation.js';

const { trans } = useTranslation()

const props = defineProps({
  optionText: {
    type: String,
    required: true,
  },
  route: {
    type: String,
    required: true,
  },
  method: {
    type: String, 
    required: false,
  },
})

const navigate = () => {
  router.visit(props.route, {
    method: props.method,
  });
}

</script>