<script setup>
import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useHead } from '@vueuse/head'
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import ErrorModal from '@/Components/ErrorModal.vue';
import HabitsSection from '@/Components/Habits/HabitsSection.vue';
import DailiesSection from '@/Components/Dailies/DailiesSection.vue';
import TodosSection from '@/Components/Todos/TodosSection.vue';
import { useTranslation } from '@/Composables/useTranslation';

const { trans } = useTranslation();
const page = usePage();

useHead({
  title: () => trans('Dashboard') + " | Questify "
})

const showErrorModal = ref(false);
const errorMessage = ref('');
const difficulties = page.props.difficulties || [];
const resetConfigs = page.props.resetConfigs || [];
const habits = computed(() => page.props.habits || []);
const dailyTasks = computed(() => page.props.dailyTasks || []);
const todoTasks = computed(() => page.props.todoTasks || []);

const userStatistics = computed(() => page.props.userStatistics);
const userClassExpMultiplier = page.props.userClassExpMultiplier;
</script>

<template>
  <AppLayout>
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
          :user-stats="userStatistics"
          :user-class-exp-multiplier="userClassExpMultiplier"
          :difficulties="difficulties"
          :reset-configs="resetConfigs"
          :habits="habits"
        />

        <!-- Dailies Section -->
        <DailiesSection
          :user-stats="userStatistics"
          :user-class-exp-multiplier="userClassExpMultiplier"
        />

        <!-- Todos Section -->
        <TodosSection
          :user-stats="userStatistics"
          :user-class-exp-multiplier="userClassExpMultiplier"
          :todo-tasks="todoTasks"
          :difficulties="difficulties"
        />
      </main>
      <Footer />
    </div>
  </AppLayout>
</template>