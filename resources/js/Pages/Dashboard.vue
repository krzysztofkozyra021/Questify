<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useTranslation } from '@/Composables/useTranslation';
import CreateHabitModal from '@/Components/Forms/CreateHabitModal.vue';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import ErrorModal from '@/Components/ErrorModal.vue';
import { router } from '@inertiajs/vue3';

const { trans } = useTranslation();
const page = usePage();

const DEFAULT_EXPERIENCE_REWARD = 5;

const showCreateHabitModal = ref(false);
const showErrorModal = ref(false);
const errorMessage = ref('');
const difficulties = page.props.difficulties || [];
const resetConfigs = page.props.resetConfigs || [];

const habits = computed(() => page.props.tasks?.habits || []);
const dailies = computed(() => page.props.tasks?.dailies || []);
const todos = computed(() => page.props.tasks?.todos || []);

const userStats = page.props.userStatistics;
const userClassExpMultiplier = page.props.userClassExpMultiplier;

// Add computed properties for remaining stats
const remainingHealth = computed(() => userStats.current_health);
const remainingEnergy = computed(() => userStats.current_energy);

const newHabit = ref('');
const newDaily = ref('');
const newTodo = ref('');

const completeTask = (task) => {
  const taskHealthPenalty = userStats.max_health * 0.1 * task.difficulty.health_penalty;
  const taskEnergyPenalty = userStats.max_energy * 0.1 * task.difficulty.energy_cost;
  
  if(remainingHealth.value <= 0 || remainingHealth.value <= taskHealthPenalty) {
    errorMessage.value = trans('You do not have enough health to complete this task.');
    showErrorModal.value = true;
    return;
  }
  if(remainingEnergy.value <= 0 || remainingEnergy.value <= taskEnergyPenalty) {
    errorMessage.value = trans('You do not have enough energy to complete this task.');
    showErrorModal.value = true;
    return;
  }
  task.is_completed = !task.is_completed;
  router.post(`/tasks/${task.id}/complete`, {
    is_completed: task.is_completed,
  }, {
    onSuccess: () => {
      // Update local stats after successful completion
      userStats.current_health = remainingHealth.value - taskHealthPenalty;
      userStats.current_energy = remainingEnergy.value - taskEnergyPenalty;
    }
  });
};

const taskNotCompleted = (task) => {
    task.is_completed = !task.is_completed;
    router.post(`/tasks/${task.id}/not-completed`, {
      is_completed: !task.is_completed,
    }, {
      onSuccess: () => {
        // Update local stats after successful uncompletion
        const taskHealthPenalty = userStats.max_health * 0.1 * task.difficulty.health_penalty;
        const taskEnergyPenalty = userStats.max_energy * 0.1 * task.difficulty.energy_cost;
        userStats.current_health = remainingHealth.value + taskHealthPenalty;
        userStats.current_energy = remainingEnergy.value + taskEnergyPenalty;
      }
    });
};

const form = ref({
    title: '',
    description: '',
    reset_frequency: 1,
    is_positive: true,
    tags: '',
    start_date: new Date().toISOString().slice(0, 10),
    difficulty_level: 2,
    is_completed: false,
    is_deadline_task: false,
    experience_reward: DEFAULT_EXPERIENCE_REWARD,
    type: 'habit',
  });

const addTask = (type) => {
  if (type === 'habit' && newHabit.value.trim()) {
    const formData = {
      ...form.value,
      title: newHabit.value.trim(),
      tags: form.value.tags.split(',').map(t => t.trim()).filter(Boolean),
      experience_reward: DEFAULT_EXPERIENCE_REWARD,
    };
    
    router.post('/tasks', formData, {
      onSuccess: () => {
        newHabit.value = '';
      }
    });
  }
};

const getTaskExperience = (task) => {
  return Math.round(task.experience_reward * task.difficulty.exp_multiplier * userClassExpMultiplier);
};

const profileImageUrl = computed(() => '/images/default-profile.png'); // Replace with real avatar if available
</script>

