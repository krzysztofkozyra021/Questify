<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <div class="flex min-h-screen">
      <DashboardSidebar :userStatistics="userStatistics" :user="user" />

      <!-- Main Content -->
      <main class="flex-1 p-6 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
          <div class="bg-slate-800/80 rounded-lg border border-slate-700 shadow-lg p-6">
            <h1 class="text-2xl font-bold text-slate-200 mb-6">Settings</h1>

            <!-- Account Settings Section -->
            <div class="space-y-6">
              <!-- Profile Information -->
              <div class="bg-slate-700/50 rounded-lg p-4">
                <h2 class="text-xl font-semibold text-slate-200 mb-4">Profile Information</h2>
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Name</label>
                    <input type="text" class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500" :value="user?.name" disabled />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Email</label>
                    <input type="email" class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500" :value="user?.email" disabled />
                  </div>
                </div>
              </div>

              <!-- Password Section -->
              <div class="bg-slate-700/50 rounded-lg p-4">
                <h2 class="text-xl font-semibold text-slate-200 mb-4">Update Password</h2>
                <form @submit.prevent="updatePassword" class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Current Password</label>
                    <input type="password" v-model="passwordForm.current_password" class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">New Password</label>
                    <input type="password" v-model="passwordForm.password" class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Confirm New Password</label>
                    <input type="password" v-model="passwordForm.password_confirmation" class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                  </div>
                  <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Update Password
                  </button>
                </form>
              </div>

              <!-- Delete Account Section -->
              <div class="bg-slate-700/50 rounded-lg p-4">
                <h2 class="text-xl font-semibold text-slate-200 mb-4">Delete Account</h2>
                <p class="text-slate-300 mb-4">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                <button @click="confirmDeleteAccount" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                  Delete Account
                </button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="closeDeleteModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-slate-200 mb-4">Are you sure you want to delete your account?</h2>
        <p class="text-slate-300 mb-4">Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.</p>
        <form @submit.prevent="deleteAccount" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Password</label>
            <input 
              type="password" 
              v-model="deleteForm.password" 
              class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
          </div>
          <div class="flex justify-end space-x-4">
            <button 
              type="button" 
              @click="closeDeleteModal" 
              class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg transition-colors"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
              :disabled="deleteForm.processing"
            >
              Delete Account
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import DashboardSidebar from '@/Components/DashboardSidebar.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  user: Object,
  userStatistics: Object,
})

const showDeleteModal = ref(false)

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const deleteForm = useForm({
  password: '',
})

const updatePassword = () => {
  passwordForm.put(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset()
    },
  })
}

const confirmDeleteAccount = () => {
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  deleteForm.reset()
}

const deleteAccount = () => {
  deleteForm.delete(route('profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => {
      closeDeleteModal()
    },
  })
}
</script>