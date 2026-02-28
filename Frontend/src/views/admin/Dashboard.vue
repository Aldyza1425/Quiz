<template>
  <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Dashboard Overview</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Selamat datang kembali, berikut ringkasan statistik hari ini.</p>
      </div>

    </div>


    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <router-link 
        v-for="action in quickActions" 
        :key="action.label"
        :to="action.to"
        class="group p-6 bg-white dark:bg-surface-dark border border-slate-100 dark:border-white/5 rounded-[24px] hover:border-primary/50 transition-all hover:shadow-xl hover:shadow-primary/5 text-center sm:text-left overflow-hidden relative"
      >
        <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center mb-4 transition-transform group-hover:scale-110 duration-300 text-2xl', action.bg, action.color]">
          <span class="material-icons text-2xl transition-transform group-hover:rotate-12">{{ action.icon }}</span>
        </div>
        <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-1">{{ action.subtitle }}</h4>
        <p class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-tight">{{ action.label }}</p>
        

        <span class="material-icons absolute -right-4 -bottom-4 text-8xl opacity-[0.03] dark:opacity-[0.05] pointer-events-none group-hover:scale-125 transition-transform duration-700">{{ action.icon }}</span>
      </router-link>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div v-for="stat in metrics" :key="stat.label" class="bg-white dark:bg-surface-dark p-8 rounded-[32px] shadow-sm border border-slate-100 dark:border-white/5 flex items-center justify-between group">
        <div>
          <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em] mb-2">{{ stat.label }}</p>
          <div class="flex items-baseline gap-2">
            <h3 class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">{{ stat.value }}</h3>
            <span class="text-xs font-bold text-slate-400 uppercase">Total</span>
          </div>
        </div>
        <div :class="['w-16 h-16 rounded-[24px] flex items-center justify-center transition-all duration-500 group-hover:rounded-full group-hover:rotate-12 text-2xl', stat.bg]">
          <span :class="`material-icons ${stat.color} text-3xl`">{{ stat.icon }}</span>
        </div>
      </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

      <div class="lg:col-span-3 bg-white dark:bg-surface-dark rounded-[32px] shadow-sm border border-slate-100 dark:border-white/5 overflow-hidden">
        <div class="px-8 py-7 border-b border-slate-50 dark:border-white/5 flex items-center justify-between">
          <div>
            <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Aktivitas Terbaru</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Riwayat pengerjaan quiz siswa</p>
          </div>
          <router-link class="text-xs font-black text-primary uppercase tracking-widest hover:underline" to="/admin/reports">Lihat Semua</router-link>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
            <thead class="bg-slate-50/50 dark:bg-white/5 text-[10px] font-black uppercase tracking-widest text-slate-400">
              <tr>
                <th class="px-8 py-5">User</th>
                <th class="px-8 py-5">Quiz</th>
                <th class="px-8 py-5">Skor</th>
                <th class="px-8 py-5 text-right">Waktu</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
              <tr v-for="act in recentActivity" :key="act.time" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                <td class="px-8 py-5">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-black uppercase">
                      {{ act.user.charAt(0) }}
                    </div>
                    <span class="font-bold text-slate-900 dark:text-slate-100">{{ act.user }}</span>
                  </div>
                </td>
                <td class="px-8 py-5 font-medium">{{ act.quiz }}</td>
                <td class="px-8 py-5">
                   <span :class="`inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${act.score >= 70 ? 'bg-secondary/10 text-secondary' : 'bg-red-100/50 text-red-500'}`">
                    {{ act.score }}%
                  </span>
                </td>
                <td class="px-8 py-5 text-right text-slate-400 font-bold uppercase text-[9px] tracking-tighter">{{ act.time }}</td>
              </tr>
              <tr v-if="recentActivity.length === 0">
                <td colspan="4" class="px-8 py-12 text-center text-slate-400 font-bold uppercase tracking-widest">
                  Belum ada aktivitas
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>


      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-surface-dark rounded-[32px] shadow-sm border border-slate-100 dark:border-white/5 overflow-hidden">
          <div class="px-8 py-7 border-b border-slate-50 dark:border-white/5">
            <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Top Performing</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Quiz paling populer & aktif</p>
          </div>
          <div class="p-4 space-y-3">
            <div v-for="(quiz, index) in topQuizzes" :key="quiz.id" class="flex items-center justify-between p-4 rounded-2xl hover:bg-slate-50 dark:hover:bg-white/5 transition-all group border border-transparent hover:border-slate-100 dark:hover:border-white/5">
              <div class="flex items-center gap-4">
                <div :class="`w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs ${index === 0 ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'}`">
                  {{ index + 1 }}
                </div>
                <div>
                  <h4 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-1">{{ quiz.title }}</h4>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ quiz.category }}</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm font-black text-primary">{{ quiz.participants }}</p>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Peserta</p>
              </div>
            </div>
            
            <div v-if="topQuizzes.length === 0" class="py-12 text-center">
              <span class="material-icons text-4xl text-slate-200 mb-2">auto_awesome</span>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Data belum tersedia</p>
            </div>
          </div>
        </div>


        <div class="bg-primary p-8 rounded-[32px] text-white relative overflow-hidden group shadow-lg shadow-primary/20">
          <div class="relative z-10">
            <h3 class="text-xl font-black uppercase tracking-tight mb-2">Butuh Bantuan?</h3>
            <p class="text-white/80 text-xs font-medium mb-6 leading-relaxed">Hubungi administrator teknis jika Anda menemui kendala dalam akses sistem.</p>
            <button class="px-6 py-3 bg-white text-primary rounded-xl text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-transform active:scale-95 shadow-xl">Kontak CS</button>
          </div>
          <span class="material-icons absolute -right-6 -bottom-6 text-[180px] text-white opacity-10 pointer-events-none group-hover:scale-110 group-hover:rotate-12 transition-transform duration-700">support_agent</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/configs/api'

