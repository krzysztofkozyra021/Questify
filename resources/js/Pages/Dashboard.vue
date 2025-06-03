<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { useHead } from '@vueuse/head'
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import ErrorModal from '@/Components/ErrorModal.vue';
import HabitsSection from '@/Components/Habits/HabitsSection.vue';
import DailiesSection from '@/Components/Dailies/DailiesSection.vue';
import TodosSection from '@/Components/Todos/TodosSection.vue';
import { useTranslation } from '@/Composables/useTranslation';
import { useNotification } from '@/Composables/useNotification';
import { showMotivationalQuote } from '@/Composables/getMotivationalQuote';
import { syncDashboardData, SYNC_INTERVAL } from '@/Composables/syncDashboardData';
import { formatTime } from '@/Composables/formatTime';

const { trans } = useTranslation();
const page = usePage();
const { addNotification } = useNotification();

useHead({
  title: () => trans('Dashboard') + " | Questify "
})


// State
const syncInterval = ref(null);
const countdownInterval = ref(null);
const isSyncing = ref(false);
const nextSyncIn = ref(SYNC_INTERVAL);
const showErrorModal = ref(false);
const errorMessage = ref('');
const lastSyncTime = ref(null);

// Use computed properties for reactive data from page props
const todos = computed(() => page.props.todoTasks || {});
const habits = computed(() => page.props.habits || {});
const dailyTasks = computed(() => page.props.dailyTasks || {});
const userStatistics = computed(() => page.props.userStatistics || {});
const userClassExpMultiplier = computed(() => page.props.userClassExpMultiplier || 1);
const difficulties = computed(() => page.props.difficulties || []);
const resetConfigs = computed(() => page.props.resetConfigs || []);


// Background sync using axios (non-blocking)
const performBackgroundSync = async () => {
  try {
    const data = await syncDashboardData();
    
    if (data) {
      if (data.tasks) {
        page.props.todoTasks = data.tasks.todos;
        page.props.habits = data.tasks.habits;
        page.props.dailyTasks = data.tasks.dailies;
      }
      if (data.user) {
        page.props.userStatistics = {
          ...page.props.userStatistics,
          ...data.user
        };
      }
      
      lastSyncTime.value = new Date();
    }
  } catch (error) {}
};

// Inertia sync (blocking, shows loading state)
const performInertiaSync = async () => {
  if (isSyncing.value) return;
  
  isSyncing.value = true;
  try {
    router.reload({
      only: ['todoTasks', 'habits', 'dailyTasks', 'userStatistics'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        nextSyncIn.value = SYNC_INTERVAL;
        lastSyncTime.value = new Date();
      },
      onError: () => {
        errorMessage.value = 'Failed to sync dashboard data. Please try again later.';
        showErrorModal.value = true;
      }
    });
  } finally {
    isSyncing.value = false;
  }
};

const checkLastSyncTimeToPreventUserFromSyncingMultipleTimes = () => {
  if (lastSyncTime.value && (new Date() - lastSyncTime.value) < 10000) {
    return true;
  }
  return false;
}

// Manual sync triggered by user
const manualSync = async () => {
  if (checkLastSyncTimeToPreventUserFromSyncingMultipleTimes()) {
    addNotification('Wait 10 seconds before syncing again.', 'error');
    return;
  }
  try {
    nextSyncIn.value = SYNC_INTERVAL;
    addNotification('Syncing dashboard data...', 'info');
    await performInertiaSync();
    addNotification('Dashboard data synced successfully', 'success');
  } catch (error) {
    addNotification('Failed to sync dashboard data. Please try again later.', 'error');
  }
};

// Countdown timer
const updateCountdown = () => {
  if (nextSyncIn.value > 0) {
    nextSyncIn.value--;
  } else {
    // Reset countdown
    nextSyncIn.value = SYNC_INTERVAL;
  }
};

const startSyncIntervals = () => {
  stopSyncIntervals();
  
  // Set up countdown timer (updates every second)
  countdownInterval.value = setInterval(updateCountdown, 1000);

  syncInterval.value = setInterval(() => {
    performBackgroundSync();
  }, SYNC_INTERVAL * 1000);
  
};

const stopSyncIntervals = () => {
  if (syncInterval.value) {
    clearInterval(syncInterval.value);
    syncInterval.value = null;
  }
  if (countdownInterval.value) {
    clearInterval(countdownInterval.value);
    countdownInterval.value = null;
  }
};

const handleVisibilityChange = () => {
  if (document.visibilityState === 'visible') {
    performBackgroundSync();
    // Restart intervals when tab becomes visible
    startSyncIntervals();
  } else {
    // Pause intervals when tab is hidden to save resources
    stopSyncIntervals();
  }
};

const handleWindowFocus = () => {
  // Only sync if more than 10 seconds have passed since last sync
  if (!lastSyncTime.value || (new Date() - lastSyncTime.value) > 10000) {
    performBackgroundSync();
  }
};

onMounted(() => {
  
  showMotivationalQuote();
  performBackgroundSync();
  startSyncIntervals();
  
  document.addEventListener('visibilitychange', handleVisibilityChange);
  window.addEventListener('focus', handleWindowFocus);
  
  router.on('navigate', () => {
    nextSyncIn.value = SYNC_INTERVAL;
  });
});

onUnmounted(() => {
  stopSyncIntervals();
  
  document.removeEventListener('visibilitychange', handleVisibilityChange);
  window.removeEventListener('focus', handleWindowFocus);
});
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
      
      <!-- Sync Status Bar -->
      <div class="bg-slate-100 border-b border-slate-200 py-2 px-4 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <span class="text-sm text-slate-600">
            {{ trans('Next sync in') }}: {{ formatTime(nextSyncIn) }}
          </span>
          <div class="flex items-center space-x-2">
            <div v-if="isSyncing" class="flex items-center">
              <svg class="animate-spin h-4 w-4 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span class="ml-2 text-sm text-slate-600">{{ trans('Syncing...') }}</span>
            </div>
          </div>
        </div>
        <button 
          @click="manualSync" 
          :disabled="isSyncing"
          class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-slate-600 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          {{ trans('Sync') }}
        </button>
      </div>

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
          :daily-tasks="dailyTasks"
          :difficulties="difficulties"
          :reset-configs="resetConfigs"
        />

        <!-- Todos Section -->
        <TodosSection
          :user-stats="userStatistics"
          :user-class-exp-multiplier="userClassExpMultiplier"
          :todo-tasks="todos"
          :difficulties="difficulties"
        />
      </main>
      <Footer />
    </div>
  </AppLayout>
</template>