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
  <div class="flex flex-col sm:flex-row w-[98%] mx-auto bg-gradient-to-br from-slate-800/95 via-slate-700/95 to-slate-800/95 h-auto sm:h-80 rounded-lg shadow-lg my-4 transform transition-all duration-300 hover:shadow-xl hover:scale-[1.01] border border-amber-500/20">
    <!-- Player Avatar Section -->
    <div class="flex flex-col items-center justify-center w-full sm:w-1/4 p-4 border-b sm:border-b-0 sm:border-r border-amber-500/20">
      <div class="relative group">
        <div class="w-36 h-36 rounded-3xl bg-gradient-to-br duration-300 from-slate-900/95 to-slate-800/95 flex items-center justify-center overflow-hidden border-4 border-amber-500/50 group-hover:border-amber-400 group-hover:shadow-lg group-hover:shadow-amber-500/40">
          <img 
            :src="profileImageUrl" 
            alt="Player Avatar" 
            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
            @error="profileImageUrl = '/images/default-profile.png'"
          >
        </div>
        <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-amber-600 to-amber-500 px-3 py-1 rounded-full border border-amber-400/50 transition-all duration-300 group-hover:border-amber-300 group-hover:bg-gradient-to-r group-hover:from-amber-500 group-hover:to-amber-400 shadow-lg shadow-amber-500/30 hover:scale-105">
          <span class="text-white font-bold fantasy-font tracking-wider drop-shadow-[0_0_8px_rgba(251,191,36,0.3)]">Level {{ userLevel }}</span>
        </div>
      </div>
      <h2 class="mt-4 text-xl font-bold text-amber-200 transition-colors duration-300 group-hover:text-amber-100 fantasy-font tracking-wider drop-shadow-[0_0_8px_rgba(251,191,36,0.3)]">{{ page.props.user?.name }}</h2>
      <p class="text-slate-200 transition-colors duration-300 group-hover:text-slate-100 fantasy-font-light">{{ trans(userClassName) }}</p>
    </div>

    <!-- Stats Section -->
    <div class="flex-1 p-6">
      <div class="space-y-4">
        <!-- Health Bar -->
        <div class="group">
          <div class="flex justify-between text-sm mb-1">
            <span class="text-red-300 font-medium transition-colors duration-300 group-hover:text-red-200 fantasy-font-light">{{ trans('Health') }}</span>
            <span class="text-slate-200 transition-colors duration-300 group-hover:text-slate-100 fantasy-font-light">{{ currentPlayerHealth }}/{{ maxPlayerHealth }}</span>
          </div>
          <div class="h-3 bg-slate-900/80 rounded-full overflow-hidden transition-all duration-300 group-hover:shadow-inner group-hover:shadow-red-500/40">
            <div
              class="h-full bg-gradient-to-r from-red-600 to-red-500 rounded-full transition-all duration-500 ease-out"
              :style="{ width: `${(currentPlayerHealth / maxPlayerHealth * 100) || 0}%` }"
            ></div>
          </div>
        </div>

        <!-- Energy Bar -->
        <div class="group">
          <div class="flex justify-between text-sm mb-1">
            <span class="text-blue-300 font-medium transition-colors duration-300 group-hover:text-blue-200 fantasy-font-light">{{ trans('Energy') }}</span>
            <span class="text-slate-200 transition-colors duration-300 group-hover:text-slate-100 fantasy-font-light">{{ currentPlayerEnergy }}/{{ maxPlayerEnergy }}</span>
          </div>
          <div class="h-3 bg-slate-900/80 rounded-full overflow-hidden transition-all duration-300 group-hover:shadow-inner group-hover:shadow-blue-500/40">
            <div
              class="h-full bg-gradient-to-r from-blue-600 to-blue-500 rounded-full transition-all duration-500 ease-out"
              :style="{ width: `${(currentPlayerEnergy / maxPlayerEnergy * 100) || 0}%` }"
            ></div>
          </div>
        </div>

        <!-- Experience Bar -->
        <div class="group">
          <div class="flex justify-between text-sm mb-1">
            <span class="text-emerald-300 font-medium transition-colors duration-300 group-hover:text-emerald-200 fantasy-font-light">{{ trans('Experience') }}</span>
            <span class="text-slate-200 transition-colors duration-300 group-hover:text-slate-100 fantasy-font-light">{{ currentPlayerExperience }}/{{ maxPlayerExperience }}</span>
          </div>
          <div class="h-3 bg-slate-900/80 rounded-full overflow-hidden transition-all duration-300 group-hover:shadow-inner group-hover:shadow-emerald-500/40">
            <div
              class="h-full bg-gradient-to-r from-emerald-600 to-emerald-500 rounded-full transition-all duration-500 ease-out"
              :style="{ width: experiencePercentage }"
            ></div>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap');

.fantasy-font {
  font-family: 'MedievalSharp', cursive;
  text-shadow: 0 0 10px rgba(251, 191, 36, 0.3);
}

.fantasy-font-light {
  font-family: 'MedievalSharp', cursive;
  letter-spacing: 0.5px;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.02);
  }
}

.group:hover .bg-gradient-to-r {
  animation: pulse 2s infinite;
}

.group:hover {
  transform: translateY(-1px);
}
</style>