<script setup>
import { ref, computed } from 'vue';
import { useTranslation } from '@/Composables/useTranslation';
import { router } from '@inertiajs/vue3';
import ErrorModal from '@/Components/ErrorModal.vue';
import CreateTodoModal from '@/Components/Forms/CreateTodoModal.vue';
import { useNotification } from '@/Composables/useNotification';

const { trans } = useTranslation();
const { addNotification } = useNotification();

const DEFAULT_TODO_EXPERIENCE_REWARD = 3;

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
  todoTasks: {
    type: Object,
    required: true
  },
  difficulties: {
    type: Array,
    required: true
  }
});

// State
const newTodo = ref('');
const showErrorModal = ref(false);
const errorMessage = ref('');
const showCreateTodoModal = ref(false);



// Computed
const todos = computed(() => Object.values(props.todoTasks));

const daysPassedAfterTodoDueDate = (todo) => {
  if (!todo || !todo.due_date) return 0;
  const today = new Date();
  const dueDate = new Date(todo.due_date);
  
  // Set both dates to midnight for accurate day calculation
  today.setHours(0, 0, 0, 0);
  dueDate.setHours(0, 0, 0, 0);
  
  const timeDiff = today.getTime() - dueDate.getTime();
  const daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
  
  if (daysDiff === 0) return 'today';
  return daysDiff;
};

// Methods
const addTodo = () => {
  if (newTodo.value.trim()) {   
    const formData = {
      title: newTodo.value.trim(),
      description: '',
      difficulty_level: 2,
      due_date: new Date().toISOString().slice(0, 10),
      start_date: new Date().toISOString().slice(0, 10),
      experience_reward: DEFAULT_TODO_EXPERIENCE_REWARD,
      tags: [],
      is_completed: false,
      type: 'todo',
    };
    router.post('/tasks/todos/store', formData, {
      onSuccess: () => {
        newTodo.value = '';
        addNotification(trans('Todo task created successfully'), 'success');
      },
      onError: (errors) => {
        errorMessage.value = errors.message || trans('Failed to create todo task');
        showErrorModal.value = true;
        addNotification(trans('Failed to create todo task'), 'error');
      }
    });
  }
};

const completeTodo = (todo) => {
  router.post(`/tasks/todos/${todo.id}/complete`, {
    is_completed: todo.is_completed,
  }, {
    onSuccess: () => {
      addNotification('+ ' + getTodoExperience(todo) + ' ' + trans('XP'), 'info');
    },
    onError: (errors) => {
      addNotification(trans('Failed to complete todo'), 'error');
    }
  });
};


const getTodoExperience = (todo) => {
  return Math.round(todo.experience_reward * todo.difficulty.exp_multiplier * props.userClassExpMultiplier);
};
</script>

