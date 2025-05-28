<template>
    <header class="bg-zinc-900 h-20 rounded-t-md w-full flex flex-row items-center justify-center gap-0">
        <button
            @click="changeStatus(1)"
            id="1"
            :class="getClass(1)"
        >
            Today
        </button>
        <button
            @click="changeStatus(2)"
            id="2"
            :class="getClass(2)"
        >
            Next Week
        </button>
        <button
            @click="changeStatus(3)"
            id="3"
            :class="getClass(3)"
        >
            Next Month
        </button>
    </header>
</template>

<script>
export default {
    name: 'DayChange',
    data() {
        return {
            activeId: 1, // Przechowuje aktualnie aktywne id
        };
    },
    methods: {
        changeStatus(id) {
            this.activeId = id; // Ustawia aktywne id
            console.log(`Aktywne id: ${id}`);
        },
        getClass(id) {
            return {
                'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 focus:outline-none focus:shadow-outline': true,
                'rounded-l': id === 1, // Zaokrąglenie dla pierwszego przycisku
                'rounded-r': id === 3, // Zaokrąglenie dla ostatniego przycisku
                'bg-blue-700': this.activeId === id, // Zmiana koloru, jeśli przycisk jest aktywny
            };
        },
    },
};
</script>