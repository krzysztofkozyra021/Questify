<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import ErrorModal from '@/Components/ErrorModal.vue';
import HabitsSection from '@/Components/Habits/HabitsSection.vue';
import DailiesSection from '@/Components/Dailies/DailiesSection.vue';
import TodosSection from '@/Components/Todos/TodosSection.vue';

const page = usePage();

const showErrorModal = ref(false);
const errorMessage = ref('');
const difficulties = page.props.difficulties || [];
const resetConfigs = page.props.resetConfigs || [];

const userStats = page.props.userStatistics;
const userClassExpMultiplier = page.props.userClassExpMultiplier;


</script>

<template>
  <div class="min-h-screen bg-white flex flex-col">
    <Header :showPlayerPanel="true" />
    <ErrorModal 
      :show="showErrorModal"
      :message="errorMessage"
      @close="showErrorModal = false"
    />
    <main class="flex-1 w-full mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8 lg:gap-12 py-4 sm:py-6 md:py-8 lg:py-12 px-3 sm:px-4 md:px-6 lg:px-8">
      <!-- Habits Section -->
      <HabitsSection
        :user-stats="userStats"
        :user-class-exp-multiplier="userClassExpMultiplier"
        :difficulties="difficulties"
        :reset-configs="resetConfigs"
      />

      <!-- Dailies Section -->
      <DailiesSection
        :user-stats="userStats"
        :user-class-exp-multiplier="userClassExpMultiplier"
      />

      <!-- Todos Section -->
      <TodosSection
        :user-stats="userStats"
        :user-class-exp-multiplier="userClassExpMultiplier"
      />
    </main>
    <Footer />
  </div>
</template>

<style>
::-webkit-scrollbar {
  width: 7px;
}
::-webkit-scrollbar-thumb {
  background: #57534e;
  border-radius: 4px;
}
::-webkit-scrollbar-track {
  background: #f3f4f6;
}
</style>
