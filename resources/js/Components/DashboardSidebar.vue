<script setup>
import PlayerPanel from '@/Components/PlayerPanel.vue';
import OptionButton from '@/Components/OptionButton.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  userStatistics: Object,
  user: Object,
});

function toggleTaskList() {
  isTaskListVisible.value = !isTaskListVisible.value;
}

function logout() {
  router.post(route('logout'));
}

function reduceHealth() {
  router.post(route('user.health'), {}, {
    preserveScroll: true,
    preserveState: false,
    onSuccess: (page) => {
      router.reload({ only: ['userStatistics'] });
    },
    onError: (errors) => {
      console.error('Error updating health:', errors);
    }
  });
}

function addExperience() {
  router.post(route('user.addExperience'), {}, {
    preserveScroll: true,
    preserveState: false,
    onSuccess: (page) => {
      router.reload({ only: ['userStatistics'] });
    },
    onError: (errors) => {
      console.error('Error updating experience:', errors);
    }
  });
}

</script>

<template>
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
              route="/dashboard"
              class="w-full px-4 py-2 text-slate-200 hover:bg-slate-700/50 rounded-lg transition-colors"
            />
          </li>
          <li>
            <OptionButton 
              optionText="Settings" 
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
</template>