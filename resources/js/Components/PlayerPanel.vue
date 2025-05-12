
<script setup>
import { computed, ref, onMounted } from 'vue';
import { useTranslation } from '@/composables/useTranslation.js';


const { trans } = useTranslation()
const props = defineProps({
  userStatistics: Object,
  user: Object,
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
onMounted(fetchProfileImage);

const currentPlayerHealth = computed(() => {
  return Math.round(props.userStatistics.current_health);
});
const maxPlayerHealth = computed(() => {
  return Math.round(props.userStatistics.max_health);
});

const currentPlayerEnergy = computed(() => {
  return Math.round(props.userStatistics.current_energy);
});
const maxPlayerEnergy = computed(() => {
  return Math.round(props.userStatistics.max_energy);
});

const currentPlayerExperience = computed(() => {
  return Math.round(props.userStatistics.current_experience);
});
const maxPlayerExperience = computed(() => {
  return Math.round(props.userStatistics.next_level_experience);
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


