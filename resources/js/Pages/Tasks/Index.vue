<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-blue-900">My Tasks</h1>
      <Link :href="route('tasks.create')" class="bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded transition">
        Add New Task
      </Link>
    </div>

    <!-- Success message after user action -->
    <div v-if="$page.props.flash?.success" class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6">
      {{ $page.props.flash.success }}
    </div>

    <!-- Tasks list -->
    <div v-if="!tasks || tasks.length === 0" class="bg-white p-6 rounded-lg shadow text-center border border-blue-100">
      <p class="text-blue-800">You don't have any tasks yet.</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="task in tasks" :key="task.id" class="bg-white rounded-lg shadow overflow-hidden border border-blue-100 hover:shadow-md transition">
        <div class="p-6">
          <div class="flex justify-between items-start mb-3">
            <h2 class="text-xl font-semibold text-blue-900">{{ task.title }}</h2>
            <span class="text-xs py-1 px-2 rounded-full" :class="getDifficultyClass(task.difficulty_level)">
              {{ task.difficulty ? task.difficulty.name : 'Level ' + task.difficulty_level }}
              {{ getDifficultyIcon(task.difficulty_level) }}
            </span>
          </div>

          <p class="text-gray-600 mb-4">{{ task.description }}</p>

          <!-- Task progress -->
          <div class="mb-4">
            <div class="flex justify-between mb-1">
              <span class="text-sm text-gray-500">Progress: {{ Math.round(task.progress) }}%</span>
              <span v-if="task.is_completed" class="text-sm text-green-600">Completed</span>
              <span v-else-if="task.is_deadline_task" class="text-sm" :class="isOverdue(task) ? 'text-red-600' : 'text-gray-600'">
                Due: {{ formatDate(task.due_date) }}
              </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="rounded-full h-2"
                   :class="getProgressColor(task)"
                   :style="{ width: task.progress + '%' }">
              </div>
            </div>
          </div>

          <!-- Tags -->
          <div v-if="task.tags && task.tags.length > 0" class="flex flex-wrap gap-1 mb-4">
            <span v-for="tag in task.tags" :key="tag.id" class="text-xs py-1 px-2 bg-blue-50 text-blue-700 rounded-full">
              {{ tag.name }}
            </span>
          </div>

          <!-- Additional information -->
          <div class="grid grid-cols-2 gap-2 text-sm text-gray-500 mt-4 border-t border-blue-50 pt-3">
            <div v-if="task.experience_reward">
              <span class="font-medium text-blue-700">XP Reward:</span> {{ task.experience_reward }}
            </div>
            <div v-if="task.resetConfig">
              <span class="font-medium text-blue-700">Repeats:</span> {{ task.resetConfig.name }}
            </div>
            <div v-if="task.repeat_every && task.repeat_unit">
              <span class="font-medium text-blue-700">Every:</span> {{ task.repeat_every }} {{ task.repeat_unit }}{{ task.repeat_every > 1 ? 's' : '' }}
            </div>
            <div v-if="task.completed_at">
              <span class="font-medium text-blue-700">Completed on:</span> {{ formatDate(task.completed_at) }}
            </div>
            <div v-if="task.start_date">
              <span class="font-medium text-blue-700">Started on:</span> {{ formatDate(task.start_date) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Import Link from Inertia.js
import { Link } from '@inertiajs/vue3';
import { route } from "ziggy-js"; // For Vue 3

// Get props passed from controller
defineProps({
  tasks: Array,
  difficulties: Array,
  resetConfigs: Array
});

// Date formatting
const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString();
};

// Check if task is overdue
const isOverdue = (task) => {
  if (!task.due_date) return false;
  return new Date(task.due_date) < new Date();
};

// Progress bar color
const getProgressColor = (task) => {
  if (task.is_completed) return 'bg-green-500';
  if (task.progress < 25) return 'bg-red-500';
  if (task.progress < 50) return 'bg-orange-500';
  if (task.progress < 75) return 'bg-yellow-500';
  return 'bg-blue-500';
};

// CSS class for difficulty level
const getDifficultyClass = (level) => {
  switch (level) {
    case 1: return 'bg-green-100 text-green-800';
    case 2: return 'bg-blue-100 text-blue-800';
    case 3: return 'bg-yellow-100 text-yellow-800';
    case 4: return 'bg-red-100 text-red-800';
    default: return 'bg-gray-100 text-gray-800';
  }
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
</script>