<template>
  <div class="min-h-screen bg-white flex flex-col">
    <Header :showPlayerPanel="true" />
    <ErrorModal 
      :show="showErrorModal"
      :message="errorMessage"
      @close="showErrorModal = false"
    />
    <main class="flex-1 w-full mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 lg:gap-12 py-6 md:py-8 lg:py-12 px-4 md:px-6 lg:px-8">
      <!-- Habits -->
      <section class="bg-stone-100 rounded-xl shadow-lg p-4 md:p-6 lg:p-8 flex flex-col min-h-[300px] md:min-h-[400px] lg:min-h-[500px]">
        <h2 class="text-lg md:text-xl font-bold text-stone-800 border-b-2 border-stone-600 pb-2 mb-3">{{ trans('Habits') }}</h2>
        <div class="flex mb-3 gap-2">
          <input v-model="newHabit" @keyup.enter="addTask('habit')" type="text" :placeholder="trans('Add a Habit')" class="flex-1 px-2 md:px-3 py-1 rounded border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm md:text-base" />
          <button @click="showCreateHabitModal = true" class="bg-stone-600 text-stone-50 px-2 md:px-3 py-1 rounded font-bold shadow hover:bg-stone-700 text-sm md:text-base">{{ trans('+') }}</button>
          <CreateHabitModal
            :show="showCreateHabitModal"
            :difficulties="difficulties"
            :resetConfigs="resetConfigs"
            @close="showCreateHabitModal = false"
          />
        </div>
        <ul class="flex-1 space-y-2 overflow-y-auto pr-1">  
          <li v-for="task in habits" :key="task.id" class="bg-white rounded-lg shadow">
            <div class="flex items-stretch gap-2 ">
              <div class="flex items-center justify-center px-2 md:px-3 py-2 rounded-l-lg"
              :class="{'bg-stone-600': !task.difficulty}" :style="task.difficulty ? { backgroundColor: task.difficulty.color || '#57534e' } : {}">
                <button @click="completeTask(task)" 
                  class="w-4 h-4 md:w-6 md:h-6 flex items-center justify-center text-white font-bold text-lg hover:scale-110 transition-all duration-200 ease-in-out">
                  +
                </button>
              </div>
              <div class="flex-1 py-2">
                <div class="flex flex-wrap items-center gap-1 md:gap-2">
                  <div class="font-semibold text-stone-800 text-sm md:text-base">{{ task.title }}</div>
                  <span v-if="task.difficulty" class="ml-1 md:ml-2 px-1.5 md:px-2 py-0.5 rounded text-xs font-bold" :style="{ backgroundColor: task.difficulty.color || '#57534e', color: '#ffffff' }">
                    <span v-if="task.difficulty.icon" v-html="task.difficulty.icon" class="mr-1"></span>{{ task.difficulty.name }}
                  </span>
                  <span v-if="task.progress !== undefined" class="ml-1 md:ml-2 text-xs text-stone-600 font-bold">{{ trans('Streak') }}: {{ task.progress }}</span>
                </div>
                <div v-if="task.description" class="text-xs text-stone-600 mt-1">{{ task.description }}</div>
                <div v-if="task.tags && task.tags.length" class="flex flex-wrap gap-1 mt-1">
                  <span v-for="tag in task.tags" :key="tag.id" class="bg-stone-100 text-stone-700 px-1.5 md:px-2 py-0.5 rounded text-xs font-semibold">#{{ tag.name }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-2 mt-1">
                  <span v-if="task.experience_reward" class="text-xs font-bold text-stone-600 bg-amber-100 px-2 py-0.5 rounded flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4 text-amber-400 mr-1" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    +{{ getTaskExperience(task) }} {{ trans('XP') }}
                  </span>
                  <span v-if="task.difficulty" class="text-xs font-bold text-stone-600 bg-red-100 px-2 py-0.5 rounded flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4 text-red-500 mr-1" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    -{{ Math.round(userStats.max_health * 0.1 * task.difficulty.health_penalty) }} {{ trans('HP') }}
                  </span>
                  <span v-if="task.difficulty" class="text-xs font-bold text-stone-600 bg-blue-100 px-2 py-0.5 rounded flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4 text-blue-500 mr-1" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    -{{ Math.round(userStats.max_energy * 0.1 * task.difficulty.energy_cost) }} {{ trans('EP') }}
                  </span>
                </div>
              </div>
              <div class="flex items-center justify-center px-2 md:px-3 py-2 rounded-r-lg"
              :class="{'bg-stone-600': !task.difficulty}" :style="task.difficulty ? { backgroundColor: task.difficulty.color || '#57534e' } : {}">
                <button @click="taskNotCompleted(task)"
                  class="w-4 h-4 md:w-6 md:h-6 flex items-center justify-center text-white font-bold text-lg hover:scale-110 transition-all duration-200 ease-in-out">
                  -
                </button>
              </div>
            </div>
          </li>
        </ul>
      </section>
      <!-- Dailies -->
      <section class="bg-stone-100 rounded-xl shadow-lg p-4 md:p-6 lg:p-8 flex flex-col min-h-[300px] md:min-h-[400px] lg:min-h-[500px]">
        <h2 class="text-lg md:text-xl font-bold text-stone-800 border-b-2 border-stone-600 pb-2 mb-3">{{ trans('Dailies') }}</h2>
        <div class="flex mb-3 gap-2">
          <input v-model="newDaily" @keyup.enter="addTask('daily')" type="text" :placeholder="trans('Add a Daily')" class="flex-1 px-2 md:px-3 py-1 rounded border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm md:text-base" />
          <button @click="addTask('daily')" class="bg-stone-600 text-stone-50 px-2 md:px-3 py-1 rounded font-bold shadow hover:bg-stone-700 text-sm md:text-base">+</button>
        </div>
        <ul class="flex-1 space-y-2 overflow-y-auto pr-1">
          <li v-for="task in dailies" :key="task.id" class="bg-white rounded-lg shadow">
            <div class="flex items-stretch gap-2 pr-2 md:pr-4">
              <div class="flex items-center justify-center px-2 md:px-3 py-2 rounded-l-lg"
              :class="{'bg-stone-600': !task.difficulty}" :style="task.difficulty ? { backgroundColor: task.difficulty.color || '#57534e' } : {}">
                <input type="checkbox" :checked="task.is_completed" @change="toggleTask(task)" 
                  class="w-4 h-4 md:w-6 md:h-6 rounded-md border-2 border-white cursor-pointer transition-all duration-200 ease-in-out
                  checked:bg-white checked:border-white focus:ring-2 focus:ring-offset-2 focus:ring-white focus:outline-none
                  hover:scale-110" 
                  :style="{ 
                    accentColor: task.difficulty ? task.difficulty.color : '#57534e',
                    backgroundColor: task.is_completed ? 'transparent' : 'transparent'
                  }" />
              </div>
              <div class="flex-1 py-2">
                <div class="flex flex-wrap items-center gap-1 md:gap-2">
                  <div class="font-semibold text-stone-800 text-sm md:text-base" :class="{ 'line-through text-stone-400': task.is_completed }">{{ task.title }}</div>
                  <span v-if="task.difficulty" class="ml-1 md:ml-2 px-1.5 md:px-2 py-0.5 rounded text-xs font-bold" :style="{ backgroundColor: task.difficulty.color || '#57534e', color: '#ffffff' }">
                    <span v-if="task.difficulty.icon" v-html="task.difficulty.icon" class="mr-1"></span>{{ task.difficulty.name }}
                  </span>
                  <span v-if="task.progress !== undefined" class="ml-1 md:ml-2 text-xs text-stone-600 font-bold">{{ trans('Streak') }}: {{ task.progress }}</span>
                  <span v-if="task.experience_reward" class="ml-1 md:ml-2 text-xs font-bold text-stone-600">+{{ getTaskExperience(task) }} {{ trans('XP') }}</span>
                  <span v-if="task.due_date" class="ml-1 md:ml-2 text-xs text-stone-600">{{ new Date(task.due_date).toLocaleDateString() }}</span>
                </div>
                <div v-if="task.description" class="text-xs text-stone-600">{{ task.description }}</div>
                <div v-if="task.tags && task.tags.length" class="flex flex-wrap gap-1 mt-1">
                  <span v-for="tag in task.tags" :key="tag.id" class="bg-stone-100 text-stone-700 px-1.5 md:px-2 py-0.5 rounded text-xs font-semibold">#{{ tag.name }}</span>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </section>
      <!-- To Do's -->
      <section class="bg-stone-100 rounded-xl shadow-lg p-4 md:p-6 lg:p-8 flex flex-col min-h-[300px] md:min-h-[400px] lg:min-h-[500px]">
        <h2 class="text-lg md:text-xl font-bold text-stone-800 border-b-2 border-stone-600 pb-2 mb-3">{{ trans("To Do's") }}</h2>
        <div class="flex mb-3 gap-2">
          <input v-model="newTodo" @keyup.enter="addTask('todo')" type="text" :placeholder="trans('Add a To Do')" class="flex-1 px-2 md:px-3 py-1 rounded border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm md:text-base" />
          <button @click="addTask('todo')" class="bg-stone-600 text-stone-50 px-2 md:px-3 py-1 rounded font-bold shadow hover:bg-stone-700 text-sm md:text-base">+</button>
        </div>
        <ul class="flex-1 space-y-2 overflow-y-auto pr-1">
          <li v-for="task in todos" :key="task.id" class="bg-white rounded-lg shadow">
            <div class="flex items-stretch gap-2 pr-2 md:pr-4">
              <div class="flex items-center justify-center px-2 md:px-3 py-2 rounded-l-lg"
              :class="{'bg-stone-600': !task.difficulty}" :style="task.difficulty ? { backgroundColor: task.difficulty.color || '#57534e' } : {}">
                <input type="checkbox" :checked="task.is_completed" @change="toggleTask(task)" 
                  class="w-4 h-4 md:w-6 md:h-6 rounded-md border-2 border-white cursor-pointer transition-all duration-200 ease-in-out
                  checked:bg-white checked:border-white focus:ring-2 focus:ring-offset-2 focus:ring-white focus:outline-none
                  hover:scale-110" 
                  :style="{ 
                    accentColor: task.difficulty ? task.difficulty.color : '#57534e',
                    backgroundColor: task.is_completed ? 'transparent' : 'transparent'
                  }" />
              </div>
              <div class="flex-1 py-2">
                <div class="flex flex-wrap items-center gap-1 md:gap-2">
                  <div class="font-semibold text-stone-800 text-sm md:text-base" :class="{ 'line-through text-stone-400': task.is_completed }">{{ task.title }}</div>
                  <span v-if="task.difficulty" class="ml-1 md:ml-2 px-1.5 md:px-2 py-0.5 rounded text-xs font-bold text-center" :style="{ backgroundColor: task.difficulty.color || '#57534e', color: '#ffffff' }">
                    <span v-if="task.difficulty.icon" v-html="task.difficulty.icon" class="mr-1"></span>{{ task.difficulty.name }}
                  </span>
                </div>
                <div class="flex flex-wrap items-center gap-1 md:gap-2 mt-1">
                  <span v-if="task.experience_reward" class="text-xs font-bold text-stone-600">+{{ getTaskExperience(task) }} XP </span>
                  <span v-if="task.due_date" class="text-xs text-stone-600">{{ new Date(task.due_date).toLocaleDateString() }}</span>
                </div>
                <div v-if="task.description" class="text-xs text-stone-600 mt-1">{{ task.description }}</div>
                <div v-if="task.tags && task.tags.length" class="flex flex-wrap gap-1 mt-1">
                  <span v-for="tag in task.tags" :key="tag.id" class="bg-stone-100 text-stone-700 px-1.5 md:px-2 py-0.5 rounded text-xs font-semibold">#{{ tag.name }}</span>
                </div>
                <ul v-if="task.checklist_items && task.checklist_items.length" class="mt-2 ml-2 pl-2 border-l-2" :style="{ borderLeftColor: task.difficulty ? task.difficulty.color : '#57534e' }">
                  <li v-for="(item, idx) in task.checklist_items" :key="idx" class="flex items-center gap-2 text-xs text-stone-600 mb-1">
                    <input type="checkbox" :checked="item.completed" disabled class="w-3 h-3 md:w-4 md:h-4 rounded" :style="{ accentColor: task.difficulty ? task.difficulty.color : '#57534e' }" />
                    <span :class="{ 'line-through text-stone-400': item.completed }">{{ item.text || item }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </li>
        </ul>
      </section>
    </main>
    <Footer />
  </div>
</template>

<style>
body {
  background: #ffffff;
}
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
