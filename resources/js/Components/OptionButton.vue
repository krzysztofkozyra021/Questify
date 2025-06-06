<script setup>
import { router } from '@inertiajs/vue3'
import { useTranslation } from '@/Composables/useTranslation'

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
  })
}

</script>

<template>
  <button
    class="w-3/4 h-20 mt-2 bg-zinc-600 hover:bg-zinc-400 p-2 rounded-3xl"
    @click="navigate"
  >
    {{ trans(optionText) }}
  </button>
</template>
