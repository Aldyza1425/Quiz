<template>
  <div v-if="attempt" class="space-y-8 pb-12">
    <!-- Header with Stats -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 dark:from-primary dark:to-sky-700 rounded-3xl p-8 sm:p-12 text-white shadow-2xl relative overflow-hidden">
      <div class="relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
          <div class="space-y-4">
            <span class="px-4 py-1.5 bg-white/10 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-[0.2em] text-sky-200">
              Hasil Quiz
            </span>
            <h1 class="text-3xl sm:text-5xl font-black tracking-tighter uppercase leading-none">
              {{ attempt.quiz?.title }}
            </h1>
            <div class="flex items-center gap-4 text-sky-100/70 text-sm font-bold uppercase tracking-widest">
              <span class="flex items-center gap-2">
                <span class="material-icons text-lg">school</span>
                {{ attempt.quiz?.subject?.name || 'UMUM' }}
              </span>
              <span class="w-1.5 h-1.5 rounded-full bg-sky-400/30"></span>
              <span class="flex items-center gap-2">
                <span class="material-icons text-lg">event</span>
                {{ formatDate(attempt.finished_at) }}
              </span>
            </div>
          </div>

          <div class="flex items-center gap-6 sm:gap-10">
            <div class="text-center group">
              <div class="text-[10px] font-black text-sky-300/50 uppercase tracking-[0.2em] mb-3 group-hover:text-sky-300 transition-colors">Skor Akhir</div>
              <div class="text-5xl sm:text-7xl font-black tracking-tighter tabular-nums drop-shadow-xl">{{ Math.round(attempt.score) }}</div>
            </div>
            <div class="w-px h-16 bg-white/10 hidden sm:block"></div>
            <div class="space-y-4">
               <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-green-500/20 text-green-400 flex items-center justify-center">
                    <span class="material-icons">check_circle</span>
                  </div>
                  <div>
                    <div class="text-[10px] font-black text-sky-300/50 uppercase tracking-widest">Benar</div>
                    <div class="text-lg font-black tracking-tight">{{ attempt.correct_answers }} / {{ attempt.total_questions }}</div>
                  </div>
               </div>
               <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center">
                    <span class="material-icons">cancel</span>
                  </div>
                  <div>
                    <div class="text-[10px] font-black text-sky-300/50 uppercase tracking-widest">Durasi</div>
                    <div class="text-lg font-black tracking-tight">
                      {{ calculateDuration(attempt) }} / {{ attempt.quiz?.duration_minutes || (attempt.quiz?.duration_seconds / 60) }} Menit
                    </div>
                  </div>
               </div>
            </div>
          </div>
        </div>
      </div>
      <span class="material-icons absolute -bottom-16 -right-16 text-[300px] text-white/5 select-none pointer-events-none rotate-12">assignment_turned_in</span>
    </div>

    <!-- Navigation Back -->
    <div class="flex items-center gap-4">
      <button 
        @click="router.push('/siswa/attempts')"
        class="inline-flex items-center gap-3 px-6 py-3 bg-white dark:bg-surface-dark border-2 border-slate-100 dark:border-slate-800 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-600 dark:text-slate-300 hover:border-primary/30 transition-all active:scale-95 shadow-sm"
      >
        <span class="material-icons text-lg text-primary">arrow_back</span>
        Kembali ke Riwayat
      </button>
    </div>

    <!-- Questions Breakdown -->
    <div class="space-y-6">
      <h2 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-3">
        <span class="w-2 h-8 bg-primary rounded-full"></span>
        Analisis Jawaban
      </h2>

      <div class="grid grid-cols-1 gap-6">
        <div 
          v-for="(question, idx) in attempt.quiz.questions" 
          :key="question.id"
          class="bg-white dark:bg-surface-dark rounded-3xl border-2 border-slate-100 dark:border-slate-800 transition-all p-6 sm:p-10 relative overflow-hidden"
          :class="{ 
            'border-green-500/30 bg-green-50/50 dark:bg-green-500/5': question.type !== 'short_answer' && isAnswerCorrect(question.id), 
            'border-red-500/30 bg-red-50/50 dark:bg-red-500/5': question.type !== 'short_answer' && !isAnswerCorrect(question.id) 
          }"
        >
          <div class="relative z-10 flex flex-col md:flex-row gap-8">
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-6">
                <span class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-sm font-black text-slate-500">{{ idx + 1 }}</span>
                <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-full text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ formatType(question.type) }}</span>
                <template v-if="question.type !== 'short_answer'">
                  <span v-if="isAnswerCorrect(question.id)" class="px-3 py-1 bg-green-500 text-white rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-1">
                    <span class="material-icons text-xs">done</span> Benar
                  </span>
                  <span v-else class="px-3 py-1 bg-red-500 text-white rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-1">
                    <span class="material-icons text-xs">close</span> Salah
                  </span>
                </template>
              </div>

              <h3 class="text-lg sm:text-xl font-bold text-slate-800 dark:text-white mb-8 leading-relaxed">
                {{ question.question_text }}
              </h3>

              <!-- Options for MC -->
              <div v-if="question.type !== 'short_answer'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div 
                  v-for="opt in question.options" 
                  :key="opt.id"
                  class="p-4 rounded-2xl border-2 flex items-center gap-4 transition-all"
                  :class="getOptionClass(question.id, opt)"
                >
                  <div :class="`w-8 h-8 rounded-full border-2 flex items-center justify-center text-xs font-black shrink-0 ${getOptionIconClass(question.id, opt)}`">
                    <span v-if="opt.is_correct" class="material-icons text-sm">check</span>
                    <span v-else-if="getUserAnswer(question.id) === opt.id" class="material-icons text-sm">close</span>
                    <span v-else>{{ String.fromCharCode(65 + (question.options.indexOf(opt))) }}</span>
                  </div>
                  <span class="font-bold text-sm">{{ opt.option_text }}</span>
                </div>
              </div>

              <!-- Answer for Short Answer -->
              <div v-else class="space-y-4">
                <div class="p-5 bg-slate-100 dark:bg-slate-800 rounded-2xl border-2 border-slate-200 dark:border-slate-700">
                  <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Jawaban Anda</div>
                  <div class="font-bold text-base sm:text-lg">{{ getUserShortAnswer(question.id) || '-' }}</div>
                </div>
              </div>
            </div>

            <!-- Side Info -->
            <div class="w-full md:w-48 shrink-0 flex flex-col justify-center gap-4 bg-white/50 dark:bg-black/10 p-6 rounded-2xl border border-slate-100 dark:border-white/5">
              <div class="text-center">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Poin Diperoleh</div>
                <div class="text-2xl font-black text-primary">{{ getPointsEarned(question.id) }} <span class="text-xs text-slate-400">/ {{ question.points }}</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/configs/api'

