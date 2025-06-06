  <script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useTranslation } from '@/Composables/useTranslation'
import { useNotification } from '@/Composables/useNotification'
const { trans } = useTranslation()

const { addNotification } = useNotification()


const props = defineProps({
  show: Boolean,
  onClose: Function,
  difficulties: Array,
})
  
const emit = defineEmits(['created', 'close'])
  
const form = ref({
  title: '',
  description: '',
  difficulty_level: props.difficulties?.[0]?.difficulty_level || 2,
  due_date: new Date().toISOString().slice(0, 10),
  start_date: new Date().toISOString().slice(0, 10),
  tags: '',
  is_completed: false,
  type: 'todo',
})
  
const initialFormState = { ...form.value }
  
const loading = ref(false)

function resetForm() {
  form.value = { ...initialFormState }
}

function close() {
  emit('close')
}
  
function submit() {
  loading.value = true
  router.post('/tasks/todos/store', {
    ...form.value,
    tags: form.value.tags.split(',').map(t => t.trim()).filter(Boolean),
  }, {
    preserveScroll: true,
    preserveState: true,
    only: ['userStatistics', 'todoTasks'],
    onSuccess: () => {
      loading.value = false
      resetForm()
      emit('created')
      addNotification(trans('Todo task created successfully'), 'success')
    },
    onError: () => {
      loading.value = false
      addNotification(trans('Failed to create todo task'), 'error')
    },
  })
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
      <button class="absolute top-2 right-2 text-gray-500" @click="close">✕</button>
      <h2 class="text-xl font-bold mb-4">{{ trans('Create Todo') }}</h2>
      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="block font-semibold mb-1">{{ trans('Title') }}*</label>
          <input v-model="form.title" required class="w-full border rounded px-2 py-1" :placeholder="trans('Add a title')">
        </div>
        <div class="mb-3">
          <label class="block font-semibold mb-1">{{ trans('Notes') }}</label>
          <textarea v-model="form.description" class="w-full border rounded px-2 py-1" :placeholder="trans('Add notes')" />
        </div>
        <div class="mb-3">
          <label class="block font-semibold mb-1">{{ trans('Difficulty') }}</label>
          <select v-model="form.difficulty_level" class="w-full border rounded px-2 py-1">
            <option v-for="d in difficulties" :key="d.difficulty_level" :value="d.difficulty_level">{{ trans(d.name) }}</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="block font-semibold mb-1">{{ trans('Tags') }}</label>
          <input v-model="form.tags" class="w-full border rounded px-2 py-1" :placeholder="trans('Add tags, comma separated')">
        </div>
        <div class="mb-3">
          <label class="block font-semibold mb-1">{{ trans('Due Date') }} </label>
          <input v-model="form.due_date" type="date" class="w-full border rounded px-2 py-1">
        </div>
        <button type="submit" class="w-full bg-amber-800 text-white py-2 rounded font-bold mt-2" :disabled="loading">
          {{ loading ? trans('Creating...') : trans('Create') }}
        </button>
      </form>
    </div>
  </div>
</template>
  
  
