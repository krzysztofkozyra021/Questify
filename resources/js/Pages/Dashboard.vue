<script setup>
import { ref } from "vue";
import DashboardBar from "@/Components/DashboardBar.vue";
import { onMounted } from "vue";
import { getMotivationalQuote } from "@/Composables/getMotivationalQuote";
import Notification from "@/Components/Notification.vue";
import { usePage } from '@inertiajs/vue3';
import Footer from "@/Components/Footer.vue";
import PlayerPanel from "@/Components/PlayerPanel.vue";
import { useTranslation } from '@/Composables/useTranslation';

const { trans } = useTranslation();
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
    <div class="flex flex-col min-h-screen">
      <DashboardBar/>
      <PlayerPanel/>
      <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 w-[98%] mx-auto my-4 p-2 gap-2">
        <div class="rounded-lg pt-20 shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-amber-500/20 flex flex-col transform hover:scale-[1.01] scroll-bg">
          <h2 class="text-xl md:text-2xl mb-20 font-bold text-amber-200 mb-6 text-center fantasy-font tracking-wider drop-shadow-[0_0_8px_rgba(251,191,36,0.3)]">{{ trans('Obowiązki') }}</h2>
          <div class="mx-7 flex-1 overflow-y-auto custom-scrollbar max-h-[270px] md:max-h-[360px] lg:max-h-[450px] px-4">
            <ul class="space-y-2">
              <li v-for="task in obligations" :key="task.id" 
                  class="transition-all duration-300 transform hover:scale-[1.01]"
                  :class="{ 'opacity-20': task.completed }">
                <div class="flex items-center gap-2">
                  <input type="checkbox" 
                         :checked="task.completed"
                         @change="toggleTask(obligations, task.id)"
                         class="w-4 h-4 rounded border-slate-800 text-slate-800 bg-transparent shadow-sm checked:bg-slate-800 checked:border-slate-800 checked:text-amber-300 focus:ring-slate-800 focus:ring-offset-slate-200 transition-all duration-300 hover:scale-110">
                  <div class="flex-1">
                    <div class="flex items-center justify-between">
                      <span class="text-slate-800 font-semibold text-base transition-all duration-300 fantasy-font-light drop-shadow-[0_1px_0_rgba(0,0,0,0.15)]" :class="{ 'line-through': task.completed }">{{ task.title }}</span>
                      <span class="text-slate-800 font-semibold text-sm transition-all duration-300 drop-shadow-[0_1px_0_rgba(0,0,0,0.15)]">{{ task.due_date }}</span>
                    </div>
                    <p class="text-slate-800 text-sm mt-0.5 transition-all duration-300 drop-shadow-[0_1px_0_rgba(0,0,0,0.10)]" :class="{ 'line-through': task.completed }">{{ task.description }}</p>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <div class="rounded-lg pt-20 shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-amber-500/20 flex flex-col transform hover:scale-[1.01] scroll-bg">
          <h2 class="text-xl md:text-2xl mb-20 font-bold text-amber-200 mb-6 text-center fantasy-font tracking-wider drop-shadow-[0_0_8px_rgba(251,191,36,0.3)]">{{ trans('Dzienne') }}</h2>
          <div class="mx-7 flex-1 overflow-y-auto custom-scrollbar max-h-[270px] md:max-h-[360px] lg:max-h-[450px] px-4">
            <ul class="space-y-2">
              <li v-for="task in dailyTasks" :key="task.id" 
                  class="transition-all duration-300 transform hover:scale-[1.01]"
                  :class="{ 'opacity-20': task.completed }">
                <div class="flex items-center gap-2">
                  <input type="checkbox" 
                         :checked="task.completed"
                         @change="toggleTask(dailyTasks, task.id)"
                         class="w-4 h-4 rounded border-slate-800 text-slate-800 bg-transparent shadow-sm checked:bg-slate-800 checked:border-slate-800 checked:text-amber-300 focus:ring-slate-800 focus:ring-offset-slate-200 transition-all duration-300 hover:scale-110">
                  <div class="flex-1">
                    <div class="flex items-center justify-between">
                      <span class="text-slate-800 font-semibold text-base transition-all duration-300 fantasy-font-light drop-shadow-[0_1px_0_rgba(0,0,0,0.15)]" :class="{ 'line-through': task.completed }">{{ task.title }}</span>
                      <span class="text-slate-800 font-semibold text-sm transition-all duration-300 fantasy-font-light drop-shadow-[0_1px_0_rgba(0,0,0,0.15)]">{{ task.reward }} XP</span>
                    </div>
                    <p class="text-slate-800 text-sm mt-0.5 transition-all duration-300 drop-shadow-[0_1px_0_rgba(0,0,0,0.10)]" :class="{ 'line-through': task.completed }">{{ task.description }}</p>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <div class="rounded-lg pt-20 shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-amber-500/20 flex flex-col transform hover:scale-[1.01] scroll-bg">
          <h2 class="text-xl md:text-2xl mb-20 font-bold text-amber-200 mb-6 text-center fantasy-font tracking-wider drop-shadow-[0_0_8px_rgba(251,191,36,0.3)]">{{ trans('Do zrobienia') }}</h2>
          <div class="  mx-7 flex-1 overflow-y-auto custom-scrollbar max-h-[270px] md:max-h-[360px] lg:max-h-[450px] px-4">
            <ul class="space-y-2">
              <li v-for="task in todoTasks" :key="task.id" 
                  class="transition-all duration-300 transform hover:scale-[1.01]"
                  :class="{ 'opacity-20': task.completed }">
                <div class="flex items-center gap-2">
                  <input type="checkbox" 
                         :checked="task.completed"
                         @change="toggleTask(todoTasks, task.id)"
                         class="w-4 h-4 rounded border-slate-800 text-slate-800 bg-transparent shadow-sm checked:bg-slate-800 checked:border-slate-800 checked:text-amber-300 focus:ring-slate-800 focus:ring-offset-slate-200 transition-all duration-300 hover:scale-110">
                  <div class="flex-1">
                    <div class="flex items-center justify-between">
                      <span class="text-slate-800 font-semibold text-base transition-all duration-300 fantasy-font-light drop-shadow-[0_1px_0_rgba(0,0,0,0.15)]" :class="{ 'line-through': task.completed }">{{ task.title }}</span>
                      <span class="text-slate-800 font-semibold text-sm transition-all duration-300 fantasy-font-light drop-shadow-[0_1px_0_rgba(0,0,0,0.15)]">{{ task.priority }}</span>
                    </div>
                    <p class="text-slate-800 text-sm mt-0.5 transition-all duration-300 drop-shadow-[0_1px_0_rgba(0,0,0,0.10)]" :class="{ 'line-through': task.completed }">{{ task.description }}</p>
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
@import url('https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap');

.fantasy-font {
  font-family: 'MedievalSharp', cursive;
  text-shadow: 0 0 10px rgba(251, 191, 36, 0.3);
}

.fantasy-font-light {
  font-family: 'MedievalSharp', cursive;
  letter-spacing: 0.5px;
}

.scroll-bg {
  background-image: url('/images/scroll(1).png');
  background-size: 100% 100%;
  background-position: center;
  background-repeat: no-repeat;
  position: relative;
  aspect-ratio: 1/1.4;
}

.scroll-bg::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to bottom, rgba(15, 23, 42, 0.1), rgba(30, 41, 59, 0.1));
  border-radius: 0.5rem;
  z-index: 0;
}

.scroll-bg > * {
  position: relative;
  z-index: 1;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}

::-webkit-scrollbar-thumb {
  background: rgba(251, 191, 36, 0.3);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(251, 191, 36, 0.5);
}
</style>
