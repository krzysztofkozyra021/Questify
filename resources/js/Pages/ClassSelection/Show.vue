<script setup>
import { ref, computed } from 'vue'
import { route } from 'ziggy-js'
import { useTranslation } from '@/Composables/useTranslation'
import Preloader from '@/Components/Preloader.vue'
import { usePage, router } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'

const { trans } = useTranslation()

useHead({
  title: trans('Class Selection') + ' | Questify',
})

const props = defineProps({
  classes: {
    type: Array,
    required: true,
  },
})

const selectedClass = ref(null)

const statRanges = {
  health: { min: 0.5, max: 1.8 },
  energy: { min: 0.4, max: 2},
  exp: { min: 0.6, max: 2 },
}

const classImages = {
  1: '/images/classes/ratWarrior.webp',
  2: '/images/classes/ratMage.webp',
  3: '/images/classes/ratRogue.webp',
  4: '/images/classes/ratPaladin.webp',
}

const classesWithImages = computed(() => {
  return props.classes.map(classItem => ({
    ...classItem,
    image: classImages[classItem.id] || '/images/classes/ratWarrior.webp',
  }))
})

function selectClass(classId) {
  selectedClass.value = classId
}

function confirmSelection() {
  if (!selectedClass.value) return
  router.post(route('select-class.store'), {
    class_id: selectedClass.value,
  })
}

function getPercentage(multiplier, statType) {
  const { min, max } = statRanges[statType]
  const range = max - min
  const normalizedValue = (multiplier - min) / range
  const scaleValue = Math.round(normalizedValue * 4) + 1
  const percentage = scaleValue * 20
  return Math.min(percentage, 100)
}
</script>

<template>
  <Preloader />
  <div class="flex min-h-screen size-full flex-col pt-2 sm:pt-4 bg-slate-800 background">
    <div class="text-center mb-2 sm:mb-4 px-4">
      <h1 class="text-2xl sm:text-3xl font-bold text-amber-100 mb-1">{{ trans('Choose Your Class') }}</h1>
      <p class="text-sm sm:text-base text-stone-100">{{ trans('Select a class to begin your journey. This choice cannot be changed later.') }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6 p-2 sm:p-4">
      <div
        v-for="classItem in classesWithImages"
        :key="classItem.id"
        class="flex flex-col justify-between bg-slate-600 bg-opacity-20 h-auto w-full rounded-lg shadow-md p-2 sm:p-4 ring-2 hover:-translate-y-2 ring-stone-500 hover:ring-amber-500 cursor-pointer transition-all duration-300 relative"
        :class="{
          'ring-2 ring-amber-400 bg-slate-700': selectedClass === classItem.id,
          'opacity-40 grayscale hover:-translate-y-2 hover:opacity-80  hover:grayscale-0 hover:shadow-lg': selectedClass && selectedClass !== classItem.id
        }"
        @click="selectClass(classItem.id)"
      >
        <div class="w-full opacity-100 rounded-lg mb-2 flex items-center justify-center">
          <img
            :src="classItem.image"
            alt="Class Image"
            class="w-24 h-48 sm:w-32 sm:h-64 object-contain rounded-lg"
          >
        </div>
        <h2 class="text-lg sm:text-2xl font-bold text-center text-amber-400 mb-2 sm:mb-3">{{ trans(classItem.name) }}</h2>

        <!-- Stats section with simplified bars -->
        <div class="space-y-2 sm:space-y-3 mb-2 sm:mb-3">
          <!-- Health stat -->
          <div>
            <div class="flex items-center mb-1">
              <span class="font-medium text-stone-100 w-16 sm:w-20 text-xs sm:text-sm">{{ trans('Health') }}:</span>
              <div class="w-full bg-slate-700 h-2 ml-2">
                <div
                  class="bg-red-600 text-white h-2"
                  :style="{ width: `${getPercentage(classItem.health_multiplier, 'health')}%` }"
                />
              </div>
            </div>
          </div>

          <!-- Energy stat -->
          <div>
            <div class="flex items-center mb-1">
              <span class="font-medium text-stone-100 w-16 sm:w-20 text-xs sm:text-sm">{{ trans('Energy') }}:</span>
              <div class="w-full bg-slate-700 h-2 ml-2">
                <div
                  class="bg-blue-600 h-2"
                  :style="{ width: `${getPercentage(classItem.energy_multiplier, 'energy')}%` }"
                />
              </div>
            </div>
          </div>

          <!-- Experience stat -->
          <div>
            <div class="flex items-center mb-1">
              <span class="font-medium text-stone-100 w-16 sm:w-20 text-xs sm:text-sm">{{ trans('EXP Gain') }}:</span>
              <div class="w-full bg-slate-700 h-2 ml-2">
                <div
                  class="bg-amber-600 h-2"
                  :style="{ width: `${getPercentage(classItem.exp_multiplier, 'exp')}%` }"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Special ability section -->
        <div class="pt-2 border-t border-slate-500 mt-auto">
          <h3 class="text-sm sm:text-base font-semibold text-stone-100 mb-1">{{ trans('Special Ability') }}</h3>
          <p class="text-xs sm:text-sm text-stone-100 overflow-hidden text-ellipsis line-clamp-2">{{ trans(classItem.special_ability) }}</p>
        </div>
      </div>
    </div>

    <div class="flex justify-center w-full px-4 pb-4">
      <button
        class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 font-medium text-base sm:text-lg rounded-lg text-stone-100 transition-all duration-300 hover:bg-amber-500/10"
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
