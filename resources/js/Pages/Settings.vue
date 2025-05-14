<script setup>
import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import { router } from '@inertiajs/vue3'
import { useTranslation } from '@/Composables/useTranslation';
import DashboardBar from '@/Components/DashboardBar.vue'
import Footer from '@/Components/Footer.vue'

const { trans } = useTranslation()

const props = defineProps({
  user: Object,
  userStatistics: Object,
  locales: Object,
  currentLocale: String,
})

const profileImageUrl = ref('/images/default-profile.png')
const selectedFile = ref(null)
const imagePreview = ref(null)
const uploadError = ref(null)
const isUploading = ref(false)
const fileInput = ref(null)

const form = useForm({
  profile_image: null
})

const fetchProfileImage = async () => {
  try {
    const response = await fetch('/profile/image')
    if (!response.ok) {
      throw new Error('Network response was not ok')
    }
    const data = await response.json()
    
    if (data.profile_image_url) {
      profileImageUrl.value = data.profile_image_url
    } else {
      profileImageUrl.value = '/images/default-profile.png'
    }
  } catch (error) {
    console.error('Error fetching profile image:', error)
    profileImageUrl.value = '/images/default-profile.png'
  }
}

const previewImage = (event) => {
  const file = event.target.files[0]
  if (file) {
    uploadError.value = null

    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']
    if (!validTypes.includes(file.type)) {
      uploadError.value = 'Please select a valid image file (JPG, PNG, or GIF).'
      return
    }

    // Check file size (2MB = 2 * 1024 * 1024 bytes)
    const maxSize = 2 * 1024 * 1024
    if (file.size > maxSize) {
      uploadError.value = 'Image size should not exceed 2MB.'
      return
    }

    selectedFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const uploadImage = async () => {
  if (!selectedFile.value) return
  
  uploadError.value = null
  isUploading.value = true

  const formData = new FormData()
  formData.append('profile_image', selectedFile.value)

  try {
    await router.post('/profile/image', formData, {
      preserveScroll: true,
      onSuccess: (page) => {
        if (page.props.profile_image_url) {
          profileImageUrl.value = page.props.profile_image_url
          imagePreview.value = null
          selectedFile.value = null
          if (fileInput.value) {
            fileInput.value.value = ''
          }
        }
        window.location.reload()
      },
      onError: (errors) => {
        if (errors.profile_image) {
          uploadError.value = errors.profile_image
        } else {
          uploadError.value = 'An error occurred while uploading the image.'
        }
      }
    })
  } catch (error) {
    console.error('Error uploading image:', error)
    uploadError.value = 'An error occurred while uploading the image.'
  } finally {
    isUploading.value = false
  }
}

onMounted(() => {
  fetchProfileImage()
  document.addEventListener('click', (event) => {
    const menu = document.getElementById('settings-menu')
    const menuButton = document.getElementById('settings-menu-button')
    if (menu && menuButton && !menu.contains(event.target) && !menuButton.contains(event.target)) {
      closeMenu()
    }
  })
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

const changeLocale = ($event) => {
  router.post(route('settings.changeLocale'), {
    locale: $event.target.value,
  }, {
    preserveScroll: true,
  })
}

const isMenuOpen = ref(false)

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const closeMenu = () => {
  isMenuOpen.value = false
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <div class="flex flex-col min-h-screen">
      <DashboardBar/>
      
      <div class="flex-1 w-[98%] bg-slate-600 rounded-lg shadow-lg mx-auto my-4 p-2">
        <div class="bg-slate-800 rounded-lg shadow-lg p-4 hover:shadow-xl transition-shadow duration-300 border border-slate-700">
          <h1 class="text-2xl font-bold text-slate-200 mb-6 text-center">{{ trans('Settings') }}</h1>

          <!-- Account Settings Section -->
          <div class="space-y-6">
            <!-- Locale Section -->
            <div class="bg-slate-700/50 rounded-lg p-4 border border-slate-600">
              <h2 class="text-xl font-semibold text-slate-200 mb-4">{{ trans('Locale') }}</h2>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-1">{{ trans('Locale') }}</label>
                  <select v-bind:value="currentLocale" 
                          class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                          @change="changeLocale">
                    <option v-for="locale in locales" :key="locale" :value="locale">{{ trans(locale) }}</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Profile Information -->
            <div class="bg-slate-700/50 rounded-lg p-4 border border-slate-600">
              <h2 class="text-xl font-semibold text-slate-200 mb-4">{{ trans('Profile Information') }}</h2>
              
              <!-- Profile Picture Section -->
              <div class="mb-6">
                <label class="block text-sm font-medium text-slate-300 mb-2">{{ trans('Profile Picture') }}</label>
                
                <div class="flex items-center space-x-4 mb-4">
                  <div class="w-24 h-24 rounded-full bg-slate-800 overflow-hidden border border-slate-600">
                    <img 
                      :src="imagePreview || profileImageUrl" 
                      alt="Profile Picture" 
                      class="w-full h-full object-cover"
                    />
                  </div>
                  
                  <div class="space-y-2">
                    <p class="text-sm text-slate-400">{{ trans('JPG, PNG or GIF. Max 2MB.') }}</p>
                    <input 
                      type="file" 
                      ref="fileInput"
                      @change="previewImage"
                      accept="image/*"
                      class="hidden"
                    />
                    <div class="flex space-x-2">
                      <button 
                        @click="$refs.fileInput.click()" 
                        type="button"
                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition-colors"
                        :disabled="isUploading"
                      >
                        {{ trans('Select Image') }}
                      </button>
                      <button 
                        v-if="selectedFile"
                        @click="uploadImage" 
                        type="button"
                        class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition-colors"
                        :disabled="isUploading"
                      >
                        {{ isUploading ? trans('Uploading...') : trans('Upload') }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-1">{{ trans('Name') }}</label>
                  <input type="text" 
                         class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                         :value="user?.name" 
                         disabled />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-1">{{ trans('Email') }}</label>
                  <input type="email" 
                         class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                         :value="user?.email" 
                         disabled />
                </div>
                <div v-if="!user.email_verified_at" class="text-red-400">
                  {{ trans('Email not verified. Please verify your email address.') }}
                </div>
              </div>
            </div>

            <!-- Password Section -->
            <div class="bg-slate-700/50 rounded-lg p-4 border border-slate-600">
              <h2 class="text-xl font-semibold text-slate-200 mb-4">{{ trans('Update Password') }}</h2>
              <form @submit.prevent="updatePassword" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-1">{{ trans('Current Password') }}</label>
                  <input type="password" 
                         v-model="passwordForm.current_password" 
                         class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-1">{{ trans('New Password') }}</label>
                  <input type="password" 
                         v-model="passwordForm.password" 
                         class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-1">{{ trans('Confirm New Password') }}</label>
                  <input type="password" 
                         v-model="passwordForm.password_confirmation" 
                         class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                  {{ trans('Update Password') }}
                </button>
              </form>
            </div>

            <!-- Delete Account Section -->
            <div class="bg-slate-700/50 rounded-lg p-4 border border-slate-600">
              <h2 class="text-xl font-semibold text-slate-200 mb-4">{{ trans('Delete Account') }}</h2>
              <p class="text-slate-300 mb-4">{{ trans('Once your account is deleted, all of its resources and data will be permanently deleted.') }}</p>
              <button @click="confirmDeleteAccount" 
                      class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                {{ trans('Delete Account') }}
              </button>
            </div>
          </div>
        </div>
      </div>
      <Footer/>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="closeDeleteModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-slate-200 mb-4">{{ trans('Are you sure you want to delete your account?') }}</h2>
        <p class="text-slate-300 mb-4">{{ trans('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>
        <form @submit.prevent="deleteAccount" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">{{ trans('Password') }}</label>
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
              {{ trans('Cancel') }}
            </button>
            <button 
              type="submit" 
              class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
              :disabled="deleteForm.processing"
            >
              {{ trans('Delete Account') }}
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Upload Error Notification -->
    <div v-if="uploadError" class="mt-2">
      <p class="text-sm text-red-500">{{ uploadError }}</p>
    </div>
  </div>
</template>