const metrics = ref([
  { label: 'Total Guru', value: '0', icon: 'school', bg: 'bg-primary/10', color: 'text-primary' },
  { label: 'Total Siswa', value: '0', icon: 'groups', bg: 'bg-secondary/10', color: 'text-secondary' },
  { label: 'Total Quiz', value: '0', icon: 'quiz', bg: 'bg-blue-100/50 dark:bg-blue-900/20', color: 'text-blue-500' },
])

const quickActions = [
  { label: 'Guru Baru', subtitle: 'Tambah', to: '/admin/teachers/add', icon: 'person_add', bg: 'bg-primary/10', color: 'text-primary' },
  { label: 'Siswa Baru', subtitle: 'Registrasi', to: '/admin/students/add', icon: 'group_add', bg: 'bg-secondary/10', color: 'text-secondary' },
  { label: 'Tambah Kelas', subtitle: 'Manajemen', to: '/admin/classrooms/add', icon: 'meeting_room', bg: 'bg-amber-100/50 dark:bg-amber-900/20', color: 'text-amber-500' },
  { label: 'Update Identitas', subtitle: 'Sekolah', to: '/admin/settings', icon: 'settings', bg: 'bg-slate-100 dark:bg-slate-800', color: 'text-slate-500' },
]

const recentActivity = ref([])
const topQuizzes = ref([])

onMounted(async () => {
  try {
    const token = localStorage.getItem('access_token')
    const response = await api.get('/admin/dashboard')
    
    const stats = response.data.stats
    metrics.value[0].value = stats.total_guru || 0
    metrics.value[1].value = stats.total_siswa || 0
    metrics.value[2].value = stats.total_quiz || 0
    
    recentActivity.value = response.data.recent_activity || []
    topQuizzes.value = response.data.top_quizzes || []
  } catch (err) {
    console.error('Gagal mengambil data dashboard', err)
  }
})
</script>
