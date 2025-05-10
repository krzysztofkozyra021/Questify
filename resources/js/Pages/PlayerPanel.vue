<template>
  <div class="bg-slate-800/80 rounded-lg border border-slate-700 p-4">
    <!-- Player Info -->
    <div class="flex items-center space-x-3 mb-4">
      <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
        <span class="text-slate-300 text-lg">{{ user?.name?.[0]?.toUpperCase() }}</span>
      </div>
      <div class="flex-1 min-w-0">
        <h2 class="text-slate-200 font-medium truncate">{{ user?.name }}</h2>
        <p class="text-slate-400 text-sm truncate">Level {{ userStatistics?.level || 1 }}</p>
      </div>
    </div>

    <!-- Stats -->
    <div class="space-y-3">
      <!-- Health -->
      <div>
        <div class="flex justify-between text-sm mb-1">
          <span class="text-red-400">{{ trans('Health') }}</span>
          <span class="text-slate-300">{{ currentPlayerHealth }}/{{ userStatistics?.max_health || 100 }}</span>
        </div>
        <div class="h-2 bg-slate-700 rounded-full overflow-hidden">
          <div
            class="h-full bg-red-500 rounded-full transition-all duration-300"
            :style="{ width: `${currentPlayerHealth / maxPlayerHealth * 100}%` }"
          ></div>
        </div>
      </div>

      <!-- Energy -->
      <div>
        <div class="flex justify-between text-sm mb-1">
          <span class="text-blue-400">{{ trans('Energy') }} </span>
          <span class="text-slate-300">{{ currentPlayerEnergy }}/{{ maxPlayerEnergy }}</span>
        </div>
        <div class="h-2 bg-slate-700 rounded-full overflow-hidden">
          <div
            class="h-full bg-blue-500 rounded-full transition-all duration-300"
            :style="{ width: `${currentPlayerEnergy / maxPlayerEnergy * 100}%` }"
          ></div>
        </div>
      </div>

      <!-- Experience -->
      <div>
        <div class="flex justify-between text-sm mb-1">
          <span class="text-emerald-400">{{ trans('Experience') }}</span>
            <span class="text-slate-300">{{ currentPlayerExperience }}/{{ maxPlayerExperience }}</span>
        </div>
        <div class="h-2 bg-slate-700 rounded-full overflow-hidden">
          <div
            class="h-full bg-emerald-500 rounded-full transition-all duration-300"
            :style="{ width: `${currentPlayerExperience / maxPlayerExperience * 100}%` }"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useTranslation } from '@/composables/useTranslation.js';

const { trans } = useTranslation()
const props = defineProps({
  userStatistics: Object,
  user: Object,
});

const currentPlayerHealth = computed(() => {
  return props.userStatistics.current_health;
});
const maxPlayerHealth = computed(() => {
  return props.userStatistics.max_health;
});

const currentPlayerEnergy = computed(() => {
  return props.userStatistics.current_energy;
});
const maxPlayerEnergy = computed(() => {
  return props.userStatistics.max_energy;
});

const currentPlayerExperience = computed(() => {
  return props.userStatistics.current_experience;
});
const maxPlayerExperience = computed(() => {
  return props.userStatistics.next_level_experience;
});




</script>
