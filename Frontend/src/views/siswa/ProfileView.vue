<template>
  <div class="max-w-4xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500 pb-20">
    <div class="flex flex-col md:flex-row gap-8">
      <!-- Left Column: Avatar & Quick Info -->
      <div class="w-full md:w-80 flex flex-col gap-6">
        <div class="bg-white dark:bg-surface-dark rounded-[32px] p-8 shadow-sm border border-slate-100 dark:border-slate-800 text-center relative overflow-hidden group">
          <div class="relative inline-block mb-6">
            <div class="w-32 h-32 rounded-full bg-slate-100 dark:bg-slate-800 border-4 border-white dark:border-slate-700 shadow-xl overflow-hidden relative mx-auto group/avatar transition-all duration-300 hover:scale-105">
              <img v-if="avatarPreview || user?.avatar_url" :src="avatarPreview || getFullUrl(user.avatar_url)" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-600">
                <span class="material-icons text-6xl">account_circle</span>
              </div>
              
              <!-- Hover Overlay for Upload -->
              <div 
                @click="triggerAvatarUpload"
                class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px] opacity-0 group-hover/avatar:opacity-100 transition-all flex flex-col items-center justify-center text-white cursor-pointer"
              >
                <span class="material-icons mb-1">{{ uploadingAvatar ? 'hourglass_empty' : 'photo_camera' }}</span>
                <span class="text-[10px] font-black uppercase tracking-widest">{{ uploadingAvatar ? 'Uploading...' : 'Ganti Foto' }}</span>
              </div>
            </div>
            
            <input ref="avatarInput" type="file" accept="image/*" class="hidden" @change="handleAvatarChange" />
          </div>

          <h3 class="text-xl font-black text-slate-900 dark:text-white mb-1 leading-tight">{{ user?.name }}</h3>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ user?.role }}</p>

          <div class="mt-8 pt-8 border-t border-slate-50 dark:border-slate-800/50 space-y-4">
            <div class="flex items-center justify-between text-left">
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kelas Utama</span>
              <span class="text-xs font-bold text-primary">{{ user?.student_classrooms?.[0]?.name || 'N/A' }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Settings Form -->
      <div class="flex-1 space-y-6">
        <div class="bg-white dark:bg-surface-dark rounded-[32px] p-8 sm:p-10 shadow-sm border border-slate-100 dark:border-slate-800">
          <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-8 flex items-center gap-3">
            <span class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center"><span class="material-icons">manage_accounts</span></span>
            Pengaturan Profil
          </h2>

          <form @submit.prevent="updateProfile" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Nama Lengkap</label>
                <input 
                  v-model="form.name"
                  type="text" 
                  required
                  class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-primary/10 text-sm font-bold text-slate-700 dark:text-slate-200"
                  placeholder="Nama Lengkap Anda"
                />
              </div>
              <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Email</label>
                <input 
                  v-model="form.email"
                  type="email" 
                  required
                  class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-primary/10 text-sm font-bold text-slate-700 dark:text-slate-200"
                  placeholder="email@example.com"
                />
              </div>
            </div>

            <div class="h-px bg-slate-50 dark:bg-slate-800/50 my-4"></div>

            <div class="space-y-6 bg-slate-50/50 dark:bg-slate-800/30 p-6 rounded-[24px]">
              <div class="flex items-center gap-2 mb-2">
                <span class="material-icons text-lg text-amber-500">lock</span>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ubah Password (Opsional)</span>
              </div>
              
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Password Baru</label>
                  <input 
                    v-model="form.password"
                    type="password" 
                    class="w-full px-6 py-4 bg-white dark:bg-slate-900 border-none rounded-2xl focus:ring-4 focus:ring-primary/10 text-sm font-bold text-slate-700 dark:text-slate-200"
                    placeholder="Minimal 8 karakter"
                  />
                </div>
                <div class="space-y-2">
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Konfirmasi Password</label>
                  <input 
                    v-model="form.password_confirmation"
                    type="password" 
                    class="w-full px-6 py-4 bg-white dark:bg-slate-900 border-none rounded-2xl focus:ring-4 focus:ring-primary/10 text-sm font-bold text-slate-700 dark:text-slate-200"
                    placeholder="Ulangi password baru"
                  />
                </div>
              </div>
            </div>

            <div class="pt-6">
              <button 
                type="submit" 
                :disabled="loading"
                class="w-full sm:w-auto px-10 py-4 bg-primary hover:bg-primary-hover text-white font-black rounded-2xl shadow-xl shadow-primary/30 transition-all flex items-center justify-center gap-3 disabled:opacity-50 active:scale-95"
              >
                <span class="material-icons">{{ loading ? 'hourglass_empty' : 'save' }}</span>
                {{ loading ? 'MENYIMPAN...' : 'SIMPAN PERUBAHAN' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useNotificationStore } from '../../stores/notification'

const notification = useNotificationStore()
const user = ref(null)
const avatarInput = ref(null)
const avatarPreview = ref(null)
const uploadingAvatar = ref(false)
const loading = ref(false)

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const fetchProfile = async () => {
  try {
    const token = localStorage.getItem('access_token')
    const res = await axios.get('http://localhost:8000/api/siswa/profile', {
      headers: { Authorization: `Bearer ${token}` }
    })
    user.value = res.data
    form.value.name = res.data.name
    form.value.email = res.data.email
  } catch (err) {
    console.error('Fetch profile failed', err)
  }
}

const triggerAvatarUpload = () => avatarInput.value?.click()

const handleAvatarChange = async (e) => {
  const file = e.target.files[0]
  if (!file) return

  // Local preview
  const reader = new FileReader()
  reader.onload = (e) => avatarPreview.value = e.target.result
  reader.readAsDataURL(file)

  // Upload to server
  uploadingAvatar.value = true
  const formData = new FormData()
  formData.append('avatar', file)

  try {
    const token = localStorage.getItem('access_token')
    const res = await axios.post('http://localhost:8000/api/siswa/profile/avatar', formData, {
      headers: { 
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'multipart/form-data'
      }
    })
    
    user.value.avatar_url = res.data.avatar_url
    
    // Update stored user in localStorage
    const storedUser = JSON.parse(localStorage.getItem('user'))
    storedUser.avatar_url = res.data.avatar_url
    localStorage.setItem('user', JSON.stringify(storedUser))
    
    // Refresh to sync layout
    window.dispatchEvent(new Event('storage')) // Simple hack or emit if using store
    
    notification.add({ title: 'Berhasil!', message: 'Foto profil berhasil diubah.', type: 'success' })
  } catch (err) {
    console.error('Avatar upload failed', err)
    notification.add({ title: 'Gagal!', message: 'Gagal mengupload foto.', type: 'error' })
  } finally {
    uploadingAvatar.value = false
  }
}

const updateProfile = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('access_token')
    const res = await axios.put('http://localhost:8000/api/siswa/profile', form.value, {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    user.value = res.data.user
    const storedUser = JSON.parse(localStorage.getItem('user'))
    storedUser.name = res.data.user.name
    storedUser.email = res.data.user.email
    localStorage.setItem('user', JSON.stringify(storedUser))

    notification.add({ title: 'Berhasil!', message: 'Profil Anda telah diperbarui.', type: 'success' })
    
    form.value.password = ''
    form.value.password_confirmation = ''
    
    window.dispatchEvent(new Event('storage'))
  } catch (err) {
    console.error('Profile update failed', err)
    const errors = err.response?.data?.errors
    notification.add({ 
      title: 'Gagal!', 
      message: errors ? Object.values(errors).flat().join(', ') : 'Gagal memperbarui profil.', 
      type: 'error' 
    })
  } finally {
    loading.value = false
  }
}

const getFullUrl = (url) => {
  if (!url) return ''
  if (url.startsWith('http')) return url
  return `http://localhost:8000${url}`
}

onMounted(fetchProfile)
</script>
