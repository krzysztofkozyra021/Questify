<script setup>
import { ref, computed, watch } from 'vue';
import { useTranslation } from '@/Composables/useTranslation';
import CreateHabitModal from '@/Components/Forms/CreateHabitModal.vue';
import EditHabitModal from '@/Components/Forms/EditHabitModal.vue';
import { router } from '@inertiajs/vue3';
import ErrorModal from '@/Components/ErrorModal.vue';

const { trans } = useTranslation();

const DEFAULT_EXPERIENCE_REWARD = 5;

const props = defineProps({
  userStats: {
    type: Object,
    required: true
  },
  userClassExpMultiplier: {
    type: Number,
    required: true
  },
  difficulties: {
    type: Array,
    required: true
  },
  resetConfigs: {
    type: Array,
    required: true
  },
  habits: {
    type: Array,
    required: true
  }
});

const showCreateHabitModal = ref(false);
const showEditHabitModal = ref(false);
const selectedTask = ref(null);
const newHabit = ref('');
const searchQuery = ref('');
const isSearchMode = ref(false);
const tempInputValue = ref('');
const showErrorModal = ref(false);
const errorMessage = ref('');
const habits = ref([...props.habits]);
const tempHabits = ref([...props.habits]);
const showWeakHabits = ref(false);
const showStrongHabits = ref(false);
const remainingHealth = computed(() => props.userStats.current_health);
const remainingEnergy = computed(() => props.userStats.current_energy);

const habitsResultFromSearchMode = computed(() => {
  let filtered = habits.value;
  // Apply search filter if in search mode
  if (isSearchMode.value && searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(habit => 
      habit.title.toLowerCase().includes(query) || 
      habit.description?.toLowerCase().includes(query) ||
      habit.tags?.some(tag => tag.name.toLowerCase().includes(query))
    );
  }
  return filtered;
});

const filterWeakHabits = computed(() => {
  showStrongHabits.value = false;
  showWeakHabits.value = !showWeakHabits.value;
  if (showWeakHabits.value) {
    habits.value = tempHabits.value;
    habits.value = habits.value.filter(habit =>
      habit.not_completed_count > habit.completed_count || habit.not_completed_count === habit.completed_count
    );
  } else {
    habits.value = tempHabits.value;
  }
});

const filterStrongHabits = computed(() => {
  showWeakHabits.value = false;
  showStrongHabits.value = !showStrongHabits.value;
  if (showStrongHabits.value) {
    habits.value = tempHabits.value;
    habits.value = habits.value.filter(habit =>
      habit.not_completed_count < habit.completed_count
    );
  } else {
    habits.value = tempHabits.value;
  }
});

watch(() => props.habits, (newHabits) => {
  habits.value = [...newHabits];
  tempHabits.value = [...newHabits];
}, { deep: true });

// Methods
const showAllHabits = () => {
  showWeakHabits.value = false;
  showStrongHabits.value = false;
  habits.value = tempHabits.value;
};

const toggleSearchMode = () => {
  if (isSearchMode.value) {
    tempInputValue.value = searchQuery.value;
  } else {
    tempInputValue.value = newHabit.value;
  }
  
  isSearchMode.value = !isSearchMode.value;
  
  if (isSearchMode.value) {
    searchQuery.value = tempInputValue.value;
  } else {
    newHabit.value = tempInputValue.value;
  }
};

const calculateTaskHealthPenalty = (task) => {
  return Math.round(props.userStats.max_health * 0.1 * task.difficulty.health_penalty);
}

const calculateTaskEnergyPenalty = (task) => {
  return Math.round(props.userStats.max_energy * 0.1 * task.difficulty.energy_cost);
}
const getTaskExperience = (task) => {
  return Math.round(task.experience_reward * task.difficulty.exp_multiplier * props.userClassExpMultiplier);
};

const addTask = (type) => {
  if (type === 'habit' && newHabit.value.trim()) {
    const formData = {
      title: newHabit.value.trim(),
      description: '',
      reset_frequency: 1,
      is_positive: true,
      tags: [],
      start_date: new Date().toISOString().slice(0, 10),
      difficulty_level: 2,
      is_completed: false,
      is_deadline_task: false,
      experience_reward: DEFAULT_EXPERIENCE_REWARD,
      type: 'habit',
    };
    
    router.post('/tasks/store/habit', formData, {
      onSuccess: () => {
        newHabit.value = '';
      },
      onError: (errors) => {
        errorMessage.value = errors.message || trans('Failed to create habit');
        showErrorModal.value = true;
      }
    });
  }
};

