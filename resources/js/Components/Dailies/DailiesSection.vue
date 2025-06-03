<script setup>
import { ref, computed, watch } from 'vue';
import { useTranslation } from '@/Composables/useTranslation';
import { router } from '@inertiajs/vue3';
import ErrorModal from '@/Components/ErrorModal.vue';
import CreateDailyModal from '@/Components/Forms/CreateDailyModal.vue';
import { useNotification } from '@/Composables/useNotification';
import EditDailyModal from '@/Components/Forms/EditDailyModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { syncDashboardData } from '@/Composables/syncDashboardData';

const { trans } = useTranslation();
const { addNotification } = useNotification();

const DEFAULT_DAILY_EXPERIENCE_REWARD = 3;

// Props
const props = defineProps({
  userStats: {
    type: Object,
    required: true
  },
  userClassExpMultiplier: {
    type: Number,
    required: true
  },
  dailyTasks: {
    type: Object,
    required: true
  },
  difficulties: {
    type: Array,
    required: true
  },
  resetConfigs: {
    type: Array,
    required: true
  }
});

// State
const newDaily = ref('');
const showErrorModal = ref(false);
const errorMessage = ref('');
const showCreateDailyModal = ref(false);
const showEditDailyModal = ref(false);
const selectedDaily = ref(null);
const showDeleteConfirmationModal = ref(false);
const dailyToDelete = ref(null);
const activeDropdown = ref(null);
const searchQuery = ref('');
const isSearchMode = ref(false);
const tempInputValue = ref('');
const remainingEnergy = computed(() => props.userStats.current_energy);
const remainingHealth = computed(() => props.userStats.current_health);
const localDailies = ref([]);
const checkboxStates = ref({});

// Watch for changes in todoTasks prop
watch(() => props.dailyTasks, (newDailies) => {
  if (newDailies && typeof newDailies === 'object') {
    const dailiesArray = Object.values(newDailies);
    localDailies.value = dailiesArray;
    dailiesArray.forEach(daily => {
      if (checkboxStates.value[daily.id] === undefined) {
        checkboxStates.value[daily.id] = daily.is_completed;
      }
    });
  }
}, { deep: true, immediate: true });

// Computed
const dailiesResultFromSearchMode = computed(() => {
  let filtered = localDailies.value;
  // Apply search filter if in search mode
  if (isSearchMode.value && searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(todo => 
      todo.title.toLowerCase().includes(query) || 
      todo.description?.toLowerCase().includes(query) ||
      todo.tags?.some(tag => tag.name.toLowerCase().includes(query))
    );
  }
  return filtered;
});

const toggleSearchMode = () => {
  if (isSearchMode.value) {
    tempInputValue.value = searchQuery.value;
  } else {
    tempInputValue.value = newDaily.value;
  }
  
  isSearchMode.value = !isSearchMode.value;
  
  if (isSearchMode.value) {
    searchQuery.value = tempInputValue.value;
  } else {
    newDaily.value = tempInputValue.value;
  }
};

// Methods
const addDaily = () => {
  if (newDaily.value.trim()) {   
    const formData = {
      title: newDaily.value.trim(),
      description: '',
      difficulty_level: 2,
      reset_frequency: 1,
      start_date: new Date().toISOString().slice(0, 10),
      experience_reward: DEFAULT_DAILY_EXPERIENCE_REWARD,
      weekly_schedule: [],
      tags: [],
      is_completed: false,
      type: 'daily',
    };
    router.post('/tasks/dailies/store', formData, {
      preserveScroll: true,
      preserveState: true,
      only: ['userStatistics', 'dailyTasks'],
      onSuccess: () => {
        newDaily.value = '';
        addNotification(trans('Daily task created successfully'), 'success');
        syncDailies();
      },
      onError: (errors) => {
        errorMessage.value = errors.message || trans('Failed to create daily task');
        showErrorModal.value = true;
        addNotification(trans('Failed to create daily task'), 'error');
      }
    });
  }
};


