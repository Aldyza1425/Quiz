<template>
  <div class="space-y-6 sm:space-y-8">
    <!-- Loading State -->
    <div v-if="loading" class="space-y-8 animate-pulse">
      <div class="h-32 bg-slate-100 dark:bg-slate-800 rounded-[32px]"></div>
      <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="i in 4" :key="i" class="h-32 bg-slate-100 dark:bg-slate-800 rounded-[28px]"></div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 h-96 bg-slate-100 dark:bg-slate-800 rounded-[32px]"></div>
        <div class="space-y-6">
          <div class="h-48 bg-slate-100 dark:bg-slate-800 rounded-[32px]"></div>
          <div class="h-64 bg-slate-100 dark:bg-slate-800 rounded-[32px]"></div>
        </div>
      </div>
    </div>

    <template v-else>
      <!-- Welcome Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-surface-dark p-6 sm:p-8 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm">
        <div class="space-y-1">
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
            Selamat Datang, <span class="text-primary">{{ user?.name }}</span>! 👋
          </h1>
          <p class="text-sm sm:text-base text-slate-500 font-medium">Ini adalah ringkasan aktivitas dan manajemen kelas Anda hari ini.</p>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 bg-primary/10 rounded-2xl text-primary font-bold text-sm self-start sm:self-center">
          <span class="material-icons text-lg">calendar_today</span>
          <span>{{ todayLabel }}</span>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white dark:bg-surface-dark p-6 rounded-[28px] border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group">
          <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-500 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <span class="material-icons">groups</span>
          </div>
          <div class="text-2xl font-black text-slate-900 dark:text-white">{{ dashboardData.stats.total_classes }}</div>
          <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Total Kelas</div>
        </div>
        
        <div class="bg-white dark:bg-surface-dark p-6 rounded-[28px] border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group">
          <div class="w-12 h-12 rounded-2xl bg-green-50 dark:bg-green-900/20 text-green-500 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <span class="material-icons">person</span>
          </div>
          <div class="text-2xl font-black text-slate-900 dark:text-white">{{ dashboardData.stats.total_students }}</div>
          <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Total Siswa</div>
        </div>

        <div class="bg-white dark:bg-surface-dark p-6 rounded-[28px] border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group">
          <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-900/20 text-orange-500 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <span class="material-icons">quiz</span>
          </div>
          <div class="text-2xl font-black text-slate-900 dark:text-white">{{ dashboardData.stats.active_quizzes }}</div>
          <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Quiz Aktif</div>
        </div>

        <div class="bg-white dark:bg-surface-dark p-6 rounded-[28px] border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group">
          <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-900/20 text-purple-500 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <span class="material-icons">schedule</span>
          </div>
          <div class="text-2xl font-black text-slate-900 dark:text-white">{{ dashboardData.stats.today_schedules_count }}</div>
          <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Jadwal Hari Ini</div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
        <!-- Today's Schedule -->
        <div class="lg:col-span-2 bg-white dark:bg-surface-dark rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
          <div class="p-6 sm:p-8 flex items-center justify-between border-b border-slate-50 dark:border-slate-800">
            <h2 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-3">
              <span class="material-icons text-primary">event_note</span>
              Jadwal Mengajar Hari Ini
            </h2>
            <router-link to="/guru/jadwal" class="text-sm font-bold text-primary hover:underline">Lihat Semua</router-link>
          </div>
          <div class="p-6 sm:p-8 space-y-4">
            <div v-if="dashboardData.today_schedules.length === 0" class="py-12 text-center text-slate-400">
              <span class="material-icons text-4xl mb-2 opacity-20">event_busy</span>
              <p class="text-sm font-bold uppercase tracking-widest">Tidak ada jadwal hari ini</p>
            </div>
            <div v-for="s in dashboardData.today_schedules" :key="s.id" class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-slate-800 transition-all group">
              <div class="px-3 py-2 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-center min-w-[80px]">
                <div class="text-xs font-black text-primary uppercase">{{ s.start_time.substring(0, 5) }}</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase -mt-1">{{ s.room || 'R.?' }}</div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="font-black text-slate-900 dark:text-white truncate">{{ s.subject?.name }} - {{ s.classroom?.name }}</div>
                <div class="text-xs font-medium text-slate-500">{{ s.notes || 'Tidak ada catatan' }}</div>
              </div>
              <router-link :to="`/guru/classrooms/${s.class_id}`" class="w-10 h-10 rounded-xl bg-primary/5 text-primary opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">
                <span class="material-icons">arrow_forward</span>
              </router-link>
            </div>
          </div>
        </div>

        <!-- Quick Actions / Activity -->
        <div class="space-y-6 sm:space-y-8">
          <div class="bg-primary rounded-[32px] p-8 text-white shadow-[0_20px_40px_-10px_rgba(59,130,246,0.3)] relative overflow-hidden group">
            <div class="relative z-10">
              <h3 class="text-xl font-black mb-2">Butuh Bantuan?</h3>
              <p class="text-sm text-blue-100 font-medium mb-6">Pelajari cara mengelola kelas dan membuat quiz interaktif untuk siswa.</p>
              <button class="bg-white text-primary px-6 py-3 rounded-xl font-black text-sm hover:scale-105 transition-transform shadow-lg">
                Buka Panduan
              </button>
            </div>
            <span class="material-icons absolute -bottom-4 -right-4 text-white/10 text-9xl group-hover:rotate-12 transition-transform">auto_stories</span>
          </div>

          <div class="bg-white dark:bg-surface-dark rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-50 dark:border-slate-800">
              <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Aktivitas Terakhir</h2>
            </div>
            <div class="p-6 space-y-6">
              <div v-if="dashboardData.recent_activities.length === 0" class="py-8 text-center text-slate-400">
                 <p class="text-xs font-bold uppercase tracking-widest opacity-50">Belum ada aktivitas</p>
              </div>
              <div v-for="(act, i) in dashboardData.recent_activities" :key="act.id" class="flex gap-4 relative">
                <div v-if="i < dashboardData.recent_activities.length - 1" class="absolute left-5 top-10 w-0.5 h-8 bg-slate-100 dark:bg-slate-800"></div>
                <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-500 flex items-center justify-center shrink-0">
                  <span class="material-icons text-sm">{{ act.module === 'quiz' ? 'quiz' : 'edit' }}</span>
                </div>
                <div>
                  <div class="text-sm font-bold text-slate-900 dark:text-white leading-tight">{{ act.message }}</div>
                  <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ formatTimeAgo(act.created_at) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../configs/api.js'

const user = ref(null)
const todayLabel = ref('')
const loading = ref(true)
const dashboardData = ref({
  stats: {
    total_classes: 0,
    total_students: 0,
    active_quizzes: 0,
    today_schedules_count: 0
  },
  today_schedules: [],
  recent_activities: []
})

onMounted(async () => {
  const userData = localStorage.getItem('user')
  if (userData) user.value = JSON.parse(userData)

  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  todayLabel.value = new Date().toLocaleDateString('id-ID', options)
  
  await fetchDashboardData()
})

const fetchDashboardData = async () => {
  try {
    const response = await api.get('/guru/dashboard')
    dashboardData.value = response.data
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
  } finally {
    loading.value = false
  }
}

const formatTimeAgo = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffInSeconds = Math.floor((now - date) / 1000)
  
  if (diffInSeconds < 60) return 'Baru saja'
  if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} menit yang lalu`
  if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} jam yang lalu`
  return date.toLocaleDateString('id-ID')
}
</script>