const completeTask = (task) => {
  const taskHealthPenalty = calculateTaskHealthPenalty(task);
  const taskEnergyPenalty = calculateTaskEnergyPenalty(task);
  
  if(remainingHealth.value <= 0 || remainingHealth.value < taskHealthPenalty) {
    errorMessage.value = trans('You do not have enough health to complete this task.');
    showErrorModal.value = true;
    return;
  }
  if(remainingEnergy.value <= 0 || remainingEnergy.value < taskEnergyPenalty) {
    errorMessage.value = trans('You do not have enough energy to complete this task.');
    showErrorModal.value = true;
    return;
  }

  task.is_completed = !task.is_completed;
  router.post(`/tasks/${task.id}/complete`, {
    is_completed: task.is_completed,
  }, {
    preserveScroll: true,
    preserveState: true,
    only: ['userStatistics', 'habits'],
    onSuccess: () => {}
  });
};

const taskNotCompleted = (task) => {
    task.is_completed = !task.is_completed;
    router.post(`/tasks/${task.id}/not-completed`, {
      is_completed: !task.is_completed,
    }, {
      preserveScroll: true,
      preserveState: true,
      only: ['userStatistics', 'habits'],
      onSuccess: () => {}
    });
};

const openEditHabitModal = (task) => {
  selectedTask.value = task;
  showEditHabitModal.value = true;
};

</script>

<template>
  <section class="bg-stone-100 rounded-xl shadow-lg p-2 sm:p-4 md:p-6 lg:p-8 flex flex-col min-h-[200px] sm:min-h-[300px] md:min-h-[400px] lg:min-h-[500px]">
    <ErrorModal 
      :show="showErrorModal"
      :message="errorMessage"
      @close="showErrorModal = false"
    />
    <div class="flex flex-col sm:flex-row items-start sm:items-center mb-3 sm:mb-4">
      <h2 class="text-base sm:text-lg md:text-xl font-bold text-stone-800 border-b-2 border-stone-600 pb-2">{{ trans('Habits') }}</h2>
      <div class="flex flex-row gap-2 sm:gap-3 mt-2 sm:mt-0 sm:ml-auto">
        <button @click="showAllHabits" 
          class="text-xs sm:text-sm font-medium text-stone-600 hover:text-amber-600 transition-colors duration-200">
          {{ trans('All') }}
        </button>
        <button @click="filterWeakHabits" 
          :class="{ 'text-amber-600 border-b-2 border-amber-600': showWeakHabits }" 
          class="text-xs sm:text-sm font-medium text-stone-600 hover:text-amber-600 transition-colors duration-200">
          {{ trans('Weak') }}
        </button>
        <button @click="filterStrongHabits" 
          :class="{ 'text-amber-600 border-b-2 border-amber-600': showStrongHabits }" 
          class="text-xs sm:text-sm font-medium text-stone-600 hover:text-amber-600 transition-colors duration-200">
          {{ trans('Strong') }}
        </button>
      </div>
    </div>
    <div class="flex flex-col xl:flex-row xl:items-center mb-2 sm:mb-3 gap-2">
      <div class="flex-1">
        <input 
          v-if="!isSearchMode"
          v-model="newHabit" 
          @keyup.enter="addTask('habit')" 
          type="text" 
          :placeholder="trans('Add a Habit')" 
          class="w-full px-2 sm:px-3 py-1.5 rounded border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm sm:text-base" 
        />
        <input 
          v-else
          v-model="searchQuery" 
          type="text" 
          :placeholder="trans('Search habits...')" 
          class="w-full px-2 sm:px-3 py-1.5 rounded border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm sm:text-base" 
        />
      </div>
      <div class="flex justify-center xl:justify-end gap-2 sm:gap-3 xl:ml-4">
        <button 
          v-if="!isSearchMode"
          @click="showCreateHabitModal = true" 
          class="bg-stone-600 text-stone-50 px-2 sm:px-3 py-1.5 sm:py-2 rounded font-bold shadow hover:bg-stone-700 text-sm sm:text-base min-w-[32px] sm:min-w-[40px]"
        >
          {{ trans('+') }}
        </button>
        <button 
          v-else
          @click="toggleSearchMode" 
          class="bg-stone-600 text-stone-50 px-2 sm:px-3 py-1.5 sm:py-2 rounded font-bold shadow hover:bg-stone-700 text-sm sm:text-base min-w-[32px] sm:min-w-[40px]"
        >
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-4 w-4 sm:h-5 sm:w-5" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              d="M6 18L18 6M6 6l12 12" 
            />
          </svg>
        </button>
        <button 
          v-if="!isSearchMode"
          @click="toggleSearchMode" 
          class="bg-stone-600 text-stone-50 px-2 sm:px-3 py-1.5 sm:py-2 rounded font-bold shadow hover:bg-stone-700 text-sm sm:text-base min-w-[32px] sm:min-w-[40px]"
        >
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-4 w-4 sm:h-5 sm:w-5" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" 
            />
          </svg>
        </button>
      </div>
    </div>
    <CreateHabitModal
      :show="showCreateHabitModal"
      :difficulties="difficulties"
      :resetConfigs="resetConfigs"
      @close="showCreateHabitModal = false"
    />
    <EditHabitModal
      v-if="selectedTask"
      :show="showEditHabitModal"
      :task="selectedTask"
      :difficulties="difficulties"
      :resetConfigs="resetConfigs"
      @close="showEditHabitModal = false; selectedTask = null"
      @edited="showEditHabitModal = false; selectedTask = null"
    />
    <ul class="flex-1 space-y-2 overflow-y-auto pr-1">  
      <li v-for="task in habitsResultFromSearchMode" :key="task.id" class="bg-white rounded-lg shadow">
        <div class="flex items-stretch gap-1 sm:gap-2">
          <div class="flex items-center justify-center px-1 sm:px-2 py-1.5 sm:py-2 rounded-l-lg"
          :class="{'bg-lime-500' : task.completed_count > task.not_completed_count,
            'bg-amber-500' : task.completed_count === task.not_completed_count,
            'bg-red-500' : task.completed_count < task.not_completed_count
          }">
            <button @click="completeTask(task)" 
              class="w-4 h-4 sm:w-5 sm:h-5 flex items-center justify-center text-white font-bold text-sm sm:text-base hover:scale-110 transition-all duration-200 ease-in-out">
              +
            </button>
          </div>
          <div class="flex-1 py-1.5 sm:py-2 px-2 sm:px-3 cursor-pointer" @click="openEditHabitModal(task)">
            <div class="flex flex-wrap items-center gap-1 sm:gap-2">
              <div class="font-semibold text-stone-800 text-sm sm:text-base">{{ task.title }}</div>
            </div>
            <div v-if="task.description" class="text-xs sm:text-sm text-stone-600 mt-1">{{ task.description }}</div>
            <div v-if="task.tags && task.tags.length" class="flex flex-wrap gap-1 mt-1">
              <span v-for="tag in task.tags" :key="tag.id" class="bg-stone-100 text-stone-700 px-1.5 py-0.5 rounded text-xs font-semibold">#{{ tag.name }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-1 sm:gap-2 mt-1">
              <span v-if="task.completed_count !== undefined" class="text-xs sm:text-sm text-stone-600 font-bold"> +{{ task.completed_count }} | -{{ task.not_completed_count }}</span>
              <div class="flex-1"></div>
              <span v-if="task.experience_reward" class="text-xs sm:text-sm font-bold text-stone-600 bg-amber-100 px-1.5 sm:px-2 py-0.5 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 text-amber-400 mr-1" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                +{{ getTaskExperience(task) }} {{ trans('XP') }}
              </span>
              <span v-if="task.difficulty && calculateTaskHealthPenalty(task) > 0" class="text-xs sm:text-sm font-bold text-stone-600 bg-red-100 px-1.5 sm:px-2 py-0.5 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 text-red-500 mr-1" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                -{{ calculateTaskHealthPenalty(task) }} {{ trans('HP') }}
              </span>
              <span v-if="task.difficulty && calculateTaskEnergyPenalty(task) > 0" class="text-xs sm:text-sm font-bold text-stone-600 bg-blue-100 px-1.5 sm:px-2 py-0.5 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 text-blue-500 mr-1" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                -{{ calculateTaskEnergyPenalty(task) }} {{ trans('EP') }}
              </span>
              <span v-if="task.difficulty" class="ml-1 sm:ml-2 px-1.5 sm:px-2 py-0.5 rounded text-xs sm:text-sm font-bold" :style="{ backgroundColor: task.difficulty.color || '#57534e', color: '#ffffff' }">
                <span v-if="task.difficulty.icon" v-html="task.difficulty.icon" class="mr-1"></span>{{ trans(task.difficulty.name) }}
              </span>
            </div>
          </div>
          <div class="flex items-center justify-center px-1 sm:px-2 py-1.5 sm:py-2 rounded-r-lg"
          :class="{'bg-lime-500' : task.completed_count > task.not_completed_count,
            'bg-amber-500' : task.completed_count === task.not_completed_count,
            'bg-red-500' : task.completed_count < task.not_completed_count
          }">
            <button @click="taskNotCompleted(task)"
              class="w-4 h-4 sm:w-5 sm:h-5 flex items-center justify-center text-white font-bold text-sm sm:text-base hover:scale-110 transition-all duration-200 ease-in-out">
              -
            </button>
          </div>
        </div>
      </li>
    </ul>
  </section>
</template> 