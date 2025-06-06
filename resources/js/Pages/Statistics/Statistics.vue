<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import { useTranslation } from '@/Composables/useTranslation'

const { trans } = useTranslation()
const page = usePage()

useHead({
  title: () => 'Questify - Statistics',
})

const props = defineProps({
    completedTasksByType: {
        type: Object,
        default: () => ({})
    },
    highestStreakHabit: {
        type: Object,
        default: () => ({})
    },
    mostMissedHabit: {
        type: Object,
        default: () => ({})
    },
    habits: {
        type: Array,
        default: () => []
    }
})

// Calculate total completed tasks
const totalCompletedTasks = computed(() => {
    return Object.values(props.completedTasksByType).reduce((sum, count) => sum + count, 0)
})

// Sort habits by completed_count to find highest and lowest streaks
const sortedHabits = computed(() => {
    return [...props.habits].sort((a, b) => b.completed_count - a.completed_count)
})

const highestStreakHabit = computed(() => sortedHabits.value[0])
const lowestStreakHabit = computed(() => sortedHabits.value[sortedHabits.value.length - 1])
</script>

<template>
    <div class="min-h-screen bg-stone-900 flex flex-col">
        <Header :show-player-panel="false" />
        
        <main class="flex-1 w-full mx-auto py-4 sm:py-6 md:py-8 lg:py-12 px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-2xl font-bold text-amber-50 mb-6">{{ trans('Statistics') }}</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Completed Tasks Card -->
                    <div class="bg-stone-800 rounded-lg p-6 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold mb-2 text-amber-50">{{ trans('Total Completed Tasks') }}</h3>
                                <p class="text-3xl font-bold text-amber-500">{{ totalCompletedTasks }}</p>
                                <div class="mt-2 space-y-1">
                                    <p v-if="completedTasksByType.habit" class="text-sm text-amber-200">
                                        {{ trans('Habits') }}: {{ completedTasksByType.habit }}
                                    </p>
                                    <p v-if="completedTasksByType.todo" class="text-sm text-amber-200">
                                        {{ trans('Todos') }}: {{ completedTasksByType.todo }}
                                    </p>
                                    <p v-if="completedTasksByType.daily" class="text-sm text-amber-200">
                                        {{ trans('Dailies') }}: {{ completedTasksByType.daily }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-amber-500">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Highest Streak Card -->
                    <div class="bg-stone-800 rounded-lg p-6 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold mb-2 text-amber-50">{{ trans('Highest Streak Habit') }}</h3>
                                <p class="text-3xl font-bold text-amber-500">{{ highestStreakHabit?.completed_count || 0 }}</p>
                                <p v-if="highestStreakHabit?.title" class="text-lg text-amber-200 mt-2">
                                    {{trans("Title")}}: {{ highestStreakHabit.title }}
                                </p>
                            </div>
                            <div class="text-amber-500">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Most Missed Habit Card -->
                    <div class="bg-stone-800 rounded-lg p-6 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold mb-2 text-amber-50">{{ trans('Most Missed Habit Streak') }}</h3>
                                <p class="text-3xl font-bold text-amber-500">{{ mostMissedHabit?.not_completed_count || 0 }}</p>
                                <p v-if="mostMissedHabit?.title" class="text-lg text-amber-200 mt-2">
                                    {{trans("Title")}}: {{ mostMissedHabit.title }}
                                </p>
                            </div>
                            <div class="text-amber-500">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <Footer />
    </div>
</template> 