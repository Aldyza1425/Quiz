<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Riwayat Quiz Anda</h1>
      
      <!-- Selection Actions -->
      <div v-if="attempts.length > 0" class="flex items-center gap-3">
        <button 
          v-if="selectedIds.length > 0"
          @click="confirmDeleteSelected"
          class="inline-flex items-center px-4 py-2.5 bg-red-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-red-600 shadow-lg shadow-red-500/20 transition-all active:scale-95"
        >
          <span class="material-icons text-sm mr-2">delete</span>
          Hapus ({{ selectedIds.length }})
        </button>
        
        <button 
          @click="toggleSelectAll"
          class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-surface-dark border-2 border-slate-100 dark:border-slate-800 text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-300 rounded-xl hover:border-primary/30 transition-all shadow-sm"
        >
          <span class="material-icons text-sm mr-2">{{ isAllSelected ? 'deselect' : 'select_all' }}</span>
          {{ isAllSelected ? 'Batal Pilih' : 'Pilih Semua' }}
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-4">
      <div 
        v-for="attempt in attempts" 
        :key="attempt.id" 
        class="bg-white dark:bg-surface-dark p-6 rounded-[28px] border-2 transition-all relative group"
        :class="selectedIds.includes(attempt.id) ? 'border-primary bg-primary/5 shadow-xl shadow-primary/5' : 'border-slate-100 dark:border-slate-800 hover:shadow-lg'"
      >
        <!-- Checkbox Selection -->
        <div 
          @click="toggleSelection(attempt.id)"
          class="absolute top-4 left-4 z-20 cursor-pointer"
        >
          <div :class="`w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all ${selectedIds.includes(attempt.id) ? 'bg-primary border-primary text-white' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700'}`">
            <span v-if="selectedIds.includes(attempt.id)" class="material-icons text-xs">done</span>
          </div>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pl-10">
          <div class="flex items-center gap-4">
            <div :class="`w-16 h-16 rounded-2xl flex flex-col items-center justify-center font-black ${scoreColor(attempt.score)} bg-opacity-10 text-opacity-100 shadow-sm border border-current border-opacity-10`" :style="{ color: scoreHex(attempt.score), backgroundColor: scoreHex(attempt.score) + '11' }">
              <span class="text-2xl tracking-tighter">{{ Math.round(attempt.score) }}</span>
              <span class="text-[8px] uppercase tracking-widest opacity-50">Skor</span>
            </div>
            <div>
              <span class="px-2.5 py-1 bg-primary text-white text-[10px] font-black uppercase rounded-lg tracking-wider truncate max-w-[120px] mb-2 inline-block shadow-sm shadow-primary/20">
                {{ attempt.quiz?.subject?.name || attempt.quiz?.category?.name || 'UMUM' }}
              </span>
              <h3 class="font-bold text-lg leading-tight text-slate-800 dark:text-white">{{ attempt.quiz?.title }}</h3>
              <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">{{ formatDate(attempt.finished_at) }}</p>
            </div>
          </div>

          <div class="flex items-center gap-6 sm:gap-10">
            <div class="text-center">
              <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ">Benar</div>
              <div class="text-lg font-black text-secondary">{{ attempt.correct_answers }} / {{ attempt.total_questions }}</div>
            </div>
            <div class="text-center">
              <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ">Durasi</div>
              <div class="text-lg font-black text-slate-700 dark:text-slate-200">
                {{ calculateDuration(attempt) }} / {{ attempt.quiz?.duration_minutes || (attempt.quiz?.duration_seconds / 60) }} Menit
              </div>
            </div>
            <button 
              @click="router.push(`/siswa/attempts/${attempt.id}`)"
              class="px-6 py-3 bg-slate-900 dark:bg-primary text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-slate-800 dark:hover:bg-primary-hover shadow-lg transition-all active:scale-95"
            >
              Detail
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="attempts.length === 0 && !loading" class="text-center py-24 bg-white dark:bg-surface-dark rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm">
      <div class="inline-flex items-center justify-center w-20 h-20 rounded-[24px] bg-slate-50 dark:bg-slate-800 text-slate-300 mb-6 group hover:rotate-12 transition-transform duration-500">
        <span class="material-icons text-5xl">history</span>
      </div>
      <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Belum ada riwayat</h3>
      <p class="text-slate-400 mt-2 font-medium">Hasil quiz yang Anda kumpulkan akan muncul di sini.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const attempts = ref([])
const loading = ref(false)
const selectedIds = ref([])

const isAllSelected = computed(() => {
  return attempts.value.length > 0 && selectedIds.value.length === attempts.value.length
})

const toggleSelection = (id) => {
  const index = selectedIds.value.indexOf(id)
  if (index > -1) {
    selectedIds.value.splice(index, 1)
  } else {
    selectedIds.value.push(id)
  }
}

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedIds.value = []
  } else {
    selectedIds.value = attempts.value.map(a => a.id)
  }
}

const confirmDeleteSelected = () => {
  if (confirm(`Hapus ${selectedIds.value.length} riwayat quiz terpilih?`)) {
    deleteSelected()
  }
}

const deleteSelected = async () => {
  try {
    const token = localStorage.getItem('access_token')
    await axios.post('http://localhost:8000/api/siswa/attempts/bulk-delete', {
      ids: selectedIds.value
    }, {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    // Refresh list
    selectedIds.value = []
    fetchAttempts()
  } catch (err) {
    console.error('Delete failed', err)
    alert('Gagal menghapus riwayat')
  }
}

const scoreColor = (score) => {
  if (score >= 80) return 'text-green-600'
  if (score >= 60) return 'text-primary'
  return 'text-red-500'
}

const scoreHex = (score) => {
  if (score >= 80) return '#10b981'
  if (score >= 60) return '#13a4ec'
  return '#ef4444'
}

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' })
}

const calculateDuration = (attempt) => {
  const start = new Date(attempt.started_at)
  const end = new Date(attempt.finished_at)
  return Math.round((end - start) / 60000)
}

const fetchAttempts = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('access_token')
    const response = await axios.get('http://localhost:8000/api/siswa/attempts', {
      headers: { Authorization: `Bearer ${token}` }
    })
    attempts.value = response.data
  } catch (err) {
    console.error('Fetch failed', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchAttempts()
})
</script>
