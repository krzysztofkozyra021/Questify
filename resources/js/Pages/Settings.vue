<script setup>
import { ref, onMounted, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'
import Modal from '@/Components/Modal.vue'
import { router } from '@inertiajs/vue3'
import { useTranslation } from '@/Composables/useTranslation';
import Footer from '@/Components/Footer.vue'
import Header from '@/Components/Header.vue'

const { trans } = useTranslation()

useHead({
  title: () => trans('Settings') + " | Questify "
})

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
const fileInputLabel = ref(trans('No file selected'))
const fileInputButton = ref(trans('Browse...'))

// Add watch effect to update translations when locale changes
watch(() => props.currentLocale, () => {
  fileInputLabel.value = trans('No file selected')
  fileInputButton.value = trans('Browse...')
})

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
    fileInputLabel.value = file.name

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

<style scoped>
input[type="file"]::file-selector-button {
  content: attr(data-browse-text);
}

input[type="file"]::before {
  content: attr(data-placeholder);
}

.custom-file-input {
  position: relative;
  display: inline-block;
  width: 100%;
}

.custom-file-input input[type="file"] {
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
}

.file-input-label {
  display: flex;
  align-items: center;
  padding: 0.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.375rem;
  background-color: white;
}

.file-input-button {
  padding: 0.5rem 1rem;
  margin-right: 0.5rem;
  background-color: #e5e7eb;
  border-radius: 0.375rem;
  font-weight: 600;
  color: #1f2937;
}

.file-input-button:hover {
  background-color: #d1d5db;
}

@media (max-width: 640px) {
  .file-input-label {
    padding: 0.375rem;
  }
  
  .file-input-button {
    padding: 0.375rem 0.75rem;
    margin-right: 0.375rem;
  }
}
</style>

<template>
  <div class="min-h-screen bg-white">
    <Header />
    <main class="max-w-2xl mx-auto py-4 md:py-6 lg:py-8 px-2 md:px-4">
      <h1 class="text-xl md:text-2xl font-bold text-stone-800 mb-4 md:mb-6 lg:mb-8">{{ trans('Settings') }}</h1>
      <div class="space-y-4 md:space-y-6 lg:space-y-8">

        <!-- Profile Information Section -->
        <div class="rounded-lg p-3 md:p-4">
          <h2 class="text-lg md:text-xl font-semibold text-stone-800 mb-3 md:mb-4">{{ trans('Profile Information') }}</h2>
          <div class="space-y-3 md:space-y-4">
            <div>
              <label class="block text-base md:text-lg font-bold text-stone-800 mb-1">{{ trans('Name') }}</label>
              <p class="text-base md:text-lg text-stone-800">{{ user.name }}</p>
            </div>
            <div>
              <label class="block text-base md:text-lg font-bold text-stone-800 mb-1">{{ trans('Email') }}</label>
              <p class="text-base md:text-lg text-stone-800">{{ user.email }} - <span v-if="!user.email_verified_at" class="text-sm md:text-base text-stone-600">{{ trans('Email not verified') }}</span></p>
            </div>
            <button @click="confirmDeleteAccount" class="text-sm md:text-base bg-red-500 text-white px-3 md:px-4 py-1.5 md:py-2 rounded font-bold shadow hover:bg-red-600">{{ trans('Delete Account') }}</button>
          </div>
        </div>

        <div class="border-t-2 border-stone-400"></div>

        <!-- Locale Section -->
        <div class="rounded-lg p-3 md:p-4">
          <h2 class="text-lg md:text-xl font-semibold text-stone-800 mb-3 md:mb-4">{{ trans('Locale') }}</h2>
          <div class="space-y-3 md:space-y-4">
            <div>
              <select v-bind:value="currentLocale" class="w-full px-2 md:px-3 py-1.5 md:py-2 bg-stone-100 border border-stone-600 rounded-lg text-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm md:text-base" @change="changeLocale">
                <option v-for="locale in locales" :key="locale" :value="locale">{{ trans(locale) }}</option>
              </select>
            </div>
          </div>
        </div>

        <div class="border-t-2 border-stone-400"></div>

        <!-- Profile Image Section -->
        <div class="rounded-lg p-3 md:p-4">
          <h2 class="text-lg md:text-xl font-semibold text-stone-800 mb-3 md:mb-4">{{ trans('Profile Image') }}</h2>
          <div class="flex items-center gap-3 md:gap-4 mb-3 md:mb-4">
            <img :src="profileImageUrl" alt="Profile" class="w-16 h-16 md:w-20 md:h-20 rounded-lg border-2 border-stone-900 bg-white shadow" />
            <div class="custom-file-input">
              <div class="file-input-label">
                <span class="file-input-button text-sm md:text-base">{{ fileInputButton }}</span>
                <span class="text-sm md:text-base">{{ fileInputLabel }}</span>
              </div>
              <input 
                ref="fileInput" 
                type="file" 
                accept="image/*" 
                @change="previewImage" 
                class="block w-full text-sm md:text-base text-stone-800"
              />
            </div>
          </div>
          <div v-if="imagePreview" class="mb-2 flex flex-row gap-3 md:gap-4 items-center">
            <img :src="imagePreview" alt="Preview" class="w-16 h-16 md:w-20 md:h-20 rounded-lg border-2 border-stone-900 bg-white shadow" />
            <p class="text-sm md:text-base font-semibold text-stone-800">{{ trans('Preview') }}</p>
          </div>
          <div v-if="uploadError" class="text-red-500 text-xs md:text-sm mb-2">{{ uploadError }}</div>
          <button @click="uploadImage" :disabled="isUploading" class="bg-stone-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded font-bold shadow hover:bg-stone-700 disabled:opacity-50 text-sm md:text-base">{{ trans('Upload') }}</button>
        </div>

        <div class="border-t-2 border-stone-400"></div>

        <!-- Password Section -->
        <div class="rounded-lg p-3 md:p-4">
          <h2 class="text-lg md:text-xl font-semibold text-stone-800 mb-3 md:mb-4">{{ trans('Change Password') }}</h2>
          <form @submit.prevent="updatePassword" class="space-y-3 md:space-y-4">
            <div>
              <label class="block text-sm md:text-base font-medium text-stone-800 mb-1">{{ trans('Current Password') }}</label>
              <input v-model="passwordForm.current_password" type="password" class="w-full px-2 md:px-3 py-1.5 md:py-2 rounded border border-stone-300 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm md:text-base" />
            </div>
            <div>
              <label class="block text-sm md:text-base font-medium text-stone-800 mb-1">{{ trans('New Password') }}</label>
              <input v-model="passwordForm.password" type="password" class="w-full px-2 md:px-3 py-1.5 md:py-2 rounded border border-stone-300 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm md:text-base" />
            </div>
            <div>
              <label class="block text-sm md:text-base font-medium text-stone-800 mb-1">{{ trans('Confirm Password') }}</label>
              <input v-model="passwordForm.password_confirmation" type="password" class="w-full px-2 md:px-3 py-1.5 md:py-2 rounded border border-stone-300 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm md:text-base" />
            </div>
            <button type="submit" class="bg-stone-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded font-bold shadow hover:bg-stone-700 text-sm md:text-base">{{ trans('Update Password') }}</button>
          </form>
        </div>
      </div>

      <Modal :show="showDeleteModal" @close="closeDeleteModal">
        <div class="p-4 md:p-6 bg-white rounded-lg">
          <h2 class="text-lg md:text-xl font-semibold text-stone-800 mb-4 md:mb-6">{{ trans('Are you sure you want to delete your account?') }}</h2>
          <p class="text-sm md:text-base text-stone-600 mb-4 md:mb-6">{{ trans('This action cannot be undone. Please enter your password to confirm.') }}</p>
          <form @submit.prevent="deleteAccount" class="space-y-3 md:space-y-4">
            <div>
              <label class="block text-sm md:text-base font-medium text-stone-800 mb-1">{{ trans('Password') }}</label>
              <input 
                v-model="deleteForm.password" 
                type="password" 
                class="w-full px-2 md:px-3 py-1.5 md:py-2 rounded border border-stone-300 focus:outline-none focus:ring-2 focus:ring-stone-600 text-sm md:text-base" 
                :placeholder="trans('Enter your password')"
              />
            </div>
            <div class="flex justify-end gap-2 md:gap-3 mt-4 md:mt-6">
              <button 
                type="button" 
                @click="closeDeleteModal" 
                class="px-3 md:px-4 py-1.5 md:py-2 rounded font-bold shadow bg-stone-200 text-stone-800 hover:bg-stone-300 transition-colors text-sm md:text-base"
              >
                {{ trans('Cancel') }}
              </button>
              <button 
                type="submit" 
                class="px-3 md:px-4 py-1.5 md:py-2 rounded font-bold shadow bg-red-500 text-white hover:bg-red-600 transition-colors text-sm md:text-base"
              >
                {{ trans('Delete Account') }}
              </button>
            </div>
          </form>
        </div>
      </Modal>
    </main>
    <Footer />
  </div>
</template>
