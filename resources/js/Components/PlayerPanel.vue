<script setup>
import { computed, ref, onMounted, watch, watchEffect } from 'vue';
import { useTranslation } from '@/Composables/useTranslation';
import { usePage } from '@inertiajs/vue3';

const { trans } = useTranslation();
const page = usePage();

// Create local reactive copy of userStatistics
const localStats = ref({...page.props.userStatistics});
const userClassName = page.props.userClassName;

// Update local stats when props change
watch(() => page.props.userStatistics, (newStats) => {
  if (newStats) {
    localStats.value = {...newStats};
  }
}, { deep: true });

watchEffect(() => {
  const userStats = page.props.userStatistics;

  if (userStats && JSON.stringify(userStats) !== JSON.stringify(localStats.value)) {
    localStats.value = {...userStats};
  }
});

const userLevel = computed(() => {
  return localStats.value?.level || 1;
});

const profileImageUrl = ref('/images/default-profile.png');

const fetchProfileImage = async () => {
  try {
    const response = await fetch(`/profile/image`);
    const data = await response.json();
    if (data.profile_image_url) {
      profileImageUrl.value = data.profile_image_url;
    }
  } catch (error) {
    console.error('Error fetching profile image:', error);
  }
};

// Fetch profile image on mount
onMounted(() => {
  fetchProfileImage();
});

const currentPlayerHealth = computed(() => {
  return Math.round(localStats.value?.current_health || 0);
});

const maxPlayerHealth = computed(() => {
  return Math.round(localStats.value?.max_health || 100);
});

const currentPlayerEnergy = computed(() => {
  return Math.round(localStats.value?.current_energy || 0);
});

const maxPlayerEnergy = computed(() => {
  return Math.round(localStats.value?.max_energy || 100);
});

const currentPlayerExperience = computed(() => {
  return Math.round(localStats.value?.current_experience || 0);
});

const maxPlayerExperience = computed(() => {
  return Math.round(localStats.value?.next_level_experience || 100);
});

const experiencePercentage = computed(() => {
  if (!maxPlayerExperience.value) return '0%';  
  return `${(currentPlayerExperience.value / maxPlayerExperience.value * 100)}%`;
});

</script>

<template>
  <div class="flex flex-col sm:flex-row w-[98%] mx-auto bg-slate-600 h-auto sm:h-80 rounded-lg shadow-lg my-4">
    <!-- Player Avatar Section -->
    <div class="flex flex-col items-center justify-center w-full sm:w-1/4 p-4 border-b sm:border-b-0 sm:border-r border-slate-500">
      <div class="relative">
        <div class="w-36 h-36 rounded-3xl bg-slate-700 flex items-center justify-center overflow-hidden border-4 border-slate-500">
          <img 
            :src="profileImageUrl" 
            alt="Player Avatar" 
            class="w-full h-full object-cover"
            @error="profileImageUrl = '/images/default-profile.png'"
          >
        </div>
        <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-slate-800 px-3 py-1 rounded-full border border-slate-500">
          <span class="text-emerald-400 font-bold">Level {{ userLevel }}</span>
        </div>
      </div>
      <h2 class="mt-4 text-xl font-bold text-white">{{ page.props.user?.name }}</h2>
      <p class="text-slate-300">{{ trans(userClassName) }}</p>
    </div>

    <!-- Stats Section -->
    <div class="flex-1 p-6">
      <div class="space-y-4">
        <!-- Health Bar -->
        <div>
          <div class="flex justify-between text-sm mb-1">
            <span class="text-red-400 font-medium">{{ trans('Health') }}</span>
            <span class="text-slate-300">{{ currentPlayerHealth }}/{{ maxPlayerHealth }}</span>
          </div>
          <div class="h-3 bg-slate-700 rounded-full overflow-hidden">
            <div
              class="h-full bg-red-500 rounded-full transition-all duration-300"
              :style="{ width: `${(currentPlayerHealth / maxPlayerHealth * 100) || 0}%` }"
            ></div>
          </div>
        </div>

        <!-- Energy Bar -->
        <div>
          <div class="flex justify-between text-sm mb-1">
            <span class="text-blue-400 font-medium">{{ trans('Energy') }}</span>
            <span class="text-slate-300">{{ currentPlayerEnergy }}/{{ maxPlayerEnergy }}</span>
          </div>
          <div class="h-3 bg-slate-700 rounded-full overflow-hidden">
            <div
              class="h-full bg-blue-500 rounded-full transition-all duration-300"
              :style="{ width: `${(currentPlayerEnergy / maxPlayerEnergy * 100) || 0}%` }"
            ></div>
          </div>
        </div>

        <!-- Experience Bar -->
        <div>
          <div class="flex justify-between text-sm mb-1">
            <span class="text-emerald-400 font-medium">{{ trans('Experience') }}</span>
            <span class="text-slate-300">{{ currentPlayerExperience }}/{{ maxPlayerExperience }}</span>
          </div>
          <div class="h-3 bg-slate-700 rounded-full overflow-hidden">
            <div
              class="h-full bg-emerald-500 rounded-full transition-all duration-300"
              :style="{ width: experiencePercentage }"
            ></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--
  <div class="bg-slate-800/80 rounded-lg border border-slate-700 p-4">
    <div class="flex items-center space-x-3 mb-4">
      <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">  
        <img :src="profileImageUrl" alt="Profile Image" class="w-full h-full object-cover rounded-full">
      </div>
      <div class="flex-1 min-w-0">
        <h2 class="text-slate-200 font-medium truncate">{{ page.props.user?.name }}</h2>
        <p :class="['text-slate-400 text-sm truncate']">
          {{ trans('Level') }} {{ userLevel }} - {{ trans(userClassName) }}
        </p>
      </div>
    </div>
    
    <div class="space-y-3">
      <div>
        <div class="flex justify-between text-sm mb-1">
          <span class="text-red-400">{{ trans('Health') }}</span>
          <span class="text-slate-300">{{ currentPlayerHealth }}/{{ maxPlayerHealth }}</span>
        </div>
        <div class="h-2 bg-slate-700 rounded-full overflow-hidden">
          <div
            class="h-full bg-red-500 rounded-full transition-all duration-300"
            :style="{ width: `${(currentPlayerHealth / maxPlayerHealth * 100) || 0}%` }"
          ></div>
        </div>
      </div>
      

      <div>
        <div class="flex justify-between text-sm mb-1">
          <span class="text-blue-400">{{ trans('Energy') }} </span>
          <span class="text-slate-300">{{ currentPlayerEnergy }}/{{ maxPlayerEnergy }}</span>
        </div>
        <div class="h-2 bg-slate-700 rounded-full overflow-hidden">
          <div
            class="h-full bg-blue-500 rounded-full transition-all duration-300"
            :style="{ width: `${(currentPlayerEnergy / maxPlayerEnergy * 100) || 0}%` }"
          ></div>
        </div>
      </div>
      

      <div>
        <div class="flex justify-between text-sm mb-1">
          <span class="text-emerald-400">{{ trans('Experience') }}</span>
          <span class="text-slate-300">{{ currentPlayerExperience }}/{{ maxPlayerExperience }}</span>
        </div>
        <div class="h-2 bg-slate-700 rounded-full overflow-hidden">
          <div
            class="h-full bg-emerald-500 rounded-full transition-all duration-300"
            :style="{ width: experiencePercentage }"
          ></div>
        </div>
      </div>
    </div>
  </div>
-->
</template>