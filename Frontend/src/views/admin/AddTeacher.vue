<template>
  <div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-300">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-4">
      <div class="flex items-center gap-4">
        <router-link 
          to="/admin/teachers" 
          class="w-10 h-10 rounded-full flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-slate-400 hover:text-primary transition-all"
        >
          <span class="material-icons">west</span>
        </router-link>
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight uppercase">{{ isEditMode ? 'Edit Data Guru' : 'Tambah Guru Baru' }}</h1>
          <p class="text-sm text-slate-500 font-medium">{{ isEditMode ? 'Perbarui informasi dan akun akses pengajar.' : 'Daftarkan tenaga pengajar baru ke dalam sistem.' }}</p>
        </div>
      </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-surface-dark border border-slate-100 dark:border-slate-800 rounded-[32px] overflow-hidden shadow-sm">
      <div class="p-8 sm:p-12">
        <form @submit.prevent="handleSubmit" class="max-w-2xl space-y-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Nama -->
            <div class="space-y-3 md:col-span-2">
              <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">Nama Lengkap</label>
              <div class="relative group">
                <span class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">person</span>
                <input 
                  v-model="form.name" 
                  type="text" 
                  placeholder="Contoh: Budi Santoso, M.Pd" 
                  class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-sm font-bold outline-none transition-all"
                  required 
                />
              </div>
            </div>

            <!-- Email -->
            <div class="space-y-3">
              <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">Alamat Email</label>
              <div class="relative group">
                <span class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">email</span>
                <input 
                  v-model="form.email" 
                  type="email" 
                  placeholder="email@sekolah.sch.id" 
                  class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-sm font-bold outline-none transition-all"
                  required 
                />
              </div>
            </div>

            <!-- NIP -->
            <div class="space-y-3">
              <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">NIP (Nomor Induk Pegawai)</label>
              <div class="relative group">
                <span class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">badge</span>
                <input 
                  v-model="form.nip" 
                  type="text" 
                  placeholder="Opsional" 
                  class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-sm font-bold outline-none transition-all"
                />
              </div>
            </div>

            <!-- Password -->
            <div class="space-y-3 md:col-span-2">
              <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">
                {{ isEditMode ? 'Ganti Password (Opsional)' : 'Password Default' }}
              </label>
              <div class="relative group">
                <span class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">lock</span>
                <input 
                  v-model="form.password" 
                  type="password" 
                  :placeholder="isEditMode ? 'Kosongkan jika tidak ingin mengubah' : 'Minimal 8 karakter'" 
                  class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-sm font-bold outline-none transition-all"
                  :required="!isEditMode"
                />
              </div>
            </div>
          </div>

          <!-- Submit Buttons -->
          <div class="flex items-center gap-4 pt-6">
            <button 
              type="submit" 
              :disabled="loading"
              class="flex-1 py-4 bg-primary text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-primary-hover transition-all active:scale-95 disabled:opacity-50 shadow-lg shadow-primary/20"
            >
              <div class="flex items-center justify-center gap-2">
                <span v-if="loading" class="material-icons animate-spin text-sm">sync</span>
                <span>{{ loading ? 'Memproses...' : (isEditMode ? 'Simpan Perubahan' : 'Daftarkan Guru') }}</span>
              </div>
            </button>
            <router-link 
              to="/admin/teachers" 
              class="px-8 py-4 bg-slate-50 dark:bg-slate-800 text-slate-500 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-100 dark:hover:bg-slate-700 transition-all text-center"
            >
              Batal
            </router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '../../configs/api.js'
import { useNotificationStore } from '../../stores/notification'

const router = useRouter()
const route = useRoute()
const notification = useNotificationStore()
const loading = ref(false)
const isEditMode = ref(false)

const form = ref({
  name: '',
  email: '',
  password: '',
  nip: '',
  role: 'guru'
})

onMounted(async () => {
  if (route.params.id) {
    isEditMode.value = true
    await fetchTeacherData(route.params.id)
  }
})

const fetchTeacherData = async (id) => {
  loading.value = true
  try {
    const response = await api.get(`/admin/users/${id}`)
    const teacher = response.data.data || response.data
    form.value = {
      name: teacher.name,
      email: teacher.email,
      nip: teacher.nip || '',
      role: 'guru'
    }
  } catch (error) {
    console.error('Failed to fetch teacher data:', error)
    notification.add({ title: 'Gagal', message: 'Gagal mengambil data guru', type: 'error' })
    router.push('/admin/teachers')
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  if (loading.value) return
  loading.value = true
  try {
    const payload = { ...form.value }
    if (isEditMode.value && !payload.password) {
      delete payload.password
    }

    if (isEditMode.value) {
      await api.patch(`/admin/users/${route.params.id}`, payload)
      notification.add({ title: 'Berhasil', message: 'Data guru berhasil diperbarui', type: 'success' })
    } else {
      await api.post('/admin/users', payload)
      notification.add({ title: 'Berhasil', message: 'Guru berhasil didaftarkan', type: 'success' })
    }
    // Redirect back to list
    setTimeout(() => {
      router.push('/admin/teachers')
    }, 500)
  } catch (error) {
    console.error('Failed to save teacher:', error)
    notification.add({ title: 'Gagal', message: error.response?.data?.message || 'Gagal menyimpan data guru', type: 'error' })
  } finally {
    loading.value = false
  }
}
</script>