<template>
  <section class="bg-stone-100 rounded-xl shadow-lg p-4 md:p-6 lg:p-8 flex flex-col min-h-[300px] md:min-h-[400px] lg:min-h-[500px]">
    <CreateTodoModal
      :show="showCreateTodoModal"
      @close="showCreateTodoModal = false"
      :difficulties="difficulties"
      :default-todo-experience-reward="DEFAULT_TODO_EXPERIENCE_REWARD"
    />
    <ErrorModal 
      :show="showErrorModal"
      :message="errorMessage"
      @close="showErrorModal = false"
    />
    <h2 class="text-lg md:text-xl font-bold text-stone-800 border-b-2 border-stone-600 pb-2 mb-3">{{ trans("To Do's") }}</h2>
    <div class="flex mb-3 gap-2">
      <input 
        v-model="newTodo" 
        @keyup.enter.prevent="addTodo()" 
        type="text" 
        :placeholder="trans('Add a To Do')" 
          class="w-full px-2 sm:px-3 py-1.5 rounded border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm sm:text-base" 
      />
      <button 
        @click="showCreateTodoModal = true" 
        class="bg-stone-600 text-stone-50 px-2 sm:px-3 py-1.5 sm:py-2 rounded font-bold shadow hover:bg-stone-700 text-sm sm:text-base min-w-[32px] sm:min-w-[40px]"
      >
        +
      </button>
    </div>
    <ul class="flex-1 space-y-2 overflow-y-auto pr-1">
      <li v-for="todo in todos" :key="todo.id" class="bg-white rounded-lg shadow">
        <div v-if="!todo.is_completed">
        <div class="flex items-stretch gap-2 pr-2 md:pr-4">
          <div class="flex items-center justify-center px-2 md:px-3 py-2 rounded-l-lg"
          :class="{'bg-stone-600': !todo.difficulty}" :style="todo.difficulty ? { backgroundColor: todo.difficulty.color || '#57534e' } : {}">
            <input 
              type="checkbox" 
              :checked="todo.is_completed" 
              @change="completeTodo(todo)" 
              class="w-4 h-4 md:w-6 md:h-6 rounded-md border-2 border-white cursor-pointer transition-all duration-200 ease-in-out
              checked:bg-white checked:border-white focus:ring-2 focus:ring-offset-2 focus:ring-white focus:outline-none
              hover:scale-110" 
              :style="{ 
                accentColor: todo.difficulty ? todo.difficulty.color : '#57534e',
                backgroundColor: todo.is_completed ? 'transparent' : 'transparent'
              }" 
            />
          </div>
          <div class="flex-1 py-2">
            <div class="flex flex-wrap items-center gap-1 md:gap-2">
              <div 
                class="font-semibold text-stone-800 text-sm md:text-base" 
                :class="{ 'line-through text-stone-400': todo.is_completed }"
              >
                {{ todo.title }}
              </div>
              <span 
                v-if="todo.difficulty" 
                class="ml-1 md:ml-2 px-1.5 md:px-2 py-0.5 rounded text-xs font-bold text-center" 
                :style="{ backgroundColor: todo.difficulty.color || '#57534e', color: '#ffffff' }"
              >
                <span v-if="todo.difficulty.icon" v-html="todo.difficulty.icon" class="mr-1"></span>
                {{ trans(todo.difficulty.name) }}
              </span>
            </div>
            <div class="flex flex-wrap items-center gap-1 md:gap-2 mt-1">
              <span v-if="todo.experience_reward" class="text-xs font-bold text-stone-600 bg-amber-100 px-1.5 py-0.5 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-amber-400 mr-1" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                +{{ getTodoExperience(todo) }} {{ trans('XP') }}
              </span>
              <span 
                v-if="todo.due_date" 
                class="text-xs text-stone-600 font-bold"
              >
                <span :class="{ 'line-through': typeof daysPassedAfterTodoDueDate(todo) === 'number' && daysPassedAfterTodoDueDate(todo) > 0 }">
                  {{ new Date(todo.due_date).toLocaleDateString() }}
                </span>
                <span 
                  v-if="daysPassedAfterTodoDueDate(todo) === 'today'" 
                  class="text-xs font-bold text-green-600 ml-1"
                >
                  ( {{ trans('due today') }} )
                </span>
                <span 
                  v-else-if="typeof daysPassedAfterTodoDueDate(todo) === 'number' && daysPassedAfterTodoDueDate(todo) > 0" 
                  class="text-xs font-bold text-red-500 ml-1"
                >
                  {{ trans('days late') }} ( {{ daysPassedAfterTodoDueDate(todo) }} ) 
                </span>
              </span>
            </div>
            <div v-if="todo.description" class="text-xs text-stone-600 mt-1">{{ todo.description }}</div>
            <div v-if="todo.tags && todo.tags.length" class="flex flex-wrap gap-1 mt-1">
              <span 
                v-for="tag in todo.tags" 
                :key="tag.id" 
                class="bg-stone-100 text-stone-700 px-1.5 md:px-2 py-0.5 rounded text-xs font-semibold"
              >
                #{{ tag.name }}
              </span>
            </div>
            <ul 
              v-if="todo.checklist_items && todo.checklist_items.length" 
              class="mt-2 ml-2 pl-2 border-l-2" 
              :style="{ borderLeftColor: todo.difficulty ? todo.difficulty.color : '#57534e' }"
            >
              <li 
                v-for="(item, idx) in todo.checklist_items" 
                :key="idx" 
                class="flex items-center gap-2 text-xs text-stone-600 mb-1"
              >
                <input 
                  type="checkbox" 
                  :checked="item.completed" 
                  disabled 
                  class="w-3 h-3 md:w-4 md:h-4 rounded" 
                  :style="{ accentColor: todo.difficulty ? todo.difficulty.color : '#57534e' }" 
                />
                <span :class="{ 'line-through text-stone-400': item.completed }">
                  {{ item.text || item }}
                </span>
              </li>
            </ul>
          </div>
        </div>
        </div>
      </li>
    </ul>
  </section>
</template> 