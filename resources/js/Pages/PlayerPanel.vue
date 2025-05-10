<template>
    <!-- Player profile -->
    <div class="flex flex-col h-60 bg-zinc-600 p-2 m-2 rounded items-center justify-items-between justify-around">
        <div class="w-[100px] h-[100px] rounded-full bg-gray-200" @click="$emit('showPlayerDetails')">
        </div>
        <h1 class="text-xl font-bold">{{ $page.props.auth.user.name }}</h1>
        
        
        <!-- Level Display -->
        <div class="flex items-center gap-2">
            <span class="text-yellow-400">Level:</span>
            <span class="font-bold">{{ props.userStatistics?.level || 1 }}</span>
        </div>

        <!-- Health Bar -->
        <div class="w-full">
            <div class="flex justify-between mb-1">
                <span class="text-red-400">Health</span>
                <span>{{ props.userStatistics?.health || 0 }}/100</span>
            </div>
            <div class="w-full h-2 bg-gray-700 rounded">
                <div class="bg-red-500 h-full rounded" :style="{ width: `${props.userStatistics?.health || 0}%` }"></div>
            </div>
        </div>

        <!-- Energy Bar -->
        <div class="w-full">
            <div class="flex justify-between mb-1">
                <span class="text-blue-400">Energy</span>
                <span>{{ props.userStatistics?.energy || 0 }}/100</span>
            </div>
            <div class="w-full h-2 bg-gray-700 rounded">
                <div class="bg-blue-500 h-full rounded" :style="{ width: `${props.userStatistics?.energy || 0}%` }"></div>
            </div>
        </div>

        <!-- Experience Bar -->
        <div class="w-full">
            <div class="flex justify-between mb-1">
                <span class="text-green-400">Experience</span>
                <span>{{ props.userStatistics?.experience || 0 }}/{{ props.userStatistics?.nextLevelExperience || 100 }}</span>
            </div>
            <div class="w-full h-2 bg-gray-700 rounded">
                <div class="bg-green-500 h-full rounded" :style="{ width: `${(props.userStatistics?.experience || 0) / (props.userStatistics?.nextLevelExperience || 100) * 100}%` }"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
  userStatistics: Object,
  user: Object,
});

// Debug logging
console.log('PlayerPanel props:', props);
</script>
