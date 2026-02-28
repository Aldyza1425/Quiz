<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Aktivitas & Notifikasi</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Pantau semua peristiwa sistem, tindakan Anda, dan pemberitahuan secara real-time.</p>
      </div>
      <div class="flex gap-2">
        <button @click="fetchLogs" class="p-2 bg-white dark:bg-surface-dark border border-slate-100 dark:border-slate-800 rounded-xl text-slate-400 hover:text-primary transition-all shadow-sm">
          <span class="material-icons">refresh</span>
        </button>
      </div>
    </div>

    <!-- Activity Feed -->
    <div class="bg-white dark:bg-surface-dark rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead class="bg-slate-50/50 dark:bg-slate-800/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800">
            <tr>
              <th class="px-6 py-4">Tipe</th>
              <th class="px-6 py-4">Pesan</th>
              <th class="px-6 py-4">Modul</th>
              <th class="px-6 py-4 text-right">Waktu</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
            <!-- Loading States -->
            <template v-if="loading">
              <tr v-for="i in 5" :key="i" class="animate-pulse">
                <td class="px-6 py-5"><div class="h-4 w-16 bg-slate-100 dark:bg-slate-800 rounded-full"></div></td>
                <td class="px-6 py-5"><div class="h-4 w-64 bg-slate-100 dark:bg-slate-800 rounded-lg"></div></td>
                <td class="px-6 py-5"><div class="h-8 w-24 bg-slate-100 dark:bg-slate-800 rounded-lg"></div></td>
                <td class="px-6 py-5"><div class="h-4 w-24 bg-slate-100 dark:bg-slate-800 rounded-lg ml-auto"></div></td>
              </tr>
            </template>

            <template v-else>
              <tr v-for="log in logs" :key="log.id" class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                <td class="px-6 py-5">
                  <span :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-tighter ${statusClass(log.type)}`">
                    {{ log.type }}
                  </span>
                </td>
                <td class="px-6 py-5">
                  <div class="flex flex-col">
                    <span class="text-slate-700 dark:text-slate-200 font-bold text-sm leading-tight">{{ log.message }}</span>
                    <span v-if="log.metadata?.quiz_id" class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">ID: QZ-{{ log.metadata.quiz_id }}</span>
                  </div>
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-400 group-hover:text-primary transition-colors">
                      <span class="material-icons text-sm">{{ moduleIcon(log.module) }}</span>
                    </div>
                    <span class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">{{ log.module }}</span>
                  </div>
                </td>
                <td class="px-6 py-5 text-right">
                  <div class="flex flex-col items-end">
                    <span class="text-xs font-black text-slate-700 dark:text-slate-300">{{ formatDateTime(log.created_at) }}</span>
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter mt-1">{{ timeAgo(log.created_at) }}</span>
                  </div>
                </td>
              </tr>
              <tr v-if="logs.length === 0">
                <td colspan="4" class="px-6 py-20 text-center text-slate-400">
                  <span class="material-icons text-4xl mb-2 opacity-20">history</span>
                  <p class="text-sm font-black uppercase tracking-widest">Belum ada aktivitas yang tercatat.</p>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/configs/api'

const logs = ref([])
const loading = ref(false)

const statusClass = (type) => {
  switch (type) {
    case 'success': return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
    case 'error': return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
    case 'info': return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
    case 'warning': return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
    default: return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400'
  }
}

const moduleIcon = (module) => {
  switch (module) {
    case 'quiz': return 'quiz'
    case 'classroom': return 'school'
    case 'schedule': return 'schedule'
    case 'user': return 'person'
    default: return 'event_note'
  }
}

const formatDateTime = (date) => {
  return new Date(date).toLocaleString('id-ID', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}

const timeAgo = (date) => {
  const seconds = Math.floor((new Date() - new Date(date)) / 1000)
  let interval = Math.floor(seconds / 3600)
  if (interval >= 1) return interval + " jam yang lalu"
  interval = Math.floor(seconds / 60)
  if (interval >= 1) return interval + " menit yang lalu"
  return "Baru saja"
}

const fetchLogs = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('access_token')
    const response = await api.get('/guru/activity-logs')
    logs.value = response.data
  } catch (err) {
    console.error('Gagal mengambil log aktivitas', err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchLogs)
</script>