const router = useRouter()
const route = useRoute()
const attempt = ref(null)

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' })
}

const calculateDuration = (att) => {
  const start = new Date(att.started_at)
  const end = new Date(att.finished_at)
  return Math.round((end - start) / 60000)
}

const formatType = (type) => {
  const types = {
    multiple_choice: 'Pilihan Ganda',
    true_false: 'Benar / Salah',
    short_answer: 'Jawaban Singkat'
  }
  return types[type] || type
}

const isAnswerCorrect = (questionId) => {
  const answer = attempt.value.answers.find(a => a.question_id === questionId)
  return answer ? !!answer.is_correct : false
}

const getUserAnswer = (questionId) => {
  const answer = attempt.value.answers.find(a => a.question_id === questionId)
  return answer ? answer.selected_option_id : null
}

const getUserShortAnswer = (questionId) => {
  const answer = attempt.value.answers.find(a => a.question_id === questionId)
  return answer ? answer.short_answer : null
}

const getPointsEarned = (questionId) => {
  const answer = attempt.value.answers.find(a => a.question_id === questionId)
  return answer ? answer.points_earned : 0
}

const getOptionClass = (questionId, opt) => {
  const userId = getUserAnswer(questionId)
  
  if (opt.is_correct) return 'border-green-500 bg-green-500/10 text-green-700 dark:text-green-400'
  if (userId === opt.id) return 'border-red-500 bg-red-500/10 text-red-700 dark:text-red-400'
  
  return 'border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 text-slate-500'
}

const getOptionIconClass = (questionId, opt) => {
  const userId = getUserAnswer(questionId)
  
  if (opt.is_correct) return 'bg-green-500 border-green-500 text-white'
  if (userId === opt.id) return 'bg-red-500 border-red-500 text-white'
  
  return 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-400'
}

onMounted(async () => {
  try {
    const token = localStorage.getItem('access_token')
    const response = await api.get(`/siswa/attempts/${route.params.id}`)
    attempt.value = response.data
  } catch (err) {
    console.error('Fetch attempt details failed', err)
    router.push('/siswa/attempts')
  }
})
</script>

<style scoped>
.scrollbar-none::-webkit-scrollbar { display: none; }
.scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>
