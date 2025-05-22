<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useTranslation } from '@/Composables/useTranslation';
import { router } from '@inertiajs/vue3';
import ErrorModal from '@/Components/ErrorModal.vue';

const { trans } = useTranslation();
const page = usePage();

const DEFAULT_EXPERIENCE_REWARD = 5;

// Props
const props = defineProps({
  userStats: {
    type: Object,
    required: true
  },
  userClassExpMultiplier: {
    type: Number,
    required: true
  }
});

// State
const newDaily = ref('');
const showErrorModal = ref(false);
const errorMessage = ref('');

// Computed
const dailies = computed(() => page.props.tasks?.dailies || []);

// Methods
const addTask = () => {
  if (newDaily.value.trim()) {
    const formData = {
      title: newDaily.value.trim(),
      description: '',
      reset_frequency: 1,
      is_positive: true,
      tags: [],
      start_date: new Date().toISOString().slice(0, 10),
      difficulty_level: 2,
      is_completed: false,
      is_deadline_task: false,
      experience_reward: DEFAULT_EXPERIENCE_REWARD,
      type: 'daily',
    };
    
    router.post('/tasks', formData, {
      onSuccess: () => {
        newDaily.value = '';
      },
      onError: (errors) => {
        errorMessage.value = errors.message || trans('Failed to create daily task');
        showErrorModal.value = true;
      }
    });
  }
};

const toggleTask = (task) => {
  const taskHealthPenalty = props.userStats.max_health * 0.1 * task.difficulty.health_penalty;
  const taskEnergyPenalty = props.userStats.max_energy * 0.1 * task.difficulty.energy_cost;
  
  if(props.userStats.current_health <= 0 || props.userStats.current_health <= taskHealthPenalty) {
    errorMessage.value = trans('Not enough health to complete this task');
    showErrorModal.value = true;
    return;
  }
  if(props.userStats.current_energy <= 0 || props.userStats.current_energy <= taskEnergyPenalty) {
    errorMessage.value = trans('Not enough energy to complete this task');
    showErrorModal.value = true;
    return;
  }
  
  task.is_completed = !task.is_completed;
  router.post(`/tasks/${task.id}/complete`, {
    is_completed: task.is_completed,
  }, {
    onSuccess: () => {
      props.userStats.current_health -= taskHealthPenalty;
      props.userStats.current_energy -= taskEnergyPenalty;
    },
    onError: (errors) => {
      errorMessage.value = errors.message || trans('Failed to complete task');
      showErrorModal.value = true;
    }
  });
};

const getTaskExperience = (task) => {
  return Math.round(task.experience_reward * task.difficulty.exp_multiplier * props.userClassExpMultiplier);
};
</script>

<template>
  <section class="bg-stone-100 rounded-xl shadow-lg p-4 md:p-6 lg:p-8 flex flex-col min-h-[300px] md:min-h-[400px] lg:min-h-[500px]">
    <ErrorModal 
      :show="showErrorModal"
      :message="errorMessage"
      @close="showErrorModal = false"
    />
    <h2 class="text-lg md:text-xl font-bold text-stone-800 border-b-2 border-stone-600 pb-2 mb-3">{{ trans('Dailies') }}</h2>
    <div class="flex mb-3 gap-2">
      <input 
        v-model="newDaily" 
        @keyup.enter="addTask" 
        type="text" 
        :placeholder="trans('Add a Daily')" 
        class="flex-1 px-2 md:px-3 py-1 rounded border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm md:text-base" 
      />
      <button 
        @click="addTask" 
        class="bg-stone-600 text-stone-50 px-2 md:px-3 py-1 rounded font-bold shadow hover:bg-stone-700 text-sm md:text-base"
      >
        +
      </button>
    </div>
    <ul class="flex-1 space-y-2 overflow-y-auto pr-1">
      <li v-for="task in dailies" :key="task.id" class="bg-white rounded-lg shadow">
        <div class="flex items-stretch gap-2 pr-2 md:pr-4">
          <div class="flex items-center justify-center px-2 md:px-3 py-2 rounded-l-lg"
          :class="{'bg-stone-600': !task.difficulty}" :style="task.difficulty ? { backgroundColor: task.difficulty.color || '#57534e' } : {}">
            <input 
              type="checkbox" 
              :checked="task.is_completed" 
              @change="toggleTask(task)" 
              class="w-4 h-4 md:w-6 md:h-6 rounded-md border-2 border-white cursor-pointer transition-all duration-200 ease-in-out
              checked:bg-white checked:border-white focus:ring-2 focus:ring-offset-2 focus:ring-white focus:outline-none
              hover:scale-110" 
              :style="{ 
                accentColor: task.difficulty ? task.difficulty.color : '#57534e',
                backgroundColor: task.is_completed ? 'transparent' : 'transparent'
              }" 
            />
          </div>
          <div class="flex-1 py-2">
            <div class="flex flex-wrap items-center gap-1 md:gap-2">
              <div 
                class="font-semibold text-stone-800 text-sm md:text-base" 
                :class="{ 'line-through text-stone-400': task.is_completed }"
              >
                {{ task.title }}
              </div>
              <span 
                v-if="task.difficulty" 
                class="ml-1 md:ml-2 px-1.5 md:px-2 py-0.5 rounded text-xs font-bold" 
                :style="{ backgroundColor: task.difficulty.color || '#57534e', color: '#ffffff' }"
              >
                <span v-if="task.difficulty.icon" v-html="task.difficulty.icon" class="mr-1"></span>
                {{ task.difficulty.name }}
              </span>
              <span 
                v-if="task.progress !== undefined" 
                class="ml-1 md:ml-2 text-xs text-stone-600 font-bold"
              >
                {{ trans('Streak') }}: {{ task.progress }}
              </span>
              <span 
                v-if="task.experience_reward" 
                class="ml-1 md:ml-2 text-xs font-bold text-stone-600"
              >
                +{{ getTaskExperience(task) }} {{ trans('XP') }}
              </span>
              <span 
                v-if="task.due_date" 
                class="ml-1 md:ml-2 text-xs text-stone-600"
              >
                {{ new Date(task.due_date).toLocaleDateString() }}
              </span>
            </div>
            <div v-if="task.description" class="text-xs text-stone-600">{{ task.description }}</div>
            <div v-if="task.tags && task.tags.length" class="flex flex-wrap gap-1 mt-1">
              <span 
                v-for="tag in task.tags" 
                :key="tag.id" 
                class="bg-stone-100 text-stone-700 px-1.5 md:px-2 py-0.5 rounded text-xs font-semibold"
              >
                #{{ tag.name }}
              </span>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </section>
</template> 