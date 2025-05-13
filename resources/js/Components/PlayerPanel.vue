<script setup>
import { computed, ref, onMounted, watch } from 'vue';
import { useTranslation } from '@/Composables/useTranslation';
import { usePage } from '@inertiajs/vue3';

const { trans } = useTranslation();
const page = usePage();

const props = defineProps({
  userStatistics: Object,
  user: Object,
});

// Create local reactive copy of userStatistics
const localStats = ref({...props.userStatistics});

// Update local stats when props change
watch(() => props.userStatistics, (newStats) => {
  if (newStats) {
    localStats.value = {...newStats};
  }
}, { deep: true });


watch(() => {
  const userStats = page.props?.value?.userStatistics

  if (userStats && JSON.stringify(userStats) !== JSON.stringify(localStats.value)) {
    localStats.value = {...userStats};
  }
}, { deep: true });

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
  console.log('Component mounted, initial stats:', localStats.value);
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
  <div class="bg-slate-800/80 rounded-lg border border-slate-700 p-4">
    <!-- Player Info -->
    <div class="flex items-center space-x-3 mb-4">
      <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
        <img :src="profileImageUrl" alt="Profile Image" class="w-full h-full object-cover rounded-full">
      </div>
      <div class="flex-1 min-w-0">
        <h2 class="text-slate-200 font-medium truncate">{{ user?.name }}</h2>
        <p :class="['text-slate-400 text-sm truncate', { 'text-yellow-300 animate-pulse': isLevelingUp }]">
          Level {{ userLevel }}
        </p>
      </div>
    </div>
    
    <!-- Stats -->
    <div class="space-y-3">
      <!-- Health -->
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
      
      <!-- Energy -->
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
      
      <!-- Experience -->
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
</template>