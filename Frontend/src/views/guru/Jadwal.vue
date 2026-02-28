<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase">Jadwal Mengajar</h1>
        <p class="text-sm text-slate-500 font-medium">Atur dan lihat jadwal mengajar mingguan Anda.</p>
      </div>
      <div class="flex items-center gap-2">
        <router-link to="/guru/jadwal/add" class="flex items-center gap-2 px-4 py-2 bg-primary border border-transparent rounded-xl text-sm font-black text-white hover:bg-primary-hover shadow-lg shadow-primary/30 transition-all">
          <span class="material-icons text-lg">add</span>
          Tambah Jadwal
        </router-link>
      </div>
    </div>

    <!-- Calendar / List Toggle -->
    <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
      <button 
        @click="viewMode = 'calendar'"
        :class="`px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all ${viewMode === 'calendar' ? 'bg-white dark:bg-slate-700 text-primary shadow-sm' : 'text-slate-400 hover:text-slate-600'}`"
      >
        Kalender
      </button>
      <button 
        @click="viewMode = 'list'"
        :class="`px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all ${viewMode === 'list' ? 'bg-white dark:bg-slate-700 text-primary shadow-sm' : 'text-slate-400 hover:text-slate-600'}`"
      >
        Daftar
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white dark:bg-surface-dark rounded-[32px] border border-slate-100 dark:border-slate-800 p-20 text-center animate-pulse">
      <div class="flex flex-col items-center justify-center gap-4">
        <div class="w-12 h-12 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Memuat data jadwal...</p>
      </div>
    </div>

    <!-- Content -->
    <template v-else>
      <!-- Schedules Grid (Calendar View) -->
      <div v-if="viewMode === 'calendar'" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
        <div v-for="day in days" :key="day" class="space-y-4">
          <div class="text-center py-3 bg-white dark:bg-surface-dark border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm">
            <div class="text-[10px] font-black uppercase text-slate-400 tracking-tighter">{{ day }}</div>
          </div>
          
          <div class="space-y-3">
            <div v-for="item in getSchedulesByDay(day)" :key="item.id" class="p-4 bg-white dark:bg-surface-dark border border-slate-100 dark:border-slate-800 rounded-[24px] shadow-sm hover:border-primary/30 hover:shadow-md transition-all group relative cursor-pointer" @click="router.push(`/guru/jadwal/edit/${item.id}`)">
              <div class="text-[10px] font-black text-primary uppercase mb-1">{{ formatTime(item.start_time) }} - {{ formatTime(item.end_time) }}</div>
              <div class="font-black text-slate-900 dark:text-white leading-tight mb-2">{{ item.subject?.name }}</div>
              <div class="flex items-center justify-between text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                <span class="text-slate-500">{{ item.classroom?.name }}</span>
                <span class="bg-slate-50 dark:bg-slate-800 px-1.5 py-0.5 rounded border border-slate-100 dark:border-slate-700">{{ item.room || 'N/A' }}</span>
              </div>
              <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <router-link :to="`/guru/jadwal/edit/${item.id}`" @click.stop class="p-1.5 hover:bg-primary/10 rounded text-slate-400 hover:text-primary"><span class="material-icons text-xs">edit</span></router-link>
                <button @click.stop="deleteSchedule(item.id)" class="p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 rounded text-slate-400 hover:text-red-500"><span class="material-icons text-xs">delete</span></button>
              </div>
            </div>

            <!-- Empty day state -->
            <div v-if="getSchedulesByDay(day).length === 0" class="py-10 flex flex-col items-center justify-center border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-[24px] text-slate-300">
              <span class="material-icons opacity-20 text-4xl mb-1">event_busy</span>
              <span class="text-[9px] font-bold uppercase tracking-widest">Kosong</span>
            </div>
          </div>
        </div>
      </div>

      <!-- List View Table -->
      <div v-else class="bg-white dark:bg-surface-dark rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden animate-in fade-in duration-200">
        <div class="overflow-x-auto" v-if="schedules.length > 0">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800">Hari</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800">Mata Pelajaran</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800">Kelas</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800">Jam</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800">Ruangan</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
              <tr v-for="item in schedules" :key="item.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                <td class="px-6 py-4 text-sm font-black text-slate-900 dark:text-white">{{ item.day_of_week }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-black text-xs">{{ item.subject?.name?.charAt(0) }}</div>
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ item.subject?.name }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm font-bold text-slate-600 dark:text-slate-400">{{ item.classroom?.name }}</td>
                <td class="px-6 py-4 text-sm font-medium text-slate-500">{{ formatTime(item.start_time) }} - {{ formatTime(item.end_time) }}</td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-[10px] font-black text-slate-500 uppercase tracking-wider border border-slate-200 dark:border-slate-700">{{ item.room || 'N/A' }}</span>
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-1 sm:gap-2 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity">
                    <router-link :to="`/guru/jadwal/edit/${item.id}`" class="p-2 hover:bg-primary/10 rounded-xl text-slate-400 hover:text-primary transition-all"><span class="material-icons text-sm">edit</span></router-link>
                    <button @click="deleteSchedule(item.id)" class="p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl text-slate-400 hover:text-red-500 transition-all"><span class="material-icons text-sm">delete</span></button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-20 text-slate-300 flex flex-col items-center">
          <span class="material-icons text-6xl mb-2 opacity-20">history_toggle_off</span>
          <p class="text-sm font-black uppercase tracking-widest text-slate-400">Belum ada jadwal mengajar</p>
        </div>
      </div>
    </template>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useNotificationStore } from '../../stores/notification'

const router = useRouter()
const notification = useNotificationStore()
const viewMode = ref('calendar')
const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
const schedules = ref([])
const loading = ref(false)

const fetchSchedules = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('access_token')
    const res = await axios.get('http://localhost:8000/api/guru/teaching-schedules', {
      headers: { Authorization: `Bearer ${token}` }
    })
    schedules.value = res.data
  } catch (err) {
    console.error('Fetch schedules failed', err)
  } finally {
    loading.value = false
  }
}

const deleteSchedule = async (id) => {
  if (!confirm('Hapus jadwal ini?')) return
  try {
    const token = localStorage.getItem('access_token')
    await axios.delete(`http://localhost:8000/api/guru/teaching-schedules/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    notification.add({ title: 'Terhapus!', message: 'Jadwal telah dihapus.', type: 'success' })
    fetchSchedules()
  } catch (err) {
    console.error('Delete failed', err)
    notification.add({ title: 'Gagal!', message: 'Tidak dapat menghapus jadwal.', type: 'error' })
  }
}

const getSchedulesByDay = (day) => {
  return schedules.value
    .filter(s => s.day_of_week === day)
    .sort((a, b) => a.start_time.localeCompare(b.start_time))
}

const formatTime = (time) => {
  if (!time) return ''
  return time.substring(0, 5)
}

onMounted(() => {
  fetchSchedules()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}
</style>
