<script setup>
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3';
  import { useTranslation } from '@/Composables/useTranslation';

  const { trans } = useTranslation();

  const props = defineProps({
    show: Boolean,
    onClose: Function,
    difficulties: Array,
    todo: Object,
  });

  const emit = defineEmits(['edited', 'close']);
  
  const form = ref({
    title: props.todo?.title || '',
    description: props.todo?.description || '',
    difficulty_level: props.todo?.difficulty_level || 2,
    // If todo has a due_date, convert it to YYYY-MM-DD format, otherwise use current date
    due_date: props.todo?.due_date ? new Date(props.todo.due_date).toISOString().slice(0, 10) : new Date().toISOString().slice(0, 10),
    tags: props.todo?.tags?.map(tag => tag.name).join(',') || '',
  });
  
  const loading = ref(false);
  
  function close() {
    emit('close');
  }
  
  function submit() {
    loading.value = true;
    router.put(`/tasks/todos/update/${props.todo.id}`, {
      ...form.value,
      tags: form.value.tags ? form.value.tags.split(',').map(t => t.trim()).filter(Boolean) : [],
    }, {
      onSuccess: () => {
        loading.value = false;
        emit('edited');
      },
      onError: (errors) => {
        console.error('Request failed with errors:', errors);
        loading.value = false;
      }
    });
  }
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
        <button class="absolute top-2 right-2 text-gray-500" @click="close">✕</button>
        <h2 class="text-xl font-bold mb-4">{{ trans('Edit Todo') }}</h2>
        <form @submit.prevent="submit">
          <div class="mb-3">
            <label class="block font-semibold mb-1">{{ trans('Title') }}*</label>
            <input v-model="form.title" required class="w-full border rounded px-2 py-1" :placeholder="trans('Add a title')" />
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
            <input v-model="form.tags" class="w-full border rounded px-2 py-1" :placeholder="trans('Add tags, comma separated')" />
          </div>
          <div class="mb-3">
            <label class="block font-semibold mb-1">{{ trans('Due Date') }} </label>
            <input v-model="form.due_date" type="date" class="w-full border rounded px-2 py-1" />
          </div>
          <button type="submit" class="w-full bg-amber-800 text-white py-2 rounded font-bold mt-2" :disabled="loading">
            {{ loading ? trans('Editing...') : trans('Edit') }}
          </button>
        </form>
      </div>
    </div>
</template>
