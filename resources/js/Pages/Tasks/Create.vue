

<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-blue-900 rounded-lg w-full max-w-md mx-auto shadow-xl overflow-hidden">
      <!-- Header -->
      <div class="flex justify-between items-center px-6 py-4 border-b border-blue-700">
        <h1 class="text-xl font-bold text-white">Create Daily</h1>
        <div class="flex space-x-2">
          <button
            @click="$inertia.visit(route('dashboard'))"
            class="px-4 py-1 text-blue-900 bg-blue-100 rounded hover:bg-blue-200 transition"
          >
            Cancel
          </button>
          <button
            type="button"
            @click="submitForm"
            class="px-4 py-1 text-white bg-blue-600 rounded hover:bg-blue-500 transition"
            :disabled="form.processing"
          >
            Create
          </button>
        </div>
      </div>

      <!-- Form content -->
      <div class="bg-blue-50 p-6">
        <form @submit.prevent="submitForm" class="space-y-5">
          <!-- Title -->
          <div>
            <label for="title" class="block text-sm font-medium text-blue-900 mb-1">Title*</label>
            <input
              id="title"
              v-model="form.title"
              placeholder="Add a title"
              type="text"
              class="w-full px-3 py-2 bg-white border border-blue-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
          </div>

          <!-- Description -->
          <div>
            <div class="flex justify-between items-center mb-1">
              <label for="description" class="block text-sm font-medium text-blue-900">Notes</label>
              <span class="text-xs text-blue-600">Markdown formatting help</span>
            </div>
            <textarea
              id="description"
              v-model="form.description"
              placeholder="Add notes"
              rows="3"
              class="w-full px-3 py-2 bg-white border border-blue-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            ></textarea>
          </div>

          <!-- Difficulty level -->
          <div>
            <label for="difficulty_level" class="block text-sm font-medium text-blue-900 mb-1">
              Difficulty (XP: {{ form.experience_reward }})
            </label>
            <div class="relative">
              <select
                id="difficulty_level"
                v-model="form.difficulty_level"
                class="w-full px-3 py-2 bg-white border border-blue-200 rounded appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
              >
                <option v-for="difficulty in difficulties" :key="difficulty.difficulty_level" :value="difficulty.difficulty_level">
                  {{ difficulty.name }} {{ getDifficultyIcon(difficulty.difficulty_level) }}
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                <svg class="w-5 h-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Start date -->
          <div>
            <label for="start_date" class="block text-sm font-medium text-blue-900 mb-1">Start Date</label>
            <div class="relative">
              <input
                id="start_date"
                v-model="form.start_date"
                type="date"
                class="w-full px-3 py-2 bg-white border border-blue-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
          </div>

          <!-- Repeats -->
          <div>
            <label for="reset_frequency" class="block text-sm font-medium text-blue-900 mb-1">Repeats</label>
            <div class="relative">
              <select
                id="reset_frequency"
                v-model="form.reset_frequency"
                @change="updateRepeatDefaults"
                class="w-full px-3 py-2 bg-white border border-blue-200 rounded appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
              >
                <option v-for="config in resetConfigs" :key="config.id" :value="config.id">
                  {{ config.name }}
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                <svg class="w-5 h-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Repeat Every -->
          <div>
            <label class="block text-sm font-medium text-blue-900 mb-1">Repeat Every</label>
            <div class="flex items-center">
              <input
                type="number"
                v-model="form.repeat_every"
                min="1"
                class="w-20 px-3 py-2 bg-white border border-blue-200 rounded-l focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
              <select
                v-model="form.repeat_unit"
                class="bg-white border border-blue-200 px-3 py-2 rounded-r focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="day">Day</option>
                <option value="week">Week</option>
                <option value="month">Month</option>
                <option value="year">Year</option>
              </select>
            </div>
          </div>

          <!-- Tags -->
          <div>
            <label class="block text-sm font-medium text-blue-900 mb-1">Tags</label>
            <div class="flex items-center mb-2">
              <input
                type="text"
                v-model="newTag"
                @keyup.enter="addTag"
                placeholder="Type and press Enter to add"
                class="flex-1 px-3 py-2 bg-white border border-blue-200 rounded-l focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
              <button
                type="button"
                @click="addTag"
                class="px-3 py-2 bg-blue-600 text-white rounded-r hover:bg-blue-500 transition"
              >
                Add
              </button>
            </div>

            <!-- Tag display area -->
            <div class="flex flex-wrap gap-2 mt-2" v-if="form.tags.length > 0">
              <div
                v-for="(tag, index) in form.tags"
                :key="index"
                class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full flex items-center text-sm"
              >
                <span>{{ tag }}</span>
                <button
                  @click="removeTag(index)"
                  type="button"
                  class="ml-1 text-blue-600 hover:text-blue-800"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Submit button -->
          <div class="text-center pt-4">
            <button
              type="submit"
              class="w-full px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-500 transition"
              :disabled="form.processing"
            >
              Create
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { route } from "ziggy-js";
import { watch } from 'vue';

// Props from controller
const props = defineProps({
  difficulties: Array,
  resetConfigs: Array
});

// Default date (today)
const today = new Date().toISOString().split('T')[0];

// Tag handling
const newTag = ref('');

const addTag = () => {
  if (newTag.value.trim() !== '' && !form.tags.includes(newTag.value.trim())) {
    form.tags.push(newTag.value.trim());
    newTag.value = '';
  }
};

const removeTag = (index) => {
  form.tags.splice(index, 1);
};

// Difficulty icons function
const getDifficultyIcon = (level) => {
  switch (level) {
    case 1: return '★'; // Very easy
    case 2: return '★★'; // Easy
    case 3: return '★★★'; // Medium
    case 4: return '★★★★'; // Hard
    default: return '';
  }
};

// Experience reward function - updated with better progression
const getExperienceReward = (level) => {
  switch (level) {
    case 1: return 10;  // Very easy
    case 2: return 25;  // Easy
    case 3: return 50;  // Medium
    case 4: return 100; // Hard
    default: return 10;
  }
};

// Form state using Inertia useForm
const form = useForm({
  title: '',
  description: '',
  difficulty_level: 2, // Default is easy
  reset_frequency: 1, // Default is daily
  start_date: today,
  due_date: '', // optional
  repeat_every: 1,
  repeat_unit: 'day',
  is_completed: false,
  is_deadline_task: true,
  experience_reward: getExperienceReward(2), // Initialize with default difficulty
  tags: [] // Array to store tags
});

// Watch for difficulty changes and update experience reward
watch(() => form.difficulty_level, (newDifficulty) => {
  form.experience_reward = getExperienceReward(newDifficulty);
});

// Function to update repeat values based on selected reset_frequency
const updateRepeatDefaults = () => {
  const selectedConfig = props.resetConfigs.find(config => config.id === form.reset_frequency);

  if (selectedConfig) {
    form.repeat_every = selectedConfig.period || 1;
    form.repeat_unit = selectedConfig.period_unit || 'day';
  }
};

// Initialize repeat values on component mount
if (props.resetConfigs && props.resetConfigs.length > 0) {
  updateRepeatDefaults();
}

// Form submission method
const submitForm = () => {
  form.post(route('tasks.store'), {
    onSuccess: () => {
      // The controller will handle saving tags to the database
      // No additional processing needed here
    }
  });
};
</script>

<style scoped>
/* No additional styles needed */
</style>
