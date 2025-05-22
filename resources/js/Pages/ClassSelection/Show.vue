<script setup>
import { ref, computed } from 'vue';
import { route } from 'ziggy-js';
import { useTranslation } from '@/Composables/useTranslation';
import Preloader from '@/Components/Preloader.vue';
import { usePage, router } from '@inertiajs/vue3';

const props = defineProps({
  classes: {
    type: Array,
    required: true
  }
});

const { trans } = useTranslation();
const selectedClass = ref(null);

const statRanges = {
  health: { min: 0.5, max: 1.8 },
  energy: { min: 0.4, max: 2},
  exp: { min: 0.6, max: 2 }
};

const classImages = {
  1: '/images/classes/ratWarrior.webp',
  2: '/images/classes/ratMage.webp',
  3: '/images/classes/ratRogue.webp',
  4: '/images/classes/ratPaladin.webp'
};

const classesWithImages = computed(() => {
  return props.classes.map(classItem => ({
    ...classItem,
    image: classImages[classItem.id] || '/images/classes/ratWarrior.webp'
  }));
});

function selectClass(classId) {
  selectedClass.value = classId;
}

function confirmSelection() {
  if (!selectedClass.value) return;
  router.post(route('select-class.store'), {
    class_id: selectedClass.value
  });
}

function getPercentage(multiplier, statType) {
  const { min, max } = statRanges[statType];
  const range = max - min;
  const normalizedValue = (multiplier - min) / range;
  const scaleValue = Math.round(normalizedValue * 4) + 1;
  const percentage = scaleValue * 20;
  return Math.min(percentage, 100);
}
</script>

<template>
  <Preloader />
  <div class="flex min-h-screen h-full w-full flex-col pt-4 sm:pt-8 bg-slate-800 background">
    <div class="text-center mb-6 sm:mb-12 px-4">
      <h1 class="text-3xl sm:text-4xl font-bold text-amber-100 mb-2">{{ trans('Choose Your Class') }}</h1>
      <p class="text-base sm:text-lg text-stone-100">{{ trans('Select a class to begin your journey. This choice cannot be changed later.') }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 lg:gap-12 mb-8 sm:mb-12 p-4 sm:p-8">
      <div
        v-for="classItem in classesWithImages"
        :key="classItem.id"
        class="flex flex-col justify-between bg-slate-600 bg-opacity-20 h-auto w-full rounded-lg shadow-md p-4 sm:p-6 ring-2 hover:-translate-y-2 ring-stone-500 hover:ring-amber-500 cursor-pointer transition-all duration-300 relative"
        :class="{
          'ring-2 ring-amber-400 bg-slate-700': selectedClass === classItem.id,
          'opacity-40 grayscale hover:-translate-y-2 hover:opacity-80  hover:grayscale-0 hover:shadow-lg': selectedClass && selectedClass !== classItem.id
        }"
        @click="selectClass(classItem.id)"
      >
        <div class="w-full opacity-100 rounded-lg mb-4 flex items-center justify-center">
          <img
            :src="classItem.image"
            alt="Class Image"
            class="w-28 h-64 sm:w-36 sm:h-80 object-cover rounded-lg"
          />
        </div>
        <h2 class="text-xl sm:text-3xl font-bold text-center text-amber-400 mb-4 sm:mb-6">{{ trans(classItem.name) }}</h2>

        <!-- Stats section with simplified bars -->
        <div class="space-y-3 sm:space-y-4 mb-4 sm:mb-6">
          <!-- Health stat -->
          <div>
            <div class="flex items-center mb-1">
              <span class="font-medium text-stone-100 w-20 sm:w-24 text-sm sm:text-base">{{ trans('Health') }}:</span>
              <div class="w-full bg-slate-700  h-2 sm:h-2.5 ml-2">
                <div
                  class="bg-red-600 text-white h-2 sm:h-2.5 "
                  :style="{ width: `${getPercentage(classItem.health_multiplier, 'health')}%` }"
                ></div>
              </div>
            </div>
          </div>

          <!-- Energy stat -->
          <div>
            <div class="flex items-center mb-1">
              <span class="font-medium text-stone-100 w-20 sm:w-24 text-sm sm:text-base">{{ trans('Energy') }}:</span>
              <div class="w-full bg-slate-700  h-2 sm:h-2.5 ml-2">
                <div
                  class="bg-blue-600 h-2 sm:h-2.5 "
                  :style="{ width: `${getPercentage(classItem.energy_multiplier, 'energy')}%` }"
                ></div>
              </div>
            </div>
          </div>

          <!-- Experience stat -->
          <div>
            <div class="flex items-center mb-1">
              <span class="font-medium text-stone-100 w-20 sm:w-24 text-sm sm:text-base">{{ trans('EXP Gain') }}:</span>
              <div class="w-full bg-slate-700  h-2 sm:h-2.5 ml-2">
                <div
                  class="bg-amber-600 h-2 sm:h-2.5 "
                  :style="{ width: `${getPercentage(classItem.exp_multiplier, 'exp')}%` }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Special ability section -->
        <div class="pt-3 sm:pt-4 border-t border-slate-500 mt-auto">
          <h3 class="text-base sm:text-lg font-semibold text-stone-100 mb-1 sm:mb-2">{{ trans('Special Ability') }}</h3>
          <p class="text-sm sm:text-base text-stone-100 overflow-hidden text-ellipsis line-clamp-3 sm:line-clamp-2 max-h-16 sm:max-h-12">{{ trans(classItem.special_ability) }}</p>
        </div>
      </div>
    </div>

    <div class="flex justify-center w-full px-4 pb-8">
      <button
        class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 font-medium text-lg sm:text-xl rounded-lg text-stone-100 transition-all duration-300 hover:bg-amber-500/10"
        :class="{
          'opacity-50 cursor-not-allowed': !selectedClass,
          'hover:scale-105': selectedClass
        }"
        :disabled="!selectedClass"
        @click="confirmSelection"
      >
        {{ trans('Confirm Selection') }}
      </button>
    </div>
  </div>
</template>

<style scoped>
.background{
  background-image: url('/images/classSelectionBackground.webp');
  background-repeat: repeat;
  background-position: top left;
  animation: backgroundMove 40s ease-in-out infinite;
  will-change: background-position;
  transform: translateZ(0);
  backface-visibility: hidden;
}

@keyframes backgroundMove {
  0% {
    background-position: 0% 0%;
  }
  25% {
    background-position: 25% 25%;
  }
  50% {
    background-position: 50% 50%;
  }
  75% {
    background-position: 25% 25%;
  }
  100% {
    background-position: 0% 0%;
  }
}
</style>