<script setup>
import { ref, watch } from "vue";
import DashboardSidebar from "@/Components/DashboardSidebar.vue";

const isTaskListVisible = ref(true);
const showLevelUpNotification = ref(false);

const props = defineProps({
  userStatistics: Object,
  user: Object,
});

// Watch for level changes
watch(() => props.userStatistics?.level, (newLevel, oldLevel) => {
  if (oldLevel && newLevel > oldLevel) {
    showLevelUpNotification.value = true;
    setTimeout(() => {
      showLevelUpNotification.value = false;
    }, 3000);
  }
}, { immediate: true });



</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <!-- Level Up Notification -->
    <div v-if="showLevelUpNotification" 
         class="fixed top-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 ease-in-out z-50"
         :class="{ 'translate-x-0 opacity-100': showLevelUpNotification, 'translate-x-full opacity-0': !showLevelUpNotification }">
      <div class="flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>
        <span class="font-bold">Level Up!</span>
        <span>You are now level {{ userStatistics?.level }}</span>
      </div>
    </div>

    <div class="flex min-h-screen">
      <DashboardSidebar :userStatistics="userStatistics" :user="user"/>

      <!-- Main Content -->
      <main class="flex-1 p-6 overflow-y-auto">
        <!-- Quest Board -->
        <div class="max-w-4xl mx-auto">
          <div class="bg-slate-800/80 rounded-lg border border-slate-700 shadow-lg">
            <!-- Quest Board Header -->
            <div class="flex justify-between items-center p-4 border-b border-slate-700">
              <h2 class="text-xl font-semibold text-slate-200">Active Quests</h2>
              <button
                @click="toggleTaskList"
                class="text-slate-400 hover:text-slate-200 transition-colors p-2 rounded-lg hover:bg-slate-700/50"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    :class="{ hidden: isTaskListVisible }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M19 9l-7 7-7-7"
                  />
                  <path
                    :class="{ hidden: !isTaskListVisible }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M19 15l-7-7-7 7"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<style>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}

::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.2);
}
</style>
