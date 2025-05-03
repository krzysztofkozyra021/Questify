<template>
    <!-- Player profile -->
    <div class="flex flex-col h-60 bg-zinc-600 p-2 m-2 rounded items-center justify-items-between  justify-around">
        <div class="w-[100px] h-[100px] rounded-full bg-gray-200" @click="$emit('showPlayerDetails')">
        </div>
        <h1>Player 1</h1>
        <div class="w-full h-2 mb-6 rounded">
            <p class="bg-grey-200 text-center">HP</p>
            <div class="bg-red-500 h-full w-full rounded"></div>
        </div>
        <p>Energia</p>
        <div class="w-full bg-gray-500 h-2 rounded">
            <div class="bg-blue-500 h-full w-3/4 rounded-full"></div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'PlayerPanel',
    props: {
        playerName: {
        type: String,
        required: true,
        },
    },
    
};
</script>