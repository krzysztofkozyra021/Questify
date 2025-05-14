<script setup>
import { ref } from "vue";
import DashboardBar from "@/Components/DashboardBar.vue";
import { onMounted } from "vue";
import { getMotivationalQuote } from "@/Composables/getMotivationalQuote";
import Notification from "@/Components/Notification.vue";
import { usePage } from '@inertiajs/vue3';
import Footer from "@/Components/Footer.vue";
import PlayerPanel from "@/Components/PlayerPanel.vue";

const page = usePage();

const isTaskListVisible = ref(true);
const showMotivationalQuote = ref(false);
const motivationalQuote = ref(page.props.motivationalQuote);

// Dodajemy funkcje do obsługi zadań
const toggleTask = (taskList, taskId) => {
  const task = taskList.value.find(t => t.id === taskId);
  if (task) {
    task.completed = !task.completed;
  }
};

// Modyfikujemy przykładowe dane, dodając pole completed
const obligations = ref([
  {
    id: 1,
    title: "Zadanie domowe z matematyki",
    description: "Rozwiąż zadania 1-5 ze strony 45",
    due_date: "2024-03-20",
    completed: false
  },
  {
    id: 2,
    title: "Projekt na informatykę",
    description: "Dokończ prezentację",
    due_date: "2024-03-25",
    completed: false
  }
]);

const dailyTasks = ref([
  {
    id: 1,
    title: "Poranny trening",
    description: "30 minut ćwiczeń",
    reward: 50,
    completed: false
  },
  {
    id: 2,
    title: "Czytanie książki",
    description: "Przeczytaj 30 stron",
    reward: 30,
    completed: false
  }
]);

const todoTasks = ref([
  {
    id: 1,
    title: "Zrobić zakupy",
    description: "Mleko, chleb, jajka",
    priority: "Wysoki",
    completed: false
  },
  {
    id: 2,
    title: "Zadzwonić do babci",
    description: "Umówić się na weekend",
    priority: "Średni",
    completed: false
  }
]);

// Show motivational quote after user visits dashboard
onMounted(async () => {
  // Only show if not already shown (prevents double popup if page reloads quickly)
  if (!showMotivationalQuote.value) {
    // Fetch the quote
    motivationalQuote.value = await getMotivationalQuote();

    if (motivationalQuote.value) {
      showMotivationalQuote.value = true;
    }
    // Hide after 5 seconds
    setTimeout(() => {
      showMotivationalQuote.value = false;
    }, 5000);
  }
});

</script>
<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">


    <!-- Motivational Quote -->
    <Notification :showNotification="showMotivationalQuote" :textToDisplay="motivationalQuote" />

    <div class="flex flex-col min-h-screen">
      <DashboardBar/>
      <PlayerPanel/>
      <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 w-[98%] bg-slate-600 rounded-lg shadow-lg mx-auto my-4 p-2 gap-2">
        <div class="bg-slate-800 rounded-lg shadow-lg p-4 hover:shadow-xl transition-shadow duration-300 border border-slate-700 flex flex-col">
          <h2 class="text-xl md:text-2xl font-bold text-slate-200 mb-4 text-center">Obowiązki</h2>
          <div class="flex-1 overflow-y-auto custom-scrollbar max-h-[300px] md:max-h-[400px] lg:max-h-[500px]">
            <ul class="space-y-2">
              <li v-for="task in obligations" :key="task.id" 
                  class="bg-slate-700/50 p-3 rounded-lg border border-slate-600 hover:bg-slate-700 transition-colors"
                  :class="{ 'opacity-50': task.completed }">
                <div class="flex items-center gap-3">
                  <input type="checkbox" 
                         :checked="task.completed"
                         @change="toggleTask(obligations, task.id)"
                         class="w-5 h-5 rounded border-slate-600 bg-slate-700 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-slate-800">
                  <div class="flex-1">
                    <div class="flex items-center justify-between">
                      <span class="text-slate-200" :class="{ 'line-through': task.completed }">{{ task.title }}</span>
                      <span class="text-slate-400 text-sm">{{ task.due_date }}</span>
                    </div>
                    <p class="text-slate-300 text-sm mt-1" :class="{ 'line-through': task.completed }">{{ task.description }}</p>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <div class="bg-slate-800 rounded-lg shadow-lg p-4 hover:shadow-xl transition-shadow duration-300 border border-slate-700 flex flex-col">
          <h2 class="text-xl md:text-2xl font-bold text-slate-200 mb-4 text-center">Dzienne</h2>
          <div class="flex-1 overflow-y-auto custom-scrollbar max-h-[300px] md:max-h-[400px] lg:max-h-[500px]">
            <ul class="space-y-2">
              <li v-for="task in dailyTasks" :key="task.id" 
                  class="bg-slate-700/50 p-3 rounded-lg border border-slate-600 hover:bg-slate-700 transition-colors"
                  :class="{ 'opacity-50': task.completed }">
                <div class="flex items-center gap-3">
                  <input type="checkbox" 
                         :checked="task.completed"
                         @change="toggleTask(dailyTasks, task.id)"
                         class="w-5 h-5 rounded border-slate-600 bg-slate-700 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-slate-800">
                  <div class="flex-1">
                    <div class="flex items-center justify-between">
                      <span class="text-slate-200" :class="{ 'line-through': task.completed }">{{ task.title }}</span>
                      <span class="text-emerald-400 text-sm">{{ task.reward }} XP</span>
                    </div>
                    <p class="text-slate-300 text-sm mt-1" :class="{ 'line-through': task.completed }">{{ task.description }}</p>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <div class="bg-slate-800 rounded-lg shadow-lg p-4 hover:shadow-xl transition-shadow duration-300 border border-slate-700 flex flex-col">
          <h2 class="text-xl md:text-2xl font-bold text-slate-200 mb-4 text-center">TO DO</h2>
          <div class="flex-1 overflow-y-auto custom-scrollbar max-h-[300px] md:max-h-[400px] lg:max-h-[500px]">
            <ul class="space-y-2">
              <li v-for="task in todoTasks" :key="task.id" 
                  class="bg-slate-700/50 p-3 rounded-lg border border-slate-600 hover:bg-slate-700 transition-colors"
                  :class="{ 'opacity-50': task.completed }">
                <div class="flex items-center gap-3">
                  <input type="checkbox" 
                         :checked="task.completed"
                         @change="toggleTask(todoTasks, task.id)"
                         class="w-5 h-5 rounded border-slate-600 bg-slate-700 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-slate-800">
                  <div class="flex-1">
                    <div class="flex items-center justify-between">
                      <span class="text-slate-200" :class="{ 'line-through': task.completed }">{{ task.title }}</span>
                      <span class="text-blue-400 text-sm">{{ task.priority }}</span>
                    </div>
                    <p class="text-slate-300 text-sm mt-1" :class="{ 'line-through': task.completed }">{{ task.description }}</p>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
      <Footer/>
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
