<template>
  <div class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-100 min-h-screen flex flex-col antialiased selection:bg-primary/30 relative overflow-hidden">
    <!-- Background Patterns -->
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
      <div class="absolute top-0 left-0 w-full h-full opacity-40 dark:opacity-10 bg-[radial-gradient(#13a4ec_1px,transparent_1px)] [background-size:20px_20px]"></div>
      <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-primary/10 rounded-full blur-[120px]"></div>
      <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary/15 rounded-full blur-[100px]"></div>
    </div>

    <!-- Main Content -->
    <main class="relative z-10 flex-grow flex items-center justify-center p-4 sm:p-6 lg:p-8">
      <div class="w-full max-w-xl bg-white dark:bg-[#1a2c35] rounded-[40px] shadow-2xl border border-slate-100 dark:border-slate-800 overflow-hidden transition-all duration-300">
        
        <!-- Progress Header -->
        <div class="pt-10 pb-6 px-10 text-center relative overflow-hidden">
           <div class="absolute top-0 left-0 w-full h-1.5 flex gap-1 px-4">
              <div :class="`h-full flex-1 rounded-full transition-all duration-500 ${step >= 1 ? 'bg-primary' : 'bg-slate-100 dark:bg-slate-800'}`"></div>
              <div :class="`h-full flex-1 rounded-full transition-all duration-500 ${step >= 2 ? 'bg-primary' : 'bg-slate-100 dark:bg-slate-800'}`"></div>
              <div :class="`h-full flex-1 rounded-full transition-all duration-500 ${step >= 3 ? 'bg-primary' : 'bg-slate-100 dark:bg-slate-800'}`"></div>
           </div>

           <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary/10 text-primary mb-4 ring-8 ring-primary/5">
              <span class="material-icons text-3xl">{{ stepIcons[step] }}</span>
           </div>
           <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight leading-none">{{ stepTitles[step] }}</h2>
           <p class="mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">{{ stepSubtitles[step] }}</p>
        </div>

        <!-- Registration Steps -->
        <div class="px-10 pb-12">
           <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl text-[11px] font-bold flex items-center gap-3">
              <span class="material-icons text-base">error_outline</span>
              {{ error }}
           </div>

           <Transition
             enter-active-class="transition duration-300 ease-out"
             enter-from-class="opacity-0 translate-x-12"
             enter-to-class="opacity-100 translate-x-0"
             leave-active-class="transition duration-200 ease-in"
             leave-from-class="opacity-100 translate-x-0"
             leave-to-class="opacity-0 -translate-x-12"
             mode="out-in"
           >
              <!-- STEP 1: Akun -->
              <div v-if="step === 1" key="step1" class="space-y-4">
                 <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Nama Lengkap</label>
                    <input v-model="form.name" type="text" placeholder="Masukkan nama lengkap..." class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary transition-all shadow-inner" />
                 </div>
                 <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Alamat Email</label>
                    <input v-model="form.email" type="email" placeholder="email@sekolah.com" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary transition-all shadow-inner" />
                 </div>
                 <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                       <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Password</label>
                       <input v-model="form.password" type="password" placeholder="••••••••" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary transition-all shadow-inner" />
                    </div>
                    <div class="space-y-2">
                       <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Konfirmasi</label>
                       <input v-model="form.password_confirmation" type="password" placeholder="••••••••" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary transition-all shadow-inner" />
                    </div>
                 </div>
                 <div class="pt-6">
                    <button @click="nextStep" :disabled="!isStep1Valid" class="w-full py-4 bg-primary text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-primary/30 hover:bg-primary-hover active:scale-[0.98] transition-all disabled:opacity-50 disabled:active:scale-100">
                       Lanjutkan ke Pemilihan Tingkat
                    </button>
                 </div>
              </div>

              <!-- STEP 2: Tingkat -->
              <div v-else-if="step === 2" key="step2" class="space-y-6">
                 <div class="grid grid-cols-1 gap-3">
                    <button 
                      v-for="grade in ['10', '11', '12']" 
                      :key="grade"
                      @click="selectGrade(grade)"
                      :class="`p-6 rounded-3xl border-2 transition-all flex items-center justify-between group ${form.grade === grade ? 'border-primary bg-primary/5' : 'border-slate-100 dark:border-slate-800 hover:border-slate-200 dark:hover:border-slate-700'}`"
                    >
                       <div class="flex items-center gap-4">
                          <div :class="`w-12 h-12 rounded-2xl flex items-center justify-center font-black text-lg transition-all ${form.grade === grade ? 'bg-primary text-white' : 'bg-slate-50 dark:bg-slate-800 text-slate-400 group-hover:bg-slate-100'}`">
                             {{ toRoman(grade) }}
                          </div>
                          <div class="text-left">
                             <h4 :class="`font-black text-sm uppercase tracking-tight ${form.grade === grade ? 'text-primary' : 'text-slate-700 dark:text-slate-200'}`">Kelas {{ toRoman(grade) }}</h4>
                             <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Sekolah Menengah Atas</p>
                          </div>
                       </div>
                       <span class="material-icons text-primary transition-all" :class="form.grade === grade ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-2'">check_circle</span>
                    </button>
                 </div>
                 <div class="pt-4 flex gap-3">
                    <button @click="step--" class="px-6 py-4 bg-slate-50 dark:bg-slate-800 text-slate-500 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-slate-100 transition-all">Kembali</button>
                    <button @click="nextStep" :disabled="!form.grade" class="flex-1 py-4 bg-primary text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-primary/30 hover:bg-primary-hover active:scale-[0.98] transition-all disabled:opacity-50">
                       Pilih Kelas di Tingkat Ini
                    </button>
                 </div>
              </div>

              <!-- STEP 3: Kelas -->
              <div v-else key="step3" class="space-y-6">
                 <div class="grid grid-cols-2 gap-3">
                    <button 
                      v-for="c in filteredClassrooms" 
                      :key="c.id"
                      @click="form.classroom_id = c.id"
                      :class="`p-6 rounded-3xl border-2 transition-all text-center relative overflow-hidden group ${form.classroom_id === c.id ? 'border-primary bg-primary/5 shadow-2xl shadow-primary/20 ring-4 ring-primary/5' : 'border-slate-100 dark:border-slate-800 hover:border-slate-200 dark:hover:border-slate-700 hover:bg-slate-50'}`"
                    >
                       <div :class="`text-xl font-black uppercase tracking-tighter transition-colors ${form.classroom_id === c.id ? 'text-primary' : 'text-slate-800 dark:text-slate-200'}`">
                         {{ c.name }}
                       </div>
                       <div v-if="form.classroom_id === c.id" class="absolute top-2 right-2">
                          <span class="material-icons text-primary text-base">check_circle</span>
                       </div>
                    </button>
                 </div>

                 <div v-if="filteredClassrooms.length === 0" class="py-12 text-center bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700">
                    <span class="material-icons text-slate-300 text-4xl mb-3">error_outline</span>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest px-8">Maaf, belum ada kelas yang dibuka untuk tingkat ini.</p>
                 </div>

                 <div class="pt-4 flex gap-3">
                    <button @click="step--" class="px-6 py-4 bg-slate-50 dark:bg-slate-800 text-slate-500 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-slate-100 transition-all">Kembali</button>
                    <button @click="handleRegister" :disabled="!form.classroom_id || loading" class="flex-1 py-4 bg-primary text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-primary/30 hover:bg-primary-hover active:scale-[0.98] transition-all disabled:opacity-50">
                       {{ loading ? 'Memproses...' : 'Selesaikan Pendaftaran' }}
                    </button>
                 </div>
              </div>
           </Transition>
        </div>

        <!-- Footer Link -->
        <div class="py-6 bg-slate-50/50 dark:bg-slate-800/30 text-center border-t border-slate-100 dark:border-slate-800">
           <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
              Sudah punya akun? 
              <router-link to="/login" class="text-primary hover:text-primary-hover font-black ml-1 transition-colors">Masuk di sini</router-link>
           </p>
        </div>
      </div>
    </main>

    <footer class="relative z-10 py-6 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest opacity-60">
      © 2026 Sekolah Media Belajar. Sistem Manajemen Kuis Terintegrasi.
    </footer>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const step = ref(1)
