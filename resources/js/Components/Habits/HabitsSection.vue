<script setup>
import { ref, computed, watch } from 'vue';
import { useTranslation } from '@/Composables/useTranslation';
import CreateHabitModal from '@/Components/Forms/CreateHabitModal.vue';
import EditHabitModal from '@/Components/Forms/EditHabitModal.vue';
import { router } from '@inertiajs/vue3';
import ErrorModal from '@/Components/ErrorModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { useNotification } from '@/Composables/useNotification';
import { syncDashboardData } from '@/Composables/syncDashboardData';


const { trans } = useTranslation();
const { addNotification } = useNotification();

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
    type: [Object, Array],
    required: true
  }
});

const showCreateHabitModal = ref(false);
const showEditHabitModal = ref(false);
const selectedHabit = ref(null);
const newHabit = ref('');
const searchQuery = ref('');
const isSearchMode = ref(false);
const tempInputValue = ref('');
const showErrorModal = ref(false);
const errorMessage = ref('');
const showDeleteConfirmationModal = ref(false);
const habitToDelete = ref(null);
const habits = ref(Array.isArray(props.habits) ? [...props.habits] : Object.values(props.habits));
const tempHabits = ref(Array.isArray(props.habits) ? [...props.habits] : Object.values(props.habits));
const showWeakHabits = ref(false);
const showStrongHabits = ref(false);
const remainingHealth = computed(() => props.userStats.current_health);
const remainingEnergy = computed(() => props.userStats.current_energy);
const activeDropdown = ref(null);

// Close dropdown when clicking outside
const closeDropdowns = () => {
  activeDropdown.value = null;
};

// Add click event listener to close dropdowns when clicking outside
if (typeof window !== 'undefined') {
  window.addEventListener('click', closeDropdowns);
}

const toggleDropdown = (habitId, event) => {
  event.stopPropagation();
  activeDropdown.value = activeDropdown.value === habitId ? null : habitId;
};

const habitsFiltered = computed(() => {
  let filtered = habits.value;

  if(showWeakHabits.value) {
    showStrongHabits.value = false;
    filtered = filtered.filter(habit =>
      habit.not_completed_count > habit.completed_count || habit.not_completed_count === habit.completed_count
    );
  } else if(showStrongHabits.value) {
    showWeakHabits.value = false;
    filtered = filtered.filter(habit =>
      habit.not_completed_count < habit.completed_count
    );
  }


  if (isSearchMode.value && searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(habit => 
      habit.title.toLowerCase().includes(query) || 
      habit.description?.toLowerCase().includes(query)
    );
  }

  
  return filtered;
});

watch(() => props.habits, (newHabits) => {
  habits.value = Array.isArray(newHabits) ? [...newHabits] : Object.values(newHabits);
  tempHabits.value = Array.isArray(newHabits) ? [...newHabits] : Object.values(newHabits);
}, { deep: true });

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

const calculateHabitHealthPenalty = (habit) => {
  return Math.round(props.userStats.max_health * 0.1 * habit.difficulty.health_penalty);
}

const calculateHabitEnergyPenalty = (habit) => {
  return Math.round(props.userStats.max_energy * 0.1 * habit.difficulty.energy_cost);
}

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
    
    router.post('/tasks/habits/store', formData, {
      preserveScroll: true,
      preserveState: true,
      only: ['userStatistics', 'habits'],
      onSuccess: () => {
        newHabit.value = '';
        addNotification(trans('Habit created successfully'), 'success');
      },
      onError: (errors) => {
        errorMessage.value = errors.message || trans('Failed to create habit');
        showErrorModal.value = true;
      }
    });
  }
};

const completeHabit = (habit) => {
  const habitHealthPenalty = calculateHabitHealthPenalty(habit);
  const habitEnergyPenalty = calculateHabitEnergyPenalty(habit);
  
  if(remainingHealth.value <= 0 || remainingHealth.value < habitHealthPenalty) {
    errorMessage.value = trans('You do not have enough health to complete this task.');
    showErrorModal.value = true;
    return;
  }
  if(remainingEnergy.value <= 0 || remainingEnergy.value < habitEnergyPenalty) {
    errorMessage.value = trans('You do not have enough energy to complete this task.');
    showErrorModal.value = true;
    return;
  }

  habit.is_completed = !habit.is_completed;
  router.post(`/tasks/habits/${habit.id}/complete`, {
    is_completed: habit.is_completed,
  }, {
    preserveScroll: true,
    preserveState: true,
    only: ['userStatistics', 'habits'],
    onSuccess: () => {
      addNotification('+ ' + habit.experience_reward + ' ' + trans('XP'), 'exp');
      addNotification(trans('- ') + calculateHabitEnergyPenalty(habit).toString() + ' ' + trans('EP'), 'energy');
      // Trigger immediate sync after completion
      syncDashboardData((newData) => {
        if (newData.tasks?.habits) {
          habits.value = Object.values(newData.tasks.habits);
        }
      });
    },
    onError: () => {
      addNotification(trans('Failed to complete habit'), 'error');
    }
  });
};

const habitNotCompleted = (habit) => {

  if(remainingHealth.value <= 0 || remainingHealth.value < calculateHabitHealthPenalty(habit)) {
    errorMessage.value = trans('You do not have enough health to complete this task.');
    showErrorModal.value = true;
    return;
  }

  router.post(`/tasks/habits/${habit.id}/not-completed`, {}, {
    preserveScroll: true,
    preserveState: true,
    only: ['userStatistics', 'habits'],
    onSuccess: () => {
      if(calculateHabitHealthPenalty(habit) > 0) {
        addNotification(trans('- ') + calculateHabitHealthPenalty(habit).toString() + ' ' + trans('HP'), 'health');
      }
      addNotification(trans("Habit streak updated"), 'info');
      // Trigger immediate sync after not completed
      syncDashboardData((newData) => {
        if (newData.tasks?.habits) {
          habits.value = Object.values(newData.tasks.habits);
        }
      });
    },
    onError: () => {
      addNotification(trans('Failed to update habit'), 'error');
    }
  });
};

const openEditHabitModal = (habit) => {
  selectedHabit.value = habit;
  showEditHabitModal.value = true;
  activeDropdown.value = null;
};

const deleteHabit = (habit) => {
  habitToDelete.value = habit;
  showDeleteConfirmationModal.value = true;
};

const confirmDelete = () => {
  if (habitToDelete.value) {
    router.delete(`/tasks/${habitToDelete.value.id}`, {
      preserveScroll: true,
      preserveState: true,
      only: ['userStatistics', 'habits'],
      onSuccess: () => {
        activeDropdown.value = null;
        habitToDelete.value = null;
        addNotification(trans('Habit deleted successfully'), 'success');
        showDeleteConfirmationModal.value = false;
      },
      onError: (errors) => {
        errorMessage.value = errors.message || trans('Failed to delete habit');
        showErrorModal.value = true;
      }
    });
  }
};

const cancelDelete = () => {
  habitToDelete.value = null;
  showDeleteConfirmationModal.value = false;
};

</script>

