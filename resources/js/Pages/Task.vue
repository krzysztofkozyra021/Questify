<template>
  <div class="w-full p-5 bg-purple-200 flex flex-row items-center justify-start gap-4 rounded-lg shadow-lg text-pretty">
   <label class="flex items-center cursor-pointer relative">
      <input
        type="checkbox"
        class="peer h-10 w-10 cursor-pointer transition-all appearance-none rounded-full bg-slate-100 shadow hover:shadow-md border border-slate-300 checked:bg-slate-800 checked:border-slate-800"
        id="check-custom-style"
      />
      <span
        class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6"
          viewBox="0 0 20 20"
          fill="currentColor"
          stroke="currentColor"
          stroke-width="1"
        >
          <path
            fill-rule="evenodd"
            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
            clip-rule="evenodd"
          ></path>
        </svg>
      </span>
    </label>
    <div class="flex flex-col items-left justify-center m-0 p-0 gap-0">
     <h1 class="text-2xl font-bold mb-4">{{ task.name }}</h1>
    <p class="text-lg text-gray-600 mb-2">{{ task.description }}</p>
    </div>
   <div class="ml-auto">
    <div class="flex items-center justify-center">
    <svg
      xmlns="http://www.w3.org/2000/svg"
      class="h-6 w-6 text-gray-600"
      fill="currentColor"
      viewBox="0 0 24 24"
    >
      <circle cx="12" cy="5" r="2" />
      <circle cx="12" cy="12" r="2" />
      <circle cx="12" cy="19" r="2" />
    </svg>
  </div>
  </div>
    
  </div>
</template>

<script>
export default {
  name: 'Task',
  props: {
    task: {
      type: Object,
      required: true,
    },
  },
};
</script>