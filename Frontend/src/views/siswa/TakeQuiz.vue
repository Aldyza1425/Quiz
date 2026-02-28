<template>
  <div v-if="attempt" class="h-screen flex flex-col bg-slate-50 dark:bg-background-dark text-slate-800 dark:text-slate-100 overflow-hidden">

    <header class="bg-white dark:bg-surface-dark border-b border-slate-200 dark:border-slate-800 h-20 shrink-0 flex items-center px-4 sm:px-8 z-20">
      <div class="flex items-center gap-3 sm:gap-4 w-1/2 md:w-1/4">
        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
          <span class="material-icons text-xl sm:text-2xl">school</span>
        </div>
        <div class="min-w-0">
          <h2 class="font-bold text-xs sm:text-sm leading-tight truncate">{{ attempt.quiz?.title }}</h2>
          <p class="text-[9px] sm:text-[10px] text-slate-400 font-bold uppercase tracking-wider truncate">Sekolah Media Belajar</p>
        </div>
      </div>

      <div class="hidden md:flex flex-1 flex-col items-center px-10">
        <div class="w-full flex justify-between text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">
          <span>Progress</span>
          <span>{{ Math.round((answeredCount / totalQuestions) * 100) }}%</span>
        </div>
        <div class="w-full h-3 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden p-0.5">
          <div 
            class="h-full bg-secondary rounded-full transition-all duration-500 shadow-sm"
            :style="{ width: `${(answeredCount / totalQuestions) * 100}%` }"
          ></div>
        </div>
      </div>

      <div class="w-1/2 md:w-1/4 flex justify-end gap-2">
        <button @click="isSidebarOpen = true" class="md:hidden p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
          <span class="material-icons">menu_open</span>
        </button>
      </div>
    </header>

    <main class="flex-1 flex overflow-hidden relative">

      <section class="flex-1 overflow-y-auto p-4 sm:p-12 flex flex-col items-center relative">
        <div class="max-w-3xl w-full">

          <div class="flex items-center justify-between mb-4 sm:mb-8">
            <span class="px-3 py-1 sm:px-4 sm:py-1.5 bg-primary/10 text-primary rounded-full text-[10px] sm:text-xs font-black uppercase tracking-widest">Question {{ currentIdx + 1 }} of {{ totalQuestions }}</span>
          </div>


          <div class="bg-white dark:bg-surface-dark border-2 border-slate-100 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-10 shadow-sm mb-6 sm:mb-10 transition-all">

            <div v-if="currentQuestion?.media_type && currentQuestion.media_type !== 'none'" class="mb-6 rounded-2xl overflow-hidden bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800">

               <img v-if="currentQuestion.media_type === 'image'" :src="getFullUrl(currentQuestion.media_url)" class="w-full h-auto max-h-[400px] object-contain mx-auto" alt="Question Media" />
               

               <video v-else-if="currentQuestion.media_type === 'video'" :src="getFullUrl(currentQuestion.media_url)" controls class="w-full max-h-[400px]" preload="metadata"></video>
               

               <div v-else-if="currentQuestion.media_type === 'youtube'" class="aspect-video w-full">
                  <iframe 
                    :src="getYoutubeEmbedUrl(currentQuestion)" 
                    class="w-full h-full" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen
                  ></iframe>
               </div>
            </div>

            <h3 class="text-lg sm:text-2xl font-bold leading-relaxed mb-6 sm:mb-8">{{ currentQuestion?.question_text }}</h3>


            <div v-if="currentQuestion?.type !== 'short_answer'" class="grid grid-cols-1 gap-3 sm:gap-4">
               <div 
                v-for="(opt, oIdx) in (currentQuestion?.options || [])" 
                :key="opt.id"
                @click="selectAnswer(opt.id)"
                :class="`group flex items-center gap-3 sm:gap-6 p-4 sm:p-5 rounded-xl sm:rounded-2xl border-2 transition-all cursor-pointer ${answers[currentQuestion.id] === opt.id ? 'bg-primary/5 border-primary ring-2 sm:ring-4 ring-primary/5' : 'bg-slate-50/30 dark:bg-slate-800/20 border-slate-100 dark:border-slate-800 hover:border-primary/40'}`"
               >
                  <div :class="`w-8 h-8 sm:w-10 sm:h-10 rounded-full border-2 flex items-center justify-center font-black text-xs sm:text-sm shrink-0 transition-all ${answers[currentQuestion.id] === opt.id ? 'bg-primary border-primary text-white scale-110' : 'bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-700 text-slate-400 group-hover:border-primary/50'}`">
                    {{ String.fromCharCode(65 + oIdx) }}
                  </div>
                  <div class="flex-1 font-bold text-sm sm:text-lg">{{ opt.option_text }}</div>
                  <div v-if="answers[currentQuestion.id] === opt.id" class="text-primary">
                    <span class="material-icons text-lg sm:text-2xl">check_circle</span>
                  </div>
               </div>
            </div>


            <div v-if="currentQuestion?.type === 'short_answer'" class="mt-4">
              <textarea 
                v-model="answers[currentQuestion.id]"
                @blur="saveAnswer(currentQuestion.id, null, answers[currentQuestion.id])"
                class="w-full p-4 sm:p-6 bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl sm:rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary text-base sm:text-xl font-bold transition-all"
                placeholder="Tulis jawaban Anda di sini..."
                rows="4"
              ></textarea>
            </div>
          </div>
        </div>


        <div class="mt-auto max-w-3xl w-full flex items-center justify-between pb-6 sm:pb-10 gap-3">
          <button 
            @click="prevQuestion" 
            :disabled="currentIdx === 0"
            class="flex-1 sm:flex-none px-4 sm:px-8 py-3 sm:py-3.5 rounded-xl sm:rounded-2xl border-2 border-slate-200 dark:border-slate-800 font-bold flex items-center justify-center gap-2 sm:gap-3 hover:bg-white dark:hover:bg-surface-dark transition-all disabled:opacity-30 disabled:pointer-events-none text-xs sm:text-base"
          >
            <span class="material-icons text-sm sm:text-lg">arrow_back</span> <span class="hidden xs:inline">Previous</span>
          </button>
          <button 
            @click="nextQuestion" 
            v-if="currentIdx < totalQuestions - 1"
            class="flex-1 sm:flex-none px-4 sm:px-8 py-3 sm:py-3.5 rounded-xl sm:rounded-2xl bg-secondary hover:bg-secondary/90 text-white font-bold flex items-center justify-center gap-2 sm:gap-3 shadow-lg shadow-secondary/30 transition-all text-xs sm:text-base ml-auto"
          >
            <span class="hidden xs:inline">Next Question</span> <span class="material-icons text-sm sm:text-lg">arrow_forward</span>
          </button>
        </div>
      </section>


      <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="md:hidden absolute inset-0 bg-slate-900/40 backdrop-blur-sm z-30"></div>


      <aside :class="`fixed md:static inset-y-0 right-0 z-40 w-80 bg-white dark:bg-surface-dark border-l border-slate-200 dark:border-slate-800 flex flex-col p-6 sm:p-8 transition-transform duration-300 transform ${isSidebarOpen ? 'translate-x-0' : 'translate-x-full md:translate-x-0'}`">

        <button @click="isSidebarOpen = false" class="md:hidden absolute top-4 left-4 text-slate-400">
          <span class="material-icons text-2xl">close</span>
        </button>


        <div class="mb-8 sm:mb-12 mt-8 md:mt-0">
          <div class="flex items-center justify-between mb-4 text-slate-400">
             <span class="text-[10px] sm:text-[10px] font-black uppercase tracking-[0.2em]">Time Remaining</span>
             <span class="material-icons text-lg">watch_later</span>
          </div>
          <div class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tighter mb-2">{{ formatTime(timeLeft) }}</div>
          <div class="flex items-center gap-2">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Time is running</span>
          </div>
        </div>


        <div class="flex-1 flex flex-col min-h-0">
          <div class="flex items-center justify-between mb-6">
             <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Question Map</span>
          </div>
          <div class="grid grid-cols-4 gap-y-4 gap-x-3 overflow-y-auto p-2 pb-6 scrollbar-none justify-items-center">
            <button 
              v-for="(q, idx) in attempt.quiz.questions" 
              :key="q.id"
              @click="currentIdx = idx; isSidebarOpen = false"
              :class="`h-12 w-12 rounded-2xl flex items-center justify-center text-sm font-black transition-all relative ${mapStateClass(idx)}`"
            >
              {{ idx + 1 }}
            </button>
          </div>
        </div>

        <button @click="finishQuiz" class="mt-8 sm:mt-10 w-full py-4 sm:py-5 bg-[#1a2c35] hover:bg-slate-800 text-white rounded-2xl font-black text-xs sm:text-sm uppercase tracking-widest flex items-center justify-center gap-3 transition-all shadow-xl shadow-slate-900/10 active:scale-95 group shrink-0">
          Submit Quiz <span class="material-icons group-hover:translate-x-1 transition-transform">send</span>
        </button>
        <p class="text-[9px] text-center text-slate-400 mt-4 font-medium uppercase tracking-tighter shrink-0">All answers will be saved automatically.</p>
      </aside>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import { useNotificationStore } from '../../stores/notification'

