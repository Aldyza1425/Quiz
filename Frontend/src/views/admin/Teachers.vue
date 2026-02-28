<template>
  <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-300">

    <FlashMessage />
    <ConfirmModal />

    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
      <div>
        <div class="flex items-center gap-3 mb-2">
          <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
            <span class="material-icons">person_4</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tighter uppercase">
            Data Guru
          </h1>
        </div>
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
          Kelola informasi tenaga pengajar dan akun akses mereka.
        </p>
      </div>

      <div class="flex items-center gap-3">
        <div class="hidden sm:flex items-center bg-white dark:bg-slate-800 rounded-2xl px-4 py-2.5 border border-slate-200 dark:border-slate-700 shadow-sm group focus-within:ring-2 focus-within:ring-primary/20 transition-all">
          <span class="material-icons text-slate-400 group-focus-within:text-primary transition-colors mr-2 text-xl">search</span>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Cari guru..." 
            class="bg-transparent border-none p-0 text-sm focus:ring-0 w-48 lg:w-64 font-bold text-slate-700 dark:text-slate-200 placeholder:text-slate-300" 
          />
        </div>
        <router-link 
          to="/admin/teachers/add"
          class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-primary-hover shadow-xl shadow-primary/20 transition-all active:scale-95 whitespace-nowrap"
        >
          <span class="material-icons text-lg">add_circle</span>
          Tambah Guru
        </router-link>
      </div>
    </div>


    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div v-for="stat in stats" :key="stat.label" class="bg-white dark:bg-surface-dark p-6 rounded-[24px] border border-slate-100 dark:border-white/5 shadow-sm overflow-hidden relative group">
        <div :class="`absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-5 group-hover:scale-110 transition-transform duration-500 ${stat.color}`"></div>
        <div class="relative z-10">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">{{ stat.label }}</p>
          <div class="flex items-end gap-2">
            <span class="text-3xl font-black text-slate-800 dark:text-white tracking-tighter">{{ stat.value }}</span>
            <span class="text-xs font-bold text-slate-400 mb-1.5">Guru</span>
          </div>
        </div>
      </div>
    </div>


    <div class="bg-white dark:bg-surface-dark rounded-[32px] border border-slate-100 dark:border-white/5 shadow-sm overflow-hidden min-h-[400px]">

      <div v-if="loading" class="flex flex-col items-center justify-center py-20 px-8 text-center bg-white dark:bg-surface-dark">
        <div class="flex flex-col items-center justify-center gap-4">
          <div class="w-12 h-12 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] animate-pulse">Memuat data guru...</p>
        </div>
      </div>


      <div v-else-if="filteredTeachers.length > 0" class="hidden md:block overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="border-b border-slate-50 dark:border-white/5">
              <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Profil Guru</th>
              <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Email & Kontak</th>
              <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Tanggal Bergabung</th>
              <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50 dark:divide-white/5">
            <tr v-for="teacher in filteredTeachers" :key="teacher.id" class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-all">
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 group-hover:bg-primary/10 group-hover:text-primary group-hover:border-primary/20 transition-all overflow-hidden shadow-inner">
                    <span v-if="!teacher.avatar" class="material-icons text-2xl">person</span>
                    <img v-else :src="teacher.avatar" alt="" class="w-full h-full object-cover" />
                  </div>
                  <div>
                    <p class="text-sm font-black text-slate-800 dark:text-white group-hover:text-primary transition-colors leading-none mb-1 uppercase tracking-tight">
                      {{ teacher.name }}
                    </p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                      NIP: {{ teacher.nip || '---' }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5 text-sm font-bold text-slate-600 dark:text-slate-400 font-mono italic">
                {{ teacher.email }}
              </td>
              <td class="px-8 py-5">
                <div class="flex flex-col">
                  <p class="text-xs font-bold text-slate-700 dark:text-slate-300">
                    {{ new Date(teacher.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                  </p>
                  <p class="text-[10px] font-medium text-slate-400 italic">
                    {{ getTimeAgo(teacher.created_at) }}
                  </p>
                </div>
              </td>
              <td class="px-8 py-5 text-right">
                <div class="flex items-center justify-end gap-2 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                  <router-link 
                    :to="`/admin/teachers/edit/${teacher.id}`"
                    class="w-10 h-10 rounded-xl bg-white dark:bg-slate-700 text-slate-400 hover:text-primary hover:shadow-lg border border-slate-200 dark:border-slate-600 flex items-center justify-center transition-all"
                    title="Edit Data"
                  >
                    <span class="material-icons text-lg">edit</span>
                  </router-link>
                  <button 
                    @click="confirmDelete(teacher)"
                    class="w-10 h-10 rounded-xl bg-white dark:bg-slate-700 text-slate-400 hover:text-red-500 hover:shadow-lg border border-slate-200 dark:border-slate-600 flex items-center justify-center transition-all"
                    title="Hapus Guru"
                  >
                    <span class="material-icons text-lg">delete</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>


      <div v-if="!loading && filteredTeachers.length > 0" class="md:hidden space-y-px p-4">
        <div v-for="teacher in filteredTeachers" :key="teacher.id" class="bg-slate-50/50 dark:bg-slate-800/10 p-5 rounded-3xl border border-transparent active:bg-primary/5 active:border-primary/20 transition-all mb-4">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-300 shadow-sm overflow-hidden">
                <span v-if="!teacher.avatar" class="material-icons text-2xl">person</span>
                <img v-else :src="teacher.avatar" alt="" class="w-full h-full object-cover" />
              </div>
              <div>
                <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-tight">{{ teacher.name }}</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">NIP: {{ teacher.nip || '---' }}</p>
              </div>
            </div>
            <div class="flex gap-2">
               <router-link 
                :to="`/admin/teachers/edit/${teacher.id}`" 
                class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 text-slate-400 shadow-sm flex items-center justify-center"
               >
                <span class="material-icons text-sm">edit</span>
               </router-link>
               <button @click="confirmDelete(teacher)" class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 text-red-400 shadow-sm flex items-center justify-center"><span class="material-icons text-sm">delete</span></button>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100 dark:border-white/5">
            <div class="space-y-1">
              <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Email</p>
              <p class="text-[11px] font-bold text-slate-600 dark:text-slate-400 truncate">{{ teacher.email }}</p>
            </div>
            <div class="space-y-1">
              <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Bergabung</p>
              <p class="text-[11px] font-bold text-slate-600 dark:text-slate-400">
                {{ new Date(teacher.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) }}
              </p>
            </div>
          </div>
        </div>
      </div>


      <div v-if="!loading && filteredTeachers.length === 0" class="flex flex-col items-center justify-center py-20 px-8 text-center animate-in fade-in zoom-in duration-500">
        <div class="w-24 h-24 rounded-full bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center mb-6 border-2 border-dashed border-slate-200 dark:border-slate-700">
          <span class="material-icons text-4xl text-slate-200 dark:text-slate-700">person_off</span>
        </div>
        <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight mb-2">Tidak Ada Data Guru</h3>
        <p class="text-sm text-slate-500 max-w-xs mx-auto">
          Belum ada guru yang terdaftar atau data yang Anda cari tidak ditemukan.
        </p>
        <button v-if="searchQuery" @click="searchQuery = ''" class="mt-6 text-primary font-bold text-sm hover:underline">Reset Pencarian</button>
      </div>
    </div>


  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../../configs/api.js'
import { useNotificationStore } from '../../stores/notification'
import FlashMessage from '../../components/FlashMessage.vue'
import ConfirmModal from '../../components/ConfirmModal.vue'

const notification = useNotificationStore()
const teachers = ref([])
const loading = ref(false)
const searchQuery = ref('')

const stats = computed(() => {
  const total = teachers.value.length
  return [
    { label: 'Total Pengajar', value: total, color: 'bg-primary' },
    { label: 'Aktif Bulan Ini', value: total, color: 'bg-green-500' }, // Dummy active state
    { label: 'Akun Baru', value: teachers.value.filter(t => isNew(t.created_at)).length, color: 'bg-orange-500' }
  ]
})

const filteredTeachers = computed(() => {
  if (!searchQuery.value) return teachers.value
  const q = searchQuery.value.toLowerCase()
  return teachers.value.filter(t => 
    t.name.toLowerCase().includes(q) || 
    t.email.toLowerCase().includes(q) ||
    (t.nip && t.nip.includes(q))
  )
})

const isNew = (date) => {
  const joinDate = new Date(date)
  const today = new Date()
  const diffTime = Math.abs(today - joinDate)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return diffDays <= 7
}

const getTimeAgo = (date) => {
  const seconds = Math.floor((new Date() - new Date(date)) / 1000)
  let interval = seconds / 31536000
  if (interval > 1) return Math.floor(interval) + " tahun lalu"
  interval = seconds / 2592000
  if (interval > 1) return Math.floor(interval) + " bulan lalu"
  interval = seconds / 86400
  if (interval > 1) return Math.floor(interval) + " hari lalu"
  return "Baru Bergabung"
}

const fetchTeachers = async () => {
  loading.value = true
  try {
    const response = await api.get('/admin/users')
    const allUsers = response.data.data || response.data
    teachers.value = allUsers.filter(u => u.role === 'guru')
  } catch (err) {
    console.error('Fetch teachers failed', err)
    notification.add({ title: 'Gagal', message: 'Gagal mengambil data guru', type: 'error' })
  } finally {
    loading.value = false
  }
}

// Modal logic removed

const confirmDelete = async (teacher) => {
  const confirmed = await notification.confirm({
    title: 'Hapus Guru?',
    message: `Data guru "${teacher.name}" dan akses loginnya akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.`,
    confirmText: 'Hapus Data',
    cancelText: 'Batal'
  })

  if (confirmed) {
    try {
      await api.delete(`/admin/users/${teacher.id}`)
      notification.add({ 
        title: 'Terhapus!', 
        message: 'Data tenaga pengajar berhasil dihapus dari sistem.', 
        type: 'success' 
      })
      fetchTeachers()
    } catch (err) {
      notification.add({ 
        title: 'Gagal', 
        message: 'Tidak dapat menghapus data guru. Silakan coba lagi.', 
        type: 'error' 
      })
    }
  }
}

onMounted(fetchTeachers)
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(0,0,0,0.05);
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.05);
}
</style>