const loading = ref(false)
const error = ref('')
const classrooms = ref([])

const stepIcons = { 1: 'person_add', 2: 'layers', 3: 'meeting_room' }
const stepTitles = { 1: 'Pendaftaran Siswa', 2: 'Pilih Tingkatan', 3: 'Pilih Ruang Kelas' }
const stepSubtitles = { 1: 'Buat akun belajar Anda', 2: 'Tingkat pendidikan saat ini', 3: 'Pilih kelas IPA, IPS, atau Umum' }

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  grade: '',
  classroom_id: ''
})

const toRoman = (n) => {
  const map = { '10': 'X', '11': 'XI', '12': 'XII', 10: 'X', 11: 'XI', 12: 'XII' }
  return map[n] || n
}

const isStep1Valid = computed(() => {
  return form.name && form.email && form.password && form.password === form.password_confirmation && form.password.length >= 8
})

const filteredClassrooms = computed(() => {
  if (!form.grade) return []
  return classrooms.value.filter(c => c.grade_level == form.grade)
})

onMounted(async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/public/classrooms')
    classrooms.value = response.data
  } catch (err) {
    console.error('Failed to fetch classrooms')
  }
})

const nextStep = () => {
  error.value = ''
  if (step.value === 1 && !isStep1Valid.value) return
  step.value++
}

const selectGrade = (grade) => {
  form.grade = grade
  form.classroom_id = ''
}

const handleRegister = async () => {
  loading.value = true
  error.value = ''
  try {
    const response = await axios.post('http://localhost:8000/api/register', {
      name: form.name,
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation,
      classroom_id: form.classroom_id
    })
    
    localStorage.setItem('access_token', response.data.access_token)
    localStorage.setItem('user', JSON.stringify(response.data.user))
    
    // Redirect to student dashboard
    router.push('/siswa/quizzes')
  } catch (err) {
    error.value = err.response?.data?.message || 'Registrasi gagal. Silakan periksa kembali data Anda.'
    // If validation fails on server, maybe go back to step 1
    if (err.response?.status === 422) step.value = 1
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.animate-in {
  animation: slideIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