const router = useRouter()
const route = useRoute()
const notification = useNotificationStore()

const attempt = ref(null)
const currentIdx = ref(0)
const answers = ref({})
const timeLeft = ref(0)
const timer = ref(null)
const isSidebarOpen = ref(false)

const currentQuestion = computed(() => attempt.value?.quiz?.questions[currentIdx.value])
const totalQuestions = computed(() => attempt.value?.quiz?.questions?.length || 0)
const answeredCount = computed(() => Object.values(answers.value).filter(v => v !== null && v !== '').length)

const formatTime = (seconds) => {
  const h = Math.floor(seconds / 3600)
  const m = Math.floor((seconds % 3600) / 60)
  const s = seconds % 60
  return [h, m, s].map(v => v.toString().padStart(2, '0')).join(':')
}

const mapStateClass = (idx) => {
  const qId = attempt.value.quiz.questions[idx].id
  if (currentIdx.value === idx) return 'bg-primary text-white ring-4 ring-primary/20 scale-110 z-10 shadow-lg'
  if (answers.value[qId]) return 'bg-green-500 text-white'
  return 'bg-slate-100 text-slate-400 dark:bg-slate-800'
}

const selectAnswer = (optionId) => {
  const qId = currentQuestion.value.id
  answers.value[qId] = optionId
  saveAnswer(qId, optionId)
}