const completeDaily = (daily) => {
  if(remainingEnergy.value < getDailyEnergyPenalty(daily)) {
    errorMessage.value = trans('You do not have enough energy to complete this task.');
    showErrorModal.value = true;
    checkboxStates.value[daily.id] = daily.is_completed;
    return;
  }

  if(remainingHealth.value <= 0) {
    errorMessage.value = trans('You do not have enough health to complete this task.');
    showErrorModal.value = true;
    checkboxStates.value[daily.id] = daily.is_completed;
    return;
  }

  router.post(`/tasks/dailies/${daily.id}/complete`, {
    is_completed: checkboxStates.value[daily.id],
  }, {
    preserveScroll: true,
    preserveState: true,
    only: ['userStatistics', 'dailyTasks'],
    onSuccess: () => {
      addNotification('+ ' + daily.experience_reward + ' ' + trans('XP'), 'exp');
      addNotification('- ' + getDailyEnergyPenalty(daily) + ' ' + trans('EP'), 'energy');
      syncDailies();
    },
    onError: () => {
      addNotification(trans('Failed to complete daily'), 'error');
      checkboxStates.value[daily.id] = daily.is_completed;
    }
  });
};

const uncompleteDaily = (daily) => {
  router.post(`/tasks/dailies/${daily.id}/not-completed`, {}, {
    preserveScroll: true,
    preserveState: true,
    only: ['userStatistics', 'dailyTasks'],
    onSuccess: () => {
      addNotification('- ' + daily.experience_reward + ' ' + trans('XP'), 'exp');
      addNotification('+ ' + getDailyEnergyPenalty(daily) + ' ' + trans('EP'), 'energy');
      checkboxStates.value[daily.id] = false;
      syncDailies();
    },
    onError: () => {
      addNotification(trans('Failed to uncomplete daily'), 'error');
      checkboxStates.value[daily.id] = true;
    }
  });
};

const getDailyEnergyPenalty = (daily) => {
  const playerMaxEnergy = props.userStats.max_energy;

  return Math.round(playerMaxEnergy * 0.1 * daily.difficulty.energy_cost);
};

const openEditDailyModal = (daily) => {
  selectedDaily.value = daily;
  showEditDailyModal.value = true;
};

// Close dropdown when clicking outside
const closeDropdowns = () => {
  activeDropdown.value = null;
};

// Add click event listener to close dropdowns when clicking outside
if (typeof window !== 'undefined') {
  window.addEventListener('click', closeDropdowns);
}

const toggleDropdown = (dailyId, event) => {
  event.stopPropagation();
  activeDropdown.value = activeDropdown.value === dailyId ? null : dailyId;
};

const deleteDaily = (daily) => {
  dailyToDelete.value = daily;
  showDeleteConfirmationModal.value = true;
};

const confirmDelete = () => {
  if (dailyToDelete.value) {
    router.delete(`/tasks/${dailyToDelete.value.id}`, {
      preserveScroll: true,
      preserveState: true,
      only: ['userStatistics', 'dailyTasks'],
      onSuccess: () => {
        activeDropdown.value = null;
        dailyToDelete.value = null;
        addNotification(trans('Daily deleted successfully'), 'success');
        showDeleteConfirmationModal.value = false;
        syncDailies();
      },
      onError: (errors) => {
        errorMessage.value = errors.message || trans('Failed to delete daily');
        showErrorModal.value = true;
      }
    });
  }
};

const syncDailies = () => {
  syncDashboardData((newData) => {
    if (newData.tasks?.dailies) {
      const dailiesArray = Object.values(newData.tasks.dailies);
      localDailies.value = dailiesArray;
      // Update checkbox states to match server state
      dailiesArray.forEach(daily => {
        checkboxStates.value[daily.id] = daily.is_completed;
      });
    }
  });
};

const cancelDelete = () => {
  dailyToDelete.value = null;
  showDeleteConfirmationModal.value = false;
};

</script>

