<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center mb-12">
      <h1 class="text-4xl font-bold text-gray-900 mb-2">Choose Your Class</h1>
      <p class="text-lg text-gray-600">Select a class to begin your journey. This choice cannot be changed later.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
      <div
        v-for="classItem in classes"
        :key="classItem.id"
        class="bg-white rounded-lg shadow-md p-6 cursor-pointer transition-all duration-300 hover:-translate-y-2 hover:shadow-lg"
        :class="{ 'ring-2 ring-green-500 bg-green-50': selectedClass === classItem.id }"
        @click="selectClass(classItem.id)"
      >
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">{{ classItem.name }}</h2>

        <!-- Stats section with simplified bars -->
        <div class="space-y-4 mb-6">
          <!-- Health stat -->
          <div>
            <div class="flex items-center mb-1">
              <span class="font-medium text-gray-700 w-24">Health:</span>
              <div class="w-full bg-gray-200 rounded-full h-2.5 ml-2">
                <div
                  class="bg-red-600 h-2.5 rounded-full"
                  :style="{ width: `${getPercentage(classItem.health_multiplier, 'health')}%` }"
                ></div>
              </div>
            </div>
          </div>

          <!-- Energy stat -->
          <div>
            <div class="flex items-center mb-1">
              <span class="font-medium text-gray-700 w-24">Energy:</span>
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
              <span class="font-medium text-gray-700 w-24">EXP Gain:</span>
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
          <h3 class="text-lg font-semibold text-gray-800 mb-2">Special Ability</h3>
          <p class="text-gray-600">{{ classItem.special_ability }}</p>
        </div>
      </div>
    </div>

    <div class="flex justify-center">
      <button
        class="px-8 py-4 font-medium text-lg rounded-md text-white transition-colors duration-300"
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
      return scaleValue * 20; // Convert to percentage (20% per unit)
    }
  }
}
</script>