const saveAnswer = async (questionId, optionId, shortAnswer = null) => {
  try {
    const token = localStorage.getItem('access_token')
    await axios.post(`http://localhost:8000/api/siswa/attempts/${attempt.value.id}/answer`, {
      question_id: questionId,
      selected_option_id: optionId,
      short_answer: shortAnswer
    }, { headers: { Authorization: `Bearer ${token}` } })
  } catch (err) {
    console.error('Save answer failed', err)
  }
}

const nextQuestion = () => {
  if (currentIdx.value < totalQuestions.value - 1) currentIdx.value++
}

const prevQuestion = () => {
  if (currentIdx.value > 0) currentIdx.value--
}

const handleBeforeUnload = (e) => {
  e.preventDefault()
  e.returnValue = ''
}

const cleanup = () => {
  if (timer.value) clearInterval(timer.value)
  window.removeEventListener('beforeunload', handleBeforeUnload)
  localStorage.removeItem(`quiz_index_${route.params.id}`)
}

const finishQuiz = async (isAuto = false) => {
  if (!isAuto && answeredCount.value < totalQuestions.value) {
    notification.add({
      title: 'Belum Selesai!',
      message: `Anda baru menjawab ${answeredCount.value} dari ${totalQuestions.value} soal. Silakan jawab semua soal sebelum mengumpulkan.`,
      type: 'error'
    })
    return
  }

  if (!isAuto) {
    const confirmed = await notification.confirm({
      title: 'Kumpulkan Quiz?',
      message: 'Anda yakin ingin mengumpulkan quiz ini? Pastikan semua jawaban sudah benar.',
      confirmText: 'Ya, Kumpulkan',
      cancelText: 'Cek Kembali'
    })

    if (!confirmed) return
  }

  try {
    const token = localStorage.getItem('access_token')
    await axios.post(`http://localhost:8000/api/siswa/attempts/${attempt.value.id}/finish`, {}, {
      headers: { Authorization: `Bearer ${token}` }
    })
    cleanup()
    router.push('/siswa/attempts')
  } catch (err) {
    console.error('Finish quiz failed', err)
  }
}

