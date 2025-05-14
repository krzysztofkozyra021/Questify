<script setup>
import PlayerPanel from '@/Components/PlayerPanel.vue';
import OptionButton from '@/Components/OptionButton.vue';
import { router } from '@inertiajs/vue3';

function toggleTaskList() {
  isTaskListVisible.value = !isTaskListVisible.value;
}

function logout() {
  router.post(route('logout'));
}
</script>

<template>
<aside class="w-80 min-h-screen bg-slate-800/95 border-r border-slate-700">
    <div class="p-4 h-full flex flex-col">
      <!-- Player Panel -->
      <div class="mb-6">
        <PlayerPanel/>
      </div>

      <!-- Navigation Menu -->
      <div class="flex-1 space-y-4">
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
              optionText="Log out" 
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