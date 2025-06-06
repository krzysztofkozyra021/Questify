<script setup>
import { ref, computed, watch } from 'vue'
import { useTranslation } from '@/Composables/useTranslation'
import { router } from '@inertiajs/vue3'
import ErrorModal from '@/Components/ErrorModal.vue'
import CreateTodoModal from '@/Components/Forms/CreateTodoModal.vue'
import { useNotification } from '@/Composables/useNotification'
import EditTodoModal from '@/Components/Forms/EditTodoModal.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import { syncDashboardData } from '@/Composables/syncDashboardData'

const { trans } = useTranslation()
const { addNotification } = useNotification()

// Props
const props = defineProps({
  userStats: {
    type: Object,
    required: true,
  },
  userClassExpMultiplier: {
    type: Number,
    required: true,
  },
  todoTasks: {
    type: Object,
    required: true,
  },
  difficulties: {
    type: Array,
    required: true,
  },
})

// State
const newTodo = ref('')
const showErrorModal = ref(false)
const errorMessage = ref('')
const showCreateTodoModal = ref(false)
const showEditTodoModal = ref(false)
const showCompletedTodos = ref(false)
const selectedTodo = ref(null)
const showDeleteConfirmationModal = ref(false)
const todoToDelete = ref(null)
const activeDropdown = ref(null)
const searchQuery = ref('')
const isSearchMode = ref(false)
const tempInputValue = ref('')
const remainingHealth = computed(() => props.userStats.current_health)
const localTodos = ref([])
const checkboxStates = ref({})

// Watch for changes in todoTasks prop
watch(() => props.todoTasks, (newTodos) => {
  if (newTodos && typeof newTodos === 'object') {
    const todosArray = Object.values(newTodos)
    localTodos.value = todosArray
    todosArray.forEach(todo => {
      if (checkboxStates.value[todo.id] === undefined) {
        checkboxStates.value[todo.id] = todo.is_completed
      }
    })
  }
}, { deep: true, immediate: true })

// Computed
const todosResult = computed(() => {
  let filtered = localTodos.value
  
  // Apply completed filter
  if (showCompletedTodos.value) {
    filtered = filtered.filter(todo => todo.is_completed)
  } else {
    filtered = filtered.filter(todo => !todo.is_completed)
  }

  // Apply search filter if in search mode
  if (isSearchMode.value && searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(todo => 
      todo.title.toLowerCase().includes(query) || 
      todo.description?.toLowerCase().includes(query) ||
      todo.tags?.some(tag => tag.name.toLowerCase().includes(query)),
    )
  }
  return filtered
})

const toggleSearchMode = () => {
  if (isSearchMode.value) {
    tempInputValue.value = searchQuery.value
  } else {
    tempInputValue.value = newTodo.value
  }
  
  isSearchMode.value = !isSearchMode.value
  
  if (isSearchMode.value) {
    searchQuery.value = tempInputValue.value
  } else {
    newTodo.value = tempInputValue.value
  }
}

// Methods
const addTodo = () => {
  if (newTodo.value.trim()) {   
    const formData = {
      title: newTodo.value.trim(),
      description: '',
      difficulty_level: 2,
      overdue_days: 0,
      due_date: new Date().toISOString().slice(0, 10),
      start_date: new Date().toISOString().slice(0, 10),
      tags: [],
      is_completed: false,
      type: 'todo',
    }
    router.post('/tasks/todos/store', formData, {
      preserveScroll: true,
      preserveState: true,
      only: ['userStatistics', 'todoTasks'],
      onSuccess: () => {
        newTodo.value = ''
        addNotification(trans('Todo task created successfully'), 'success')
        syncTodos()
      },
      onError: (errors) => {
        errorMessage.value = errors.message || trans('Failed to create todo task')
        showErrorModal.value = true
        addNotification(trans('Failed to create todo task'), 'error')
      },
    })
  }
}

const completeTodo = (todo) => {
  if(remainingHealth.value < todo.overdue_days) {
    errorMessage.value = trans('You do not have enough health to complete this task.')
    showErrorModal.value = true
    checkboxStates.value[todo.id] = todo.is_completed
    return
  }

  router.post(`/tasks/todos/${todo.id}/complete`, {
    is_completed: checkboxStates.value[todo.id],
  }, {
    preserveScroll: true,
    preserveState: true,
    only: ['userStatistics', 'todoTasks'],
    onSuccess: () => {
      addNotification('+ ' + todo.experience_reward + ' ' + trans('XP'), 'exp')
      if (todo.overdue_days > 0) {
        addNotification('- ' + todo.overdue_days + ' ' + trans('HP'), 'health')
      }
      syncTodos()
    },
    onError: () => {
      addNotification(trans('Failed to complete todo'), 'error')
      checkboxStates.value[todo.id] = todo.is_completed
    },
  })
}

const uncompleteTodo = (todo) => {
  router.post(`/tasks/todos/${todo.id}/uncomplete`, {
    is_completed: checkboxStates.value[todo.id],
  }, {
    preserveScroll: true,
    preserveState: true,
    only: ['userStatistics', 'todoTasks'],
    onSuccess: () => {
      addNotification('-' + todo.experience_reward + ' ' + trans('XP'), 'exp')
      if (todo.overdue_days > 0) {
        addNotification('+' + todo.overdue_days + ' ' + trans('HP'), 'health')
      }
      syncTodos()
    },
    onError: () => {
      addNotification(trans('Failed to uncomplete todo'), 'error')
      checkboxStates.value[todo.id] = todo.is_completed
    },
  })
}

const filterCompletedTodos = () => {
  showCompletedTodos.value = !showCompletedTodos.value
}

const openEditTodoModal = (todo) => {
  selectedTodo.value = todo
  showEditTodoModal.value = true
}

// Close dropdown when clicking outside
const closeDropdowns = () => {
  activeDropdown.value = null
}

// Add click event listener to close dropdowns when clicking outside
if (typeof window !== 'undefined') {
  window.addEventListener('click', closeDropdowns)
}

const toggleDropdown = (todoId, event) => {
  event.stopPropagation()
  activeDropdown.value = activeDropdown.value === todoId ? null : todoId
}

const deleteTodo = (todo) => {
  todoToDelete.value = todo
  showDeleteConfirmationModal.value = true
}

const confirmDelete = () => {
  if (todoToDelete.value) {
    router.delete(`/tasks/${todoToDelete.value.id}`, {
      preserveScroll: true,
      preserveState: true,
      only: ['userStatistics', 'todoTasks'],
      onSuccess: () => {
        activeDropdown.value = null
        todoToDelete.value = null
        addNotification(trans('Todo deleted successfully'), 'success')
        showDeleteConfirmationModal.value = false
        syncTodos()
      },
      onError: (errors) => {
        errorMessage.value = errors.message || trans('Failed to delete todo')
        showErrorModal.value = true
      },
    })
  }
}

const syncTodos = () => {
  syncDashboardData((newData) => {
    if (newData.tasks?.todos) {
      localTodos.value = Object.values(newData.tasks.todos)
    }
  })
}

const cancelDelete = () => {
  todoToDelete.value = null
  showDeleteConfirmationModal.value = false
}

</script>

<template>
  <section class="bg-stone-100 rounded-xl shadow-lg p-2 sm:p-4 md:p-6 lg:p-8 flex flex-col min-h-[200px] sm:min-h-[300px] md:min-h-[400px] lg:min-h-[500px]">
    <CreateTodoModal
      :show="showCreateTodoModal"
      :difficulties="difficulties"
      @close="showCreateTodoModal = false"
      @created="showCreateTodoModal = false; newTodo = ''; syncTodos()"
    />
    <EditTodoModal
      v-if="selectedTodo"
      :show="showEditTodoModal"
      :todo="selectedTodo"
      :difficulties="difficulties"
      @close="showEditTodoModal = false; selectedTodo = null"
      @edited="showEditTodoModal = false; selectedTodo = null; syncTodos()"
    />
    <ErrorModal 
      :show="showErrorModal"
      :message="errorMessage"
      @close="showErrorModal = false"
    />
    <ConfirmationModal
      :show="showDeleteConfirmationModal"
      :title="trans('Delete Todo')"
      :message="trans('Are you sure you want to delete this todo?')"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
    <div class="flex flex-col sm:flex-row items-start sm:items-center mb-3 sm:mb-4">
      <h2 class="text-base sm:text-lg md:text-xl font-bold text-stone-800 border-b-2 border-stone-600 pb-2 break-words">{{ trans('To Do\'s') }}</h2>
      <div class="flex flex-row gap-2 sm:gap-3 mt-2 sm:mt-0 sm:ml-auto">
        <button :class="{ 'text-amber-600 border-b-2 border-amber-600': showCompletedTodos }" 
                class="text-xs sm:text-sm font-medium text-stone-600 hover:text-amber-600 transition-colors duration-200 whitespace-nowrap" 
                @click="filterCompletedTodos"
        >
          {{ trans('Completed') }}
        </button>
      </div>
    </div>
    <div class="flex flex-col xl:flex-row xl:items-center mb-2 sm:mb-3 gap-2">
      <div class="flex-1">
        <input 
          v-if="!isSearchMode"
          v-model="newTodo" 
          type="text" 
          :placeholder="trans('Add a To Do')" 
          class="w-full px-2 sm:px-3 py-1.5 rounded border border-stone-300 bg-white text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm sm:text-base" 
          @keyup.enter.prevent="addTodo()" 
        >
        <input 
          v-else
          v-model="searchQuery" 
          type="text" 
          :placeholder="trans('Search todos...')" 
          class="w-full px-2 sm:px-3 py-1.5 rounded border border-stone-300 bg-white text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm sm:text-base" 
        >
      </div>
      <div class="flex justify-center xl:justify-end gap-2 sm:gap-3 xl:ml-4">
        <button 
          v-if="!isSearchMode"
          class="bg-stone-600 text-stone-50 px-2 sm:px-3 py-1.5 sm:py-2 rounded font-bold shadow hover:bg-stone-700 text-sm sm:text-base min-w-[32px] sm:min-w-[40px]" 
          @click="showCreateTodoModal = true"
        >
          +
        </button>
        <button 
          v-else
          class="bg-stone-600 text-stone-50 px-2 sm:px-3 py-1.5 sm:py-2 rounded font-bold shadow hover:bg-stone-700 text-sm sm:text-base min-w-[32px] sm:min-w-[40px]" 
          @click="toggleSearchMode"
        >
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="size-4 sm:size-5" 
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
          class="bg-stone-600 text-stone-50 px-2 sm:px-3 py-1.5 sm:py-2 rounded font-bold shadow hover:bg-stone-700 text-sm sm:text-base min-w-[32px] sm:min-w-[40px]" 
          @click="toggleSearchMode"
        >
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="size-4 sm:size-5" 
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
      <li v-for="todo in todosResult" :key="todo.id" class="bg-white rounded-lg shadow">
        <div class="flex items-stretch gap-2 pr-2 md:pr-4">
          <div class="flex items-center justify-center p-2 md:px-3 rounded-l-lg shrink-0"
               :class="{'!bg-stone-600': !todo.difficulty || todo.is_completed} "
               :style="{ backgroundColor: todo.difficulty ? todo.difficulty.color : '#57534e' }"
          >
            <input 
              :id="todo.id"
              v-model="checkboxStates[todo.id]" 
              type="checkbox"
              class="size-4 md:size-6 rounded-md border-2 border-white cursor-pointer transition-all duration-200 ease-in-out checked:bg-white checked:border-white
              focus:ring-2 focus:ring-offset-2 focus:ring-white focus:outline-none hover:scale-110" 
              :style="{ 
                accentColor: todo.difficulty ? todo.difficulty.color : '#57534e',
                backgroundColor: todo.is_completed ? 'transparent' : 'transparent'
              }" 
              @change="todo.is_completed ? uncompleteTodo(todo) : completeTodo(todo)" 
            >
          </div>
          <div class="flex-1 py-2 cursor-pointer hover:bg-stone-50 transition-colors duration-200 min-w-0" @click="openEditTodoModal(todo)">
            <div class="flex flex-wrap items-center gap-1 md:gap-2">
              <div 
                class="font-semibold text-stone-800 text-sm md:text-base break-words overflow-hidden " 
                :class="{ 'line-through text-stone-400': todo.is_completed }"
              >
                {{ todo.title }}
              </div>
              <span 
                v-if="todo.difficulty" 
                class="ml-1 md:ml-2 px-1.5 md:px-2 py-0.5 rounded text-xs font-bold text-center whitespace-nowrap shrink-0" 
                :style="{ backgroundColor: todo.difficulty.color || '#57534e', color: '#ffffff' }"
              >
                <span v-if="todo.difficulty.icon" class="mr-1" v-html="todo.difficulty.icon" />
                {{ trans(todo.difficulty.name) }}
              </span>
            </div>
            <div class="flex flex-wrap items-center gap-1 md:gap-2 mt-1">
              <span v-if="todo.experience_reward" class="text-xs font-bold text-stone-600 bg-amber-100 px-1.5 py-0.5 rounded flex items-center whitespace-nowrap shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-3 text-amber-400 mr-1 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                +{{ todo.experience_reward }} {{ trans('XP') }}
              </span>
              <span v-if="todo.overdue_days > 0" class="text-xs font-bold text-stone-600 bg-red-100 px-1.5 py-0.5 rounded flex items-center whitespace-nowrap shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-3 text-red-500 mr-1 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                - {{ todo.overdue_days }} {{ trans('HP') }}
              </span>
              <span 
                v-if="todo.due_date && !todo.is_completed" 
                class="text-xs text-stone-600 font-bold whitespace-nowrap shrink-0"
              >
                <span :class="{ 'line-through': todo.overdue_days > 0}">
                  {{ new Date(todo.due_date).toLocaleDateString() }}
                </span>
                <span 
                  v-if="todo.overdue_days === 0 && new Date(todo.due_date).toDateString() === new Date().toDateString()" 
                  class="text-xs font-bold text-green-600 ml-1"
                >
                  ( {{ trans('due today') }} )
                </span>
                <span 
                  v-else-if="todo.overdue_days > 0" 
                  class="text-xs font-bold text-red-500 ml-1"
                >
                  {{ trans('days late') }} ( {{ todo.overdue_days }} ) 
                </span>
              </span>
            </div>
            <div v-if="todo.description" class="text-xs text-stone-600 mt-1 break-words overflow-hidden">{{ todo.description }}</div>
            <div v-if="todo.tags && todo.tags.length" class="flex flex-wrap gap-1 mt-1">
              <span 
                v-for="tag in todo.tags" 
                :key="tag.id" 
                class="bg-stone-100 text-stone-700 px-1.5 md:px-2 py-0.5 rounded text-xs font-semibold break-words"
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
                  class="size-3 md:size-4 rounded shrink-0" 
                  :style="{ accentColor: todo.difficulty ? todo.difficulty.color : '#57534e' }" 
                >
                <span :class="{ 'line-through text-stone-400': item.completed }" class="break-words overflow-hidden">
                  {{ item.text || item }}
                </span>
              </li>
            </ul>
          </div>
          <div class="relative flex items-center px-2 shrink-0">
            <button 
              class="p-1 rounded hover:bg-stone-100 transition-colors duration-200"
              @click="toggleDropdown(todo.id, $event)"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
              </svg>
            </button>
            <div 
              v-if="activeDropdown === todo.id"
              class="absolute right-0 top-8 z-10 bg-white rounded-lg shadow-lg border border-stone-200 py-1 min-w-[120px]"
              @click.stop
            >
              <button 
                class="w-full text-left px-3 py-2 hover:bg-stone-100 transition-colors duration-200 text-sm flex items-center gap-2"
                @click="openEditTodoModal(todo)"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                {{ trans('Edit') }}
              </button>
              <button 
                class="w-full text-left px-3 py-2 hover:bg-stone-100 transition-colors duration-200 text-sm flex items-center gap-2 text-red-600"
                @click="deleteTodo(todo)"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