<template>
  <section class="bg-stone-100 rounded-xl shadow-lg p-2 sm:p-4 md:p-6 lg:p-8 flex flex-col min-h-[200px] sm:min-h-[300px] md:min-h-[400px] lg:min-h-[500px]">
    <ErrorModal 
      :show="showErrorModal"
      :message="errorMessage"
      @close="showErrorModal = false"
    />
    <ConfirmationModal
      :show="showDeleteConfirmationModal"
      :title="trans('Delete Habit')"
      :message="trans('Are you sure you want to delete this habit?')"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
    <div class="flex flex-col sm:flex-row items-start sm:items-center mb-3 sm:mb-4">
      <h2 class="text-base sm:text-lg md:text-xl font-bold text-stone-800 border-b-2 border-stone-600 pb-2 break-words">{{ trans('Habits') }}</h2>
      <div class="flex flex-row gap-2 sm:gap-3 mt-2 sm:mt-0 sm:ml-auto">
        <button @click="showAllHabits" 
          class="text-xs sm:text-sm font-medium text-stone-600 hover:text-amber-600 transition-colors duration-200 whitespace-nowrap">
          {{ trans('All') }}
        </button>
        <button @click="showWeakHabits = !showWeakHabits; showStrongHabits = false" 
          :class="{ 'text-amber-600 border-b-2 border-amber-600': showWeakHabits }" 
          class="text-xs sm:text-sm font-medium text-stone-600 hover:text-amber-600 transition-colors duration-200 whitespace-nowrap">
          {{ trans('Weak') }}
        </button>
        <button @click="showStrongHabits = !showStrongHabits; showWeakHabits = false" 
          :class="{ 'text-amber-600 border-b-2 border-amber-600': showStrongHabits }" 
          class="text-xs sm:text-sm font-medium text-stone-600 hover:text-amber-600 transition-colors duration-200 whitespace-nowrap">
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
      v-if="selectedHabit"
      :show="showEditHabitModal"
      :habit="selectedHabit"
      :difficulties="difficulties"
      :resetConfigs="resetConfigs"
      @close="showEditHabitModal = false; selectedHabit = null"
      @edited="showEditHabitModal = false; selectedHabit = null"
    />
    <ul class="flex-1 space-y-2 overflow-y-auto pr-1">  
      <li v-for="habit in habitsFiltered" :key="habit.id" class="bg-white rounded-lg shadow">
        <div class="flex items-stretch gap-2">
          <div class="flex items-center justify-center px-2 md:px-3 py-2 rounded-l-lg flex-shrink-0"
          :class="{'bg-lime-500' : habit.completed_count > habit.not_completed_count,
            'bg-amber-500' : habit.completed_count === habit.not_completed_count,
            'bg-red-500' : habit.completed_count < habit.not_completed_count
          }">
            <button @click="completeHabit(habit)" 
              class="w-4 h-4 md:w-6 md:h-6 flex items-center justify-center text-white font-bold text-sm md:text-base hover:scale-110 transition-all duration-200 ease-in-out">
              +
            </button>
          </div>
          <div class="flex-1 py-2 cursor-pointer hover:bg-stone-50 transition-colors duration-200 min-w-0" @click="openEditHabitModal(habit)">
            <div class="flex flex-wrap items-center gap-1 md:gap-2">
              <div class="font-semibold text-stone-800 text-sm md:text-base break-words overflow-hidden">{{ habit.title }}</div>
              <span v-if="habit.difficulty" class="ml-1 md:ml-2 px-1.5 md:px-2 py-0.5 rounded text-xs font-bold text-center whitespace-nowrap flex-shrink-0" 
                :style="{ backgroundColor: habit.difficulty.color || '#57534e', color: '#ffffff' }">
                <span v-if="habit.difficulty.icon" v-html="habit.difficulty.icon" class="mr-1"></span>
                {{ trans(habit.difficulty.name) }}
              </span>
            </div>
            <div class="flex flex-wrap items-center gap-1 md:gap-2 mt-2">
              <span v-if="habit.completed_count !== undefined" class="text-xs font-bold text-stone-600 whitespace-nowrap flex-shrink-0">
                +{{ habit.completed_count }} | -{{ habit.not_completed_count }}
              </span>
              <span v-if="habit.experience_reward" class="text-xs font-bold text-stone-600 bg-amber-100 px-1.5 py-0.5 rounded flex items-center whitespace-nowrap flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-amber-400 mr-1 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                +{{ habit.experience_reward }} {{ trans('XP') }}
              </span>
              <span v-if="habit.difficulty && calculateHabitHealthPenalty(habit) > 0" class="text-xs font-bold text-stone-600 bg-red-100 px-1.5 py-0.5 rounded flex items-center whitespace-nowrap flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-red-500 mr-1 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                -{{ calculateHabitHealthPenalty(habit) }} {{ trans('HP') }}
              </span>
              <span v-if="habit.difficulty && calculateHabitEnergyPenalty(habit) > 0" class="text-xs font-bold text-stone-600 bg-blue-100 px-1.5 py-0.5 rounded flex items-center whitespace-nowrap flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-blue-500 mr-1 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                -{{ calculateHabitEnergyPenalty(habit) }} {{ trans('EP') }}
              </span>
            </div>
            <div class="mt-2">
              <div v-if="habit.description" class="text-xs text-stone-600 break-words overflow-hidden">{{ habit.description }}</div>
              <div v-if="habit.tags && habit.tags.length" class="flex flex-wrap gap-1 mt-1">
                <span v-for="tag in habit.tags" :key="tag.id" class="bg-stone-100 text-stone-700 px-1.5 md:px-2 py-0.5 rounded text-xs font-semibold break-words">
                  #{{ tag.name }}
                </span>
              </div>
            </div>
          </div>
          <div class="relative flex items-center px-2 flex-shrink-0">
            <button 
              @click="toggleDropdown(habit.id, $event)"
              class="p-1 rounded hover:bg-stone-100 transition-colors duration-200"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
              </svg>
            </button>
            <div 
              v-if="activeDropdown === habit.id"
              class="absolute right-0 top-8 z-10 bg-white rounded-lg shadow-lg border border-stone-200 py-1 min-w-[120px]"
              @click.stop
            >
              <button 
                @click="openEditHabitModal(habit)"
                class="w-full text-left px-3 py-2 hover:bg-stone-100 transition-colors duration-200 text-sm flex items-center gap-2"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                {{ trans('Edit') }}
              </button>
              <button 
                @click="deleteHabit(habit)"
                class="w-full text-left px-3 py-2 hover:bg-stone-100 transition-colors duration-200 text-sm flex items-center gap-2 text-red-600"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                {{ trans('Delete') }}
              </button>
            </div>
          </div>
          <div class="flex items-center justify-center px-2 md:px-3 py-2 rounded-r-lg flex-shrink-0"
          :class="{'bg-lime-500' : habit.completed_count > habit.not_completed_count,
            'bg-amber-500' : habit.completed_count === habit.not_completed_count,
            'bg-red-500' : habit.completed_count < habit.not_completed_count
          }">
            <button @click="habitNotCompleted(habit)"
              class="w-4 h-4 md:w-6 md:h-6 flex items-center justify-center text-white font-bold text-sm md:text-base hover:scale-110 transition-all duration-200 ease-in-out">
              -
            </button>
          </div>
        </div>
      </li>
    </ul>
  </section>
</template>