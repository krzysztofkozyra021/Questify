<script setup>
import PlayerPanel from "@/Pages/PlayerPanel.vue";
import Task from "@/Pages/Task.vue";
import OptionButton from "@/Pages/OptionButton.vue";
import DayChange from "@/Pages/DayChange.vue";
import { ref } from "vue";

const isTaskListVisible = ref(true); // Kontroluje widoczność listy zadań

function toggleTaskList() {
  isTaskListVisible.value = !isTaskListVisible.value; // Przełącz widoczność
}

</script>

<template>
  <div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-80 h-screen bg-zinc-950 text-white flex flex-col p-4 justify-between">
      <PlayerPanel @showPlayerDetails="handlePlayerDetails" />
      <div class="space-y-4">
        <h1 class="text-2xl font-bold mb-4">Menu</h1>
        <ul class="space-y-2">
          <li>
            <OptionButton optionText="Zadania" route="/tasks" />
          </li>
          <li>
            <OptionButton optionText="Ustawienia" route="/settings" />
          </li>
          <li>
            <OptionButton optionText="Wyloguj się" route="/logout" />
          </li>
        </ul>
      </div>
      <div>
        <button class="w-full bg-zinc-600 hover:bg-zinc-400 p-2 mt-4 rounded">⚙️ Ustawienia</button>
      </div>
    </aside>

    <!-- Główna zawartość -->
    <main class="w-full h-screen bg-gray-100 p-4 flex justify-center items-center flex-col">
      <DayChange />

      <div class="w-full bg-white p-6 rounded-lg shadow-lg">
        <div class="flex justify-between items-center">
          <h1 class="text-2xl font-bold">Witaj, Tomaszu!</h1>
          <button
            @click="toggleTaskList"
            class="text-gray-600 hover:text-gray-800"
            data-accordion
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
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

        <!-- Lista zadań -->
        <ul
          v-show="isTaskListVisible"
          class="space-y-4 mt-4 transition-all duration-300 ease-in-out"
        >
          <li>
            <Task :task="{ name: 'Umyć naczynia', description: 'Umyć naczynia zalegające w zlewie' }" />
          </li>
          <li>
            <Task :task="{ name: 'Posprzątać pokój', description: 'Posprzątać pokój przed przyjaciółmi' }" />
          </li>
          <li>
            <Task :task="{ name: 'Zrobić zakupy', description: 'Kupić mleko i chleb' }" />
          </li>
        </ul>
      </div>
    </main>
  </div>
</template>
