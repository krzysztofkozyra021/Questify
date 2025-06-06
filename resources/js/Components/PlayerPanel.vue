<script setup>
import { computed, ref, onMounted, watch, watchEffect } from 'vue'
import { useTranslation } from '@/Composables/useTranslation'
import { usePage } from '@inertiajs/vue3'


const { trans } = useTranslation()
const page = usePage()

// Create local reactive copy of userStatistics
const localStats = ref({...page.props.userStatistics})
const userClassName = page.props.userClassName

// Update local stats when props change
watch(() => page.props.userStatistics, (newStats) => {
  if (newStats) {
    localStats.value = {...newStats}
  }
}, { deep: true })

watchEffect(() => {
  const userStats = page.props.userStatistics

  if (userStats && JSON.stringify(userStats) !== JSON.stringify(localStats.value)) {
    localStats.value = {...userStats}
  }
})

const userLevel = computed(() => {
  return localStats.value?.level || 1
})

const profileImageUrl = ref('/images/default-profile.png')

const fetchProfileImage = async () => {
  try {
    const response = await fetch('/profile/image')
    const data = await response.json()
    if (data.profile_image_url) {
      profileImageUrl.value = data.profile_image_url
    }
  } catch (error) {
    console.error('Error fetching profile image:', error)
  }
}

// Fetch profile image on mount
onMounted(() => {
  fetchProfileImage()
})

const currentPlayerHealth = computed(() => {
  return Math.round(localStats.value?.current_health || 0)
})

const maxPlayerHealth = computed(() => {
  return Math.round(localStats.value?.max_health || 100)
})

const currentPlayerEnergy = computed(() => {
  return Math.round(localStats.value?.current_energy || 0)
})

const maxPlayerEnergy = computed(() => {
  return Math.round(localStats.value?.max_energy || 100)
})

const currentPlayerExperience = computed(() => {
  return Math.round(localStats.value?.current_experience || 0)
})

const maxPlayerExperience = computed(() => {
  return Math.round(localStats.value?.next_level_experience || 100)
})

const experiencePercentage = computed(() => {
  if (!maxPlayerExperience.value) return '0%'  
  return `${(currentPlayerExperience.value / maxPlayerExperience.value * 100)}%`
})

</script>

<template>
  <div class="flex flex-col sm:flex-row items-center rounded-xl shadow p-3 md:px-4 md:py-2 gap-3 md:gap-4 min-w-[280px] md:min-w-[400px] bg-stone-800">
    <!-- Profile Section -->
    <div class="flex flex-col sm:flex-row items-center gap-2 md:gap-4 w-full sm:w-auto sm:border-r sm:border-stone-600 sm:pr-4">
      <img :src="profileImageUrl" alt="Player Avatar" class="size-16 md:size-12 rounded-lg border-2 border-amber-400 shadow-md shrink-0" @error="profileImageUrl = '/images/default-profile.png'">
      <div class="flex flex-col items-center sm:items-start gap-1">
        <span class="text-lg md:text-lg text-white font-bold truncate max-w-[200px]">{{ page.props.user?.name }}</span>
        <div class="flex items-center gap-2">
          <span class="bg-amber-400 text-stone-900 font-bold px-2 py-0.5 rounded text-sm">Lv {{ userLevel }}</span>
          <span class="text-amber-400 text-sm font-semibold truncate max-w-[120px]">{{ trans(userClassName) }}</span>
        </div>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="flex flex-col gap-2 w-full sm:w-auto sm:flex-1 sm:pl-4">
      <!-- Health bar -->
      <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 md:size-4 text-red-500 shrink-0" viewBox="0 0 24 24" fill="currentColor">
          <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
        <div class="flex-1 h-3 md:h-3 bg-gray-100 overflow-hidden rounded-full">
          <div class="h-full bg-red-500 transition-all duration-500 ease-in-out rounded-full" :style="{ width: ((currentPlayerHealth / maxPlayerHealth * 100) || 0) + '%' }" />
        </div>
        <span class="text-sm text-white font-normal whitespace-nowrap ml-1 min-w-[60px] text-right">{{ currentPlayerHealth }}/{{ maxPlayerHealth }}</span>
      </div>
      <!-- Energy bar -->
      <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 md:size-4 text-blue-500 shrink-0" viewBox="0 0 24 24" fill="currentColor">
          <path d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        <div class="flex-1 h-3 md:h-3 bg-gray-100 overflow-hidden rounded-full">
          <div class="h-full bg-blue-500 transition-all duration-500 ease-in-out rounded-full" :style="{ width: ((currentPlayerEnergy / maxPlayerEnergy * 100) || 0) + '%' }" />
        </div>
        <span class="text-sm text-white font-normal whitespace-nowrap ml-1 min-w-[60px] text-right">{{ currentPlayerEnergy }}/{{ maxPlayerEnergy }}</span>
      </div>
      <!-- XP bar -->
      <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 md:size-4 text-amber-400 shrink-0" viewBox="0 0 24 24" fill="currentColor">
          <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
        </svg>
        <div class="flex-1 h-3 md:h-3 bg-gray-100 overflow-hidden rounded-full">
          <div class="h-full bg-amber-400 transition-all duration-500 ease-in-out rounded-full" :style="{ width: ((currentPlayerExperience / maxPlayerExperience * 100) || 0) + '%' }" />
        </div>
        <span class="text-sm text-white font-normal whitespace-nowrap ml-1 min-w-[60px] text-right">{{ currentPlayerExperience }}/{{ maxPlayerExperience }}</span>
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

/* Add smooth transition for the stats bars */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

@media (max-width: 640px) {
  .fantasy-font {
    font-size: 0.875rem;
  }
  
  .fantasy-font-light {
    font-size: 0.75rem;
  }
}
</style>
