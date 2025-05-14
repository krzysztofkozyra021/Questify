<script setup>
import { ref } from "vue";
import DashboardSidebar from "@/Components/DashboardSidebar.vue";
import { onMounted } from "vue";
import { getMotivationalQuote } from "@/Composables/getMotivationalQuote";
import Notification from "@/Components/Notification.vue";
import { usePage } from '@inertiajs/vue3';
import Footer from "@/Components/Footer.vue";

const page = usePage();

const isTaskListVisible = ref(true);
const showMotivationalQuote = ref(false);
const motivationalQuote = ref(page.props.motivationalQuote);



// Show motivational quote after user visits dashboard
onMounted(async () => {
  // Only show if not already shown (prevents double popup if page reloads quickly)
  if (!showMotivationalQuote.value) {
    // Fetch the quote
    motivationalQuote.value = await getMotivationalQuote();

    if (motivationalQuote.value) {
      showMotivationalQuote.value = true;
    }
    // Hide after 5 seconds
    setTimeout(() => {
      showMotivationalQuote.value = false;
    }, 5000);
  }
});

</script>
<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">


    <!-- Motivational Quote -->
    <Notification :showNotification="showMotivationalQuote" :textToDisplay="motivationalQuote" />

    <div class="flex min-h-screen">
      <DashboardSidebar/>

      <!-- Main Content -->
      <main class="flex-1 p-6 overflow-y-auto">
        <!-- Quest Board -->
        <div class="max-w-4xl mx-auto">
          <div class="bg-slate-800/80 rounded-lg border border-slate-700 shadow-lg">
            <!-- Quest Board Header -->
            <div class="flex justify-between items-center p-4 border-b border-slate-700">
              <h2 class="text-xl font-semibold text-slate-200">Active Quests</h2>
              <button
                @click="toggleTaskList"
                class="text-slate-400 hover:text-slate-200 transition-colors p-2 rounded-lg hover:bg-slate-700/50"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    :class="{ hidden: isTaskListVisible }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M19 9l-7 7-7-7"
                  />
                  <path
                    :class="{ hidden: !isTaskListVisible }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M19 15l-7-7-7 7"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
  <Footer/>
</template>

<style>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}

::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.2);
}
</style>
