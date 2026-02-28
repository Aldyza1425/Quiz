<template>
  <div class="space-y-8">
    <div class="bg-gradient-to-r from-primary to-sky-600 rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
      <div class="relative z-10 max-w-2xl">
        <h1 class="text-3xl font-bold mb-4">Mari asah kemampuanmu! 🚀</h1>
        <p class="text-sky-100 text-lg">Pilih quiz yang tersedia dan tunjukkan hasil belajar terbaikmu hari ini.</p>
      </div>
      <span class="material-icons absolute -bottom-10 -right-10 text-[200px] text-white/10 select-none">rocket_launch</span>
    </div>

    <!-- Category Filters -->
    <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-none">
      <button 
        @click="selectedSubject = null"
        :class="`px-5 py-2 rounded-full text-sm font-semibold transition-all whitespace-nowrap ${!selectedSubject ? 'bg-primary text-white shadow-md' : 'bg-white dark:bg-surface-dark hover:bg-slate-50'}`"
      >
        Semua
      </button>
      <button 
        v-for="sub in subjects" 
        :key="sub.id"
        @click="selectedSubject = sub.id"
        :class="`px-5 py-2 rounded-full text-sm font-semibold transition-all whitespace-nowrap ${selectedSubject === sub.id ? 'bg-primary text-white shadow-md' : 'bg-white dark:bg-surface-dark hover:bg-slate-50'}`"
      >
        {{ sub.name }}
      </button>
    </div>

    <!-- Quiz Lists -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div v-for="quiz in filteredQuizzes" :key="quiz.id" class="bg-white dark:bg-surface-dark rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all border border-slate-100 dark:border-slate-800 flex flex-col group">
        <div class="h-3 w-full bg-primary/20 group-hover:bg-primary transition-colors"></div>
        <div class="p-6 flex-1 flex flex-col">
          <div class="flex items-center justify-between mb-4">
            <span class="px-2.5 py-1 bg-primary text-white text-[10px] font-black uppercase rounded-lg tracking-wider truncate max-w-[120px] shadow-sm shadow-primary/20">{{ quiz.subject?.name || quiz.category?.name || 'UMUM' }}</span>
            <div class="flex items-center gap-1 text-[10px] text-slate-400">
              <span class="material-icons text-sm">schedule</span>
              {{ formatDuration(quiz.duration_seconds || quiz.duration_minutes * 60) }}
            </div>
          </div>
          
          <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-3 leading-snug group-hover:text-primary transition-colors">{{ quiz.title }}</h3>
          <p class="text-slate-500 dark:text-slate-400 text-sm line-clamp-3 mb-6 flex-1">{{ quiz.description }}</p>
          
          <div class="flex items-center justify-between mt-auto">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-400 uppercase">
                {{ quiz.creator?.name?.charAt(0) }}
              </div>
              <span class="text-xs text-slate-500">{{ quiz.creator?.name }}</span>
            </div>
            <button @click="startQuiz(quiz.id)" class="px-6 py-2.5 bg-slate-900 dark:bg-primary text-white text-sm font-bold rounded-xl hover:bg-slate-800 dark:hover:bg-primary-hover shadow-lg transition-all">
              MULAI
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/configs/api'

const router = useRouter()
const quizzes = ref([])
const subjects = ref([])
const selectedSubject = ref(null)

const filteredQuizzes = computed(() => {
  if (!selectedSubject.value) return quizzes.value
  return quizzes.value.filter(q => q.subject_id === selectedSubject.value)
})

const formatDuration = (seconds) => {
  if (!seconds) return '0 Detik'
  const h = Math.floor(seconds / 3600)
  const m = Math.floor((seconds % 3600) / 60)
  const s = seconds % 60
  
  let result = []
  if (h > 0) result.push(`${h} Jam`)
  if (m > 0) result.push(`${m} Menit`)
  if (s > 0) result.push(`${s} Detik`)
  
  return result.join(' ')
}

const startQuiz = (id) => {
  router.push(`/siswa/quiz/${id}`)
}

onMounted(async () => {
  try {
    const token = localStorage.getItem('access_token')
    const [qRes, sRes] = await Promise.all([
      api.get('/siswa/quizzes'),
      api.get('/siswa/quizzes/subjects')
    ])
    quizzes.value = qRes.data
    subjects.value = sRes.data
  } catch (err) {
    console.error('Fetch failed', err)
  }
})
</script>
