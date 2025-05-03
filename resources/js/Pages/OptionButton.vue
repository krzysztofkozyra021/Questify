<template>
  <button
    class="w-full h-20 bg-zinc-600 hover:bg-zinc-400 p-2 rounded-3xl"
    @click="navigate"
  >
    {{ optionText }}
  </button>
</template>

<script>
import { router } from '@inertiajs/vue3';

export default {
  name: 'OptionButton',
 props: {
  optionText: {
    type: String,
    required: true,
  },
  route: {
    type: String,
    required: true,
  },
},
  methods: {
    navigate() {
      router.visit(this.route); // Przekierowanie na podany adres URL
    },
  },
};
</script>