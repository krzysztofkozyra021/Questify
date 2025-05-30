<script setup>
import { onMounted, ref } from 'vue'

const model = defineModel({
  type: String,
  required: true,
})

const input = ref(null)

onMounted(() => {
  if (input.value.hasAttribute('autofocus')) {
    input.value.focus()
  }
})

defineExpose({ focus: () => input.value.focus() })
</script>

<template>
  <input
    ref="input"
    v-model="model"
    class="mt-1 block w-full bg-stone-900 text-amber-50 placeholder-stone-400 focus:border-amber-600 focus:ring-amber-600 rounded-lg"
  >
</template>
