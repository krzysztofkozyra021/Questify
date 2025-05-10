<script setup>
import PlayerPanel from "@/Pages/PlayerPanel.vue";
import Task from "@/Pages/Task.vue";
import OptionButton from "@/Pages/OptionButton.vue";
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";

const isTaskListVisible = ref(true);
const showLevelUpNotification = ref(false);
const previousLevel = ref(null);

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
  previousLevel.value = newLevel;
}, { immediate: true });

function toggleTaskList() {
  isTaskListVisible.value = !isTaskListVisible.value;
}

function logout() {
  router.post(route('logout'));
}

function reduceHealth() {
  router.post(route('user.health'), {}, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: (page) => {
      // The userStatistics will be automatically updated in the props
    },
    onError: (errors) => {
      console.error('Error updating health:', errors);
    }
  });
}

function addExperience() {
  router.post(route('user.addExperience'), {}, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: (page) => {
      // The userStatistics will be automatically updated in the props
    },
    onError: (errors) => {
      console.error('Error updating experience:', errors);
    }
  });
}

</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <!-- Level Up Notification -->
    <div v-if="showLevelUpNotification" 
         class="fixed top-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 ease-in-out"
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
      <!-- Sidebar -->
      <aside class="w-80 min-h-screen bg-slate-800/95 border-r border-slate-700">
        <div class="p-4 h-full flex flex-col">
          <!-- Player Panel -->
          <div class="mb-6">
            <PlayerPanel :userStatistics="userStatistics" :user="user" @showPlayerDetails="handlePlayerDetails" />
          </div>

          <!-- Test Button -->
          <div class="mb-4">
            <button
              @click="reduceHealth"
              class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
            >
              Test: Reduce Health (-10)
            </button>
          </div>
          <div class="mb-4">
            <button
              @click="addExperience"
              class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
            >
              Test: Add Experience (10)
            </button>
          </div>

          <!-- Navigation Menu -->
          <div class="flex-1 space-y-4">
            <h1 class="text-xl font-semibold text-slate-200 text-center mb-4">
              Quest Log
            </h1>
            <ul class="space-y-2">
              <li>
                <OptionButton 
                  optionText="Quests" 
                  route="/tasks"
                  class="w-full px-4 py-2 text-slate-200 hover:bg-slate-700/50 rounded-lg transition-colors"
                />
              </li>
              <li>
                <OptionButton 
                  optionText="Inventory" 
                  route="/settings"
                  class="w-full px-4 py-2 text-slate-200 hover:bg-slate-700/50 rounded-lg transition-colors"
                />
              </li>
              <li>
                <OptionButton 
                  optionText="Leave Realm" 
                  route="/logout"
                  method="post"
                  class="w-full px-4 py-2 text-slate-200 hover:bg-slate-700/50 rounded-lg transition-colors"
                />
              </li>
            </ul>
          </div>
        </div>
      </aside>

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