<template>
  <section class="bg-stone-100 rounded-xl shadow-lg p-2 sm:p-4 md:p-6 lg:p-8 flex flex-col min-h-[200px] sm:min-h-[300px] md:min-h-[400px] lg:min-h-[500px]">
    <CreateDailyModal
      :show="showCreateDailyModal"
      @close="showCreateDailyModal = false"
      @created="showCreateDailyModal = false; newDaily = ''; syncDailies()"
      :difficulties="difficulties"
      :default-daily-experience-reward="DEFAULT_DAILY_EXPERIENCE_REWARD"
      :reset-configs="resetConfigs"
    />
    <EditDailyModal
      v-if="selectedDaily"
      :show="showEditDailyModal"
      :daily="selectedDaily"
      :difficulties="difficulties"
      :reset-configs="resetConfigs"
      @close="showEditDailyModal = false; selectedDaily = null"
      @edited="showEditDailyModal = false; selectedDaily = null; syncDailies()"
    />
    <ErrorModal 
      :show="showErrorModal"
      :message="errorMessage"
      @close="showErrorModal = false"
    />
    <ConfirmationModal
      :show="showDeleteConfirmationModal"
      :title="trans('Delete Daily')"
      :message="trans('Are you sure you want to delete this daily?')"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
    <h2 class="text-base sm:text-lg md:text-xl font-bold text-stone-800 border-b-2 border-stone-600 pb-2 mb-3 break-words">{{ trans("Dailies") }}</h2>
    <div class="flex flex-col xl:flex-row xl:items-center mb-2 sm:mb-3 gap-2">
      <div class="flex-1">
        <input 
          v-if="!isSearchMode"
          v-model="newDaily" 
          @keyup.enter.prevent="addDaily()" 
          type="text" 
          :placeholder="trans('Add a Daily')" 
          class="w-full px-2 sm:px-3 py-1.5 rounded border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm sm:text-base" 
        />
        <input 
          v-else
          v-model="searchQuery" 
          type="text" 
          :placeholder="trans('Search dailies...')" 
          class="w-full px-2 sm:px-3 py-1.5 rounded border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm sm:text-base" 
        />
      </div>
      <div class="flex justify-center xl:justify-end gap-2 sm:gap-3 xl:ml-4">
        <button 
          v-if="!isSearchMode"
          @click="showCreateDailyModal = true" 
          class="bg-stone-600 text-stone-50 px-2 sm:px-3 py-1.5 sm:py-2 rounded font-bold shadow hover:bg-stone-700 text-sm sm:text-base min-w-[32px] sm:min-w-[40px]"
        >
          +
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
    <ul class="flex-1 space-y-2 overflow-y-auto pr-1">
      <li v-for="daily in dailiesResultFromSearchMode" :key="daily.id" class="bg-white rounded-lg shadow">
        <div class="flex items-stretch gap-2 pr-2 md:pr-4">
          <div class="flex items-center justify-center px-2 md:px-3 py-2 rounded-l-lg flex-shrink-0"
          :style="{ backgroundColor: daily.is_completed ? '#57534e' : (daily.difficulty ? daily.difficulty.color : '#57534e') }">
            <input 
              :id="daily.id"
              type="checkbox" 
              v-model="checkboxStates[daily.id]"
              @change="daily.is_completed ? uncompleteDaily(daily) : completeDaily(daily)" 
              class="w-4 h-4 md:w-6 md:h-6 rounded-md border-2 border-white cursor-pointer transition-all duration-200 ease-in-out
              checked:bg-white checked:border-white focus:ring-2 focus:ring-offset-2 focus:ring-white focus:outline-none
              hover:scale-110" 
              :style="{ 
                accentColor: daily.difficulty ? daily.difficulty.color : '#57534e',
                backgroundColor: daily.is_completed ? 'transparent' : 'transparent'
              }" 
            />
          </div>
          <div class="flex-1 py-2 cursor-pointer hover:bg-stone-50 transition-colors duration-200 min-w-0" @click="openEditDailyModal(daily)">
            <div class="flex flex-wrap items-center gap-1 md:gap-2">
              <div 
                class="font-semibold text-stone-800 text-sm md:text-base break-words overflow-hidden" 
                :class="{ 'line-through text-stone-400': daily.is_completed }"
              >
                {{ daily.title }}
              </div>
              <span 
                v-if="daily.difficulty" 
                class="ml-1 md:ml-2 px-1.5 md:px-2 py-0.5 rounded text-xs font-bold text-center whitespace-nowrap flex-shrink-0" 
                :style="{ backgroundColor: daily.difficulty.color || '#57534e', color: '#ffffff' }"
              >
                <span v-if="daily.difficulty.icon" v-html="daily.difficulty.icon" class="mr-1"></span>
                {{ trans(daily.difficulty.name) }}
              </span>
            </div>
            <div class="flex flex-wrap items-center gap-1 md:gap-2 mt-1">
              <span v-if="daily.experience_reward && !daily.is_completed" class="text-xs font-bold text-stone-600 bg-amber-100 px-1.5 py-0.5 rounded flex items-center whitespace-nowrap flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-amber-400 mr-1 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                +{{ daily.experience_reward }} {{ trans('XP') }}
              </span>
              <span v-if="daily.difficulty && getDailyEnergyPenalty(daily) > 0 && !daily.is_completed" class="text-xs font-bold text-stone-600 bg-blue-100 px-1.5 py-0.5 rounded flex items-center whitespace-nowrap flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-blue-500 mr-1 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                -{{ getDailyEnergyPenalty(daily) }} {{ trans('EP') }}
              </span>
            </div>
            <div v-if="daily.description" class="text-xs text-stone-600 mt-1 break-words overflow-hidden">{{ daily.description }}</div>
            <div v-if="daily.tags && daily.tags.length" class="flex flex-wrap gap-1 mt-1">
              <span 
                v-for="tag in daily.tags" 
                :key="tag.id" 
                class="bg-stone-100 text-stone-700 px-1.5 md:px-2 py-0.5 rounded text-xs font-semibold break-words"
              >
                #{{ tag.name }}
              </span>
            </div>
            <ul 
              v-if="daily.checklist_items && daily.checklist_items.length" 
              class="mt-2 ml-2 pl-2 border-l-2" 
              :style="{ borderLeftColor: daily.difficulty ? daily.difficulty.color : '#57534e' }"
            >
              <li 
                v-for="(item, idx) in daily.checklist_items" 
                :key="idx" 
                class="flex items-center gap-2 text-xs text-stone-600 mb-1"
              >
                <input 
                  type="checkbox" 
                  :checked="item.completed" 
                  disabled 
                  class="w-3 h-3 md:w-4 md:h-4 rounded flex-shrink-0" 
                  :style="{ accentColor: daily.difficulty ? daily.difficulty.color : '#57534e' }" 
                />
                <span :class="{ 'line-through text-stone-400': item.completed }" class="break-words overflow-hidden">
                  {{ item.text || item }}
                </span>
              </li>
            </ul>
          </div>
          <div class="relative flex items-center px-2 flex-shrink-0">
            <button 
              @click="toggleDropdown(daily.id, $event)"
              class="p-1 rounded hover:bg-stone-100 transition-colors duration-200"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
              </svg>
            </button>
            <div 
              v-if="activeDropdown === daily.id"
              class="absolute right-0 top-8 z-10 bg-white rounded-lg shadow-lg border border-stone-200 py-1 min-w-[120px]"
              @click.stop
            >
              <button 
                @click="openEditDailyModal(daily)"
                class="w-full text-left px-3 py-2 hover:bg-stone-100 transition-colors duration-200 text-sm flex items-center gap-2"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                {{ trans('Edit') }}
              </button>
              <button 
                @click="deleteDaily(daily)"
                class="w-full text-left px-3 py-2 hover:bg-stone-100 transition-colors duration-200 text-sm flex items-center gap-2 text-red-600"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                {{ trans('Delete') }}
              </button>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </section>
</template> 