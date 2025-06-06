<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useTranslation } from '@/Composables/useTranslation'
import { useNotification } from '@/Composables/useNotification'
import { syncDashboardData } from '@/Composables/syncDashboardData'
const { trans } = useTranslation()
const { addNotification } = useNotification()

const props = defineProps({
  show: Boolean,
  onClose: Function,
  difficulties: Array,
  daily: Object,
  resetConfigs: Array,
})

const emit = defineEmits(['edited', 'close'])
  
const form = ref({
  title: props.daily?.title || '',
  description: props.daily?.description || '',
  difficulty_level: props.daily?.difficulty_level || 2,
  experience_reward: props.daily?.experience_reward || 3,
  reset_frequency: props.daily?.reset_frequency || 1,
  weekly_schedule: props.daily?.weekly_schedule || [],
  tags: props.daily?.tags?.map(tag => tag.name).join(',') || '',
})
  
const weeklySchedule = ref(props.daily?.weekly_schedule || [])
  
const loading = ref(false)
  
function close() {
  emit('close')
}
  
function submit() {
  loading.value = true
  router.put(`/tasks/dailies/update/${props.daily.id}`, {
    ...form.value,
    tags: form.value.tags ? form.value.tags.split(',').map(t => t.trim()).filter(Boolean) : [],
    experience_reward: form.value.experience_reward,
    weekly_schedule: weeklySchedule.value,
  }, {
    preserveScroll: true,
    preserveState: true,
    only: ['userStatistics', 'dailyTasks'],
    onSuccess: () => {
      loading.value = false
      emit('edited')
      addNotification(trans('Daily task updated successfully'), 'success')
      syncDashboardData()
    },
    onError: () => {
      loading.value = false
      addNotification(trans('Failed to update Daily task'), 'error')
    },
  })
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
      <button class="absolute top-2 right-2 text-gray-500" @click="close">✕</button>
      <h2 class="text-xl font-bold mb-4">{{ trans('Edit daily') }}</h2>
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
          <label class="block font-semibold mb-1">{{ trans('Reset Counter') }} </label>
          <select v-model="form.reset_frequency" class="w-full border rounded px-2 py-1">
            <option v-for="r in resetConfigs" :key="r.id" :value="r.id">{{ trans(r.name) }}</option>
          </select>
        </div>

        <div v-if="form.reset_frequency === 2" class="mb-3 w-full">
          <!-- Weekly -->
          <label class="block font-semibold mb-1">{{ trans('Weekly Schedule') }} </label>
          <div class="grid grid-cols-7 w-full max-w-md">
            <div v-for="(day, index) in ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']" :key="day" class="w-full">
              <label class="relative inline-flex items-center cursor-pointer w-full">
                <input v-model="weeklySchedule" type="checkbox" :value="day" class="sr-only peer">
                <div class="w-full h-6 flex items-center justify-center peer-checked:bg-amber-600 bg-stone-600 text-white"
                     :class="{
                       'rounded-l': index === 0,
                       'rounded-r': index === 6
                     }"
                >
                  <span class="text-xs">{{ trans(day.slice(0, 2).toUpperCase()) }}</span>
                </div>
              </label>
            </div>
          </div>
        </div>
        <button type="submit" class="w-full bg-amber-800 text-white py-2 rounded font-bold mt-2" :disabled="loading">
          {{ loading ? trans('Editing...') : trans('Edit') }}
        </button>
      </form>
    </div>
  </div>
</template>