const confirmExit = async () => {
  if (answeredCount.value < totalQuestions.value) {
    notification.add({
      title: 'Ups!',
      message: 'Selesaikan semua soal dulu ya sebelum keluar. Sedikit lagi!',
      type: 'info'
    })
    return
  }
  
  const confirmed = await notification.confirm({
    title: 'Keluar dari Quiz?',
    message: 'Progres Anda tersimpan namun waktu akan terus berjalan. Yakin ingin keluar?',
    confirmText: 'Ya, Keluar',
    cancelText: 'Lanjutkan'
  })

  if (confirmed) {
    cleanup()
    router.push('/siswa/quizzes')
  }
}

const getFullUrl = (url) => {
  if (!url) return ''
  if (url.startsWith('http')) return url
  return `http://localhost:8000${url}`
}

const getYouTubeId = (url) => {
  if (!url) return ''
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/
  const match = url.match(regExp)
  return (match && match[2].length === 11) ? match[2] : null
}

const getYoutubeEmbedUrl = (q) => {
  const id = getYouTubeId(q.media_url)
  if (!id) return ''
  let url = `https://www.youtube.com/embed/${id}?`
  if (q.media_start) url += `start=${q.media_start}&`
  if (q.media_end) url += `end=${q.media_end}&`
  return url
}

onMounted(async () => {
  notification.clearAll()
  try {
    const token = localStorage.getItem('access_token')
    const response = await axios.post(`http://localhost:8000/api/siswa/quizzes/${route.params.id}/attempt`, {}, {
      headers: { Authorization: `Bearer ${token}` }
    })
    attempt.value = response.data.attempt
    
    // Timer Persistence Logic
    const startTime = new Date(attempt.value.started_at).getTime()
    const now = new Date().getTime()
    const elapsedSeconds = Math.floor((now - startTime) / 1000)
    const durationSeconds = attempt.value.quiz.duration_seconds || (attempt.value.quiz.duration_minutes * 60)
    
    timeLeft.value = Math.max(0, durationSeconds - elapsedSeconds)

    // Index Persistence
    const savedIdx = localStorage.getItem(`quiz_index_${route.params.id}`)
    if (savedIdx !== null) {
      currentIdx.value = parseInt(savedIdx)
    }

    // Load existing answers
    if (attempt.value.answers) {
      attempt.value.answers.forEach(a => {
        answers.value[a.question_id] = a.short_answer || a.selected_option_id
      })
    }

    window.addEventListener('beforeunload', handleBeforeUnload)

    timer.value = setInterval(() => {
      if (timeLeft.value > 0) {
        timeLeft.value--
      } else {
        finishQuiz(true)
      }
    }, 1000)
  } catch (err) {
    console.error('Start attempt failed', err)
    router.push('/siswa/quizzes')
  }
})

onUnmounted(() => {
  if (timer.value) clearInterval(timer.value)
  window.removeEventListener('beforeunload', handleBeforeUnload)
})
</script>

<style scoped>
.scrollbar-none::-webkit-scrollbar { display: none; }
.scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>
