<template>
  <div class="flex min-h-screen h-full w-full flex-col pt-8 background">
    <div class="text-center mb-12">
      <h1 class="text-4xl font-bold text-white mb-2">Choose Your Class</h1>
      <p class="text-lg text-gray-600">Select a class to begin your journey. This choice cannot be changed later.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-28 mb-12 px-4">
      <div
        v-for="classItem in classes"
        :key="classItem.id"
        class="bg-white bg-opacity-20 h-[650px] w-full rounded-lg shadow-md p-6 cursor-pointer transition-all duration-300 hover:-translate-y-2 hover:opacity-80 hover:grayscale-0 hover:shadow-lg relative"
        :class="{ 
          'ring-2 ring-green-500 bg-green-50': selectedClass === classItem.id,
          'opacity-40 grayscale': selectedClass && selectedClass !== classItem.id
        }"
        @click="selectClass(classItem.id)"
      >
      <div class="w-full opacity-100 rounded-lg mb-4 flex items-center justify-center">
        <img
  :src="classItem.image"
  alt="Class Image"
  class="w-36 h-80 object-fill rounded-lg  "/>
      </div>
        <h2 class="text-2xl font-bold text-center text-purple-400 mb-6">{{ classItem.name }}</h2>

        <!-- Stats section with simplified bars -->
        <div class="space-y-4 mb-6">
          <!-- Health stat -->
          <div>
            <div class="flex items-center mb-1">
              <span class="font-medium text-gray-100 w-24">Health:</span>
              <div class="w-full bg-gray-200 rounded-full h-2.5 ml-2">
                <div
                  class="bg-red-600 text-white h-2.5 rounded-full"
                  :style="{ width: `${getPercentage(classItem.health_multiplier, 'health')}%` }"
                ></div>
              </div>
            </div>
          </div>

          <!-- Energy stat -->
          <div>
            <div class="flex items-center mb-1">
              <span class="font-medium text-white w-24">Energy:</span>
              <div class="w-full bg-gray-200 rounded-full h-2.5 ml-2">
                <div
                  class="bg-blue-600 h-2.5 rounded-full"
                  :style="{ width: `${getPercentage(classItem.energy_multiplier, 'energy')}%` }"
                ></div>
              </div>
            </div>
          </div>

          <!-- Experience stat -->
          <div>
            <div class="flex items-center mb-1">
              <span class="font-medium text-white w-24">EXP Gain:</span>
              <div class="w-full bg-gray-200 rounded-full h-2.5 ml-2">
                <div
                  class="bg-purple-600 h-2.5 rounded-full"
                  :style="{ width: `${getPercentage(classItem.exp_multiplier, 'exp')}%` }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Special ability section -->
        <div class="pt-4 border-t border-gray-200">
          <h3 class="text-lg font-semibold text-white mb-2">Special Ability</h3>
          <p class="text-gray-100">{{ classItem.special_ability }}</p>
        </div>
      </div>
    </div>

    <div class="flex justify-center w-full">
      <button
        class="px-8 py-2 font-medium text-lg rounded-md h-20 text-white transition-colors duration-300"
        :class="{
          'bg-green-600 hover:bg-green-700': selectedClass,
          'bg-gray-400 cursor-not-allowed': !selectedClass
        }"
        :disabled="!selectedClass"
        @click="confirmSelection"
      >
        Confirm Selection
      </button>
    </div>
  </div>
</template>

<script>
import {route} from "ziggy-js";

export default {
  props: {
    classes: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      selectedClass: null,
      classes: [
        {
          id: 1,
          name: 'Warrior',
          health_multiplier: 1.2,
          energy_multiplier: 0.8,
          exp_multiplier: 1.0,
          special_ability: 'Shield Bash',
          image: '/images/szczurWojownik.webp'
        },
        {
          id: 2,
          name: 'Mage',
          health_multiplier: 0.8,
          energy_multiplier: 1.5,
          exp_multiplier: 1.2,
          special_ability: 'Fireball',
          image: '/images/szczurMag.webp'
        },
        {
          id: 3,
          name: 'Rogue',
          health_multiplier: 0.9,
          energy_multiplier: 1.3,
          exp_multiplier: 1.4,
          special_ability: 'Stealth',
          image: '/images/szczurRzezimieszek.webp'
        },
        {
          id: 4,
          name: 'Cleric',
          health_multiplier: 1.0,
          energy_multiplier: 1.0,
          exp_multiplier: 1.5,
          special_ability: 'Heal',
          image: '/images/szczurPalladyn.webp'
        }
      ],
      statRanges: {
        health: {
          min: 0.7,
          max: 1.4
        },
        energy: {
          min: 0.7,
          max: 1.2
        },
        exp: {
          min: 0.9,
          max: 1.5
        }
      }
    }
  },
  methods: {
    selectClass(classId) {
      this.selectedClass = classId;
    },

    confirmSelection() {
      if (!this.selectedClass) return;

      this.$inertia.post(route('select-class.store'), {
        class_id: this.selectedClass
      });
    },

    // Get percentage for progress bar width using 1-5 scale
   getPercentage(multiplier, statType) {
  const { min, max } = this.statRanges[statType];
  const range = max - min;
  const normalizedValue = (multiplier - min) / range;

  // Scale to 1-5 instead of 1-10
  const scaleValue = Math.round(normalizedValue * 4) + 1; // Scale to 1-5
  const percentage = scaleValue * 20; // Convert to percentage (20% per unit)

  // Ensure the percentage does not exceed 100%
  return Math.min(percentage, 100);
}
  }
}
</script>
<style scoped>
.pattern-background {
  position: absolute;
  inset: 0;
  background-image: url('/images/szczurMag.webp');
  background-repeat:  no-repeat;
  opacity: 0.3;
  z-index: 0;
}
.background{
  background-image: url('/images/background.webp');
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