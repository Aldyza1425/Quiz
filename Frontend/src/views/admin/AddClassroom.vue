<template>
  <div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-300">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-4">
      <div class="flex items-center gap-4">
        <router-link 
          to="/admin/classrooms" 
          class="w-10 h-10 rounded-full flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-slate-400 hover:text-primary transition-all"
        >
          <span class="material-icons">west</span>
        </router-link>
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight uppercase">{{ isEditMode ? 'Edit Ruang Kelas' : 'Tambah Kelas Baru' }}</h1>
          <p class="text-sm text-slate-500 font-medium">{{ isEditMode ? 'Perbarui konfigurasi ruang kelas dan tahun ajaran.' : 'Konfigurasi ruang kelas and tahun ajaran baru.' }}</p>
        </div>
      </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-surface-dark border border-slate-100 dark:border-slate-800 rounded-[32px] overflow-hidden">
      <div class="p-8 sm:p-16">
        <form @submit.prevent="handleSubmit" class="max-w-2xl space-y-12">
          <!-- Main Form Fields -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-y-12 gap-x-8">
            <div class="space-y-3 md:col-span-2">
              <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">Nama Ruang Kelas</label>
              <div class="relative group">
                <span class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">home_work</span>
                <input 
                  v-model="form.name" 
                  type="text" 
                  placeholder="Contoh: X IPA 1 atau XI IPS 2" 
                  class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-sm font-bold outline-none transition-all"
                  required 
                />
              </div>
            </div>

            <div class="space-y-3 relative">
              <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">Tingkat Kelas</label>
              <div class="relative group">
                <span class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-primary group-focus-within:text-primary transition-colors">stairs</span>
                <button 
                  type="button"
                  @click.stop="isGradeDropdownOpen = !isGradeDropdownOpen; isYearDropdownOpen = false; isTeacherDropdownOpen = false"
                  class="w-full pl-14 pr-12 py-4 bg-slate-50 dark:bg-slate-800 border-2 border-primary/20 rounded-2xl focus:ring-2 focus:ring-primary/40 text-[11px] font-black uppercase tracking-widest outline-none transition-all cursor-pointer min-w-[200px] text-left flex items-center justify-between"
                >
                  <span class="text-slate-900 dark:text-white">KELAS {{ form.grade_level }} ({{ getGradeLabel(form.grade_level).toUpperCase() }})</span>
                  <span class="material-icons text-primary opacity-60 transition-transform" :class="{'rotate-180': isGradeDropdownOpen}">expand_more</span>
                </button>
                
                <div v-if="isGradeDropdownOpen" class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-1 z-50 overflow-hidden animate-in fade-in slide-in-from-top-1">
                  <div 
                    v-for="g in ['10', '11', '12']" :key="g"
                    @click="form.grade_level = g; isGradeDropdownOpen = false"
                    :class="`px-5 py-3 text-[10px] font-black uppercase tracking-wider cursor-pointer transition-all ${form.grade_level === g ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-primary'}`"
                  >
                    KELAS {{ g }} ({{ getGradeLabel(g).toUpperCase() }})
                  </div>
                </div>
              </div>
            </div>

            <div class="space-y-3 relative">
              <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">Tahun Ajaran</label>
              <div class="relative group">
                <span class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-primary group-focus-within:text-primary transition-colors">calendar_today</span>
                <button 
                  type="button"
                  @click.stop="isYearDropdownOpen = !isYearDropdownOpen; isGradeDropdownOpen = false; isTeacherDropdownOpen = false"
                  class="w-full pl-14 pr-12 py-4 bg-slate-50 dark:bg-slate-800 border-2 border-primary/40 rounded-3xl focus:ring-2 focus:ring-primary/40 text-[11px] font-black uppercase tracking-widest outline-none transition-all cursor-pointer min-w-[240px] text-left flex items-center justify-between shadow-sm"
                >
                  <span class="text-slate-900 dark:text-white">TAHUN {{ form.academic_year }}</span>
                  <span class="material-icons text-primary opacity-60 transition-transform" :class="{'rotate-180': isYearDropdownOpen}">expand_more</span>
                </button>

                <div v-if="isYearDropdownOpen" class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-900 border-opacity-10 py-0 z-50 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                  <div 
                    v-for="y in ['2025/2026', '2024/2025', '2023/2024']" :key="y"
                    @click="form.academic_year = y; isYearDropdownOpen = false"
                    :class="`px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider cursor-pointer transition-all ${form.academic_year === y ? 'bg-primary text-white shadow-lg' : 'text-slate-600 hover:bg-slate-50 hover:text-primary'}`"
                  >
                    TAHUN {{ y }}
                  </div>
                </div>
              </div>
            </div>

            <div class="space-y-3 relative">
              <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">Wali Kelas</label>
              <div class="relative group">
                <span class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-primary group-focus-within:text-primary transition-colors">supervisor_account</span>
                <button 
                  type="button"
                  @click.stop="isTeacherDropdownOpen = !isTeacherDropdownOpen; isGradeDropdownOpen = false; isYearDropdownOpen = false"
                  class="w-full pl-14 pr-12 py-4 bg-slate-50 dark:bg-slate-800 border-2 border-primary/20 rounded-3xl focus:ring-2 focus:ring-primary/40 text-[11px] font-black uppercase tracking-widest outline-none transition-all cursor-pointer text-left flex items-center justify-between shadow-sm"
                >
                  <span class="text-slate-900 dark:text-white truncate pr-4">{{ getTeacherName(form.homeroom_teacher_id) }}</span>
                  <span class="material-icons text-primary opacity-60 transition-transform" :class="{'rotate-180': isTeacherDropdownOpen}">expand_more</span>
                </button>

                <div v-if="isTeacherDropdownOpen" class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-900 border-opacity-10 py-0 z-50 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                  <div class="max-h-[105px] overflow-y-auto custom-scrollbar">
                    <div 
                      @click="form.homeroom_teacher_id = null; isTeacherDropdownOpen = false"
                      :class="`px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider cursor-pointer transition-all ${form.homeroom_teacher_id === null ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50 hover:text-primary'}`"
                    >
                      BELUM DITENTUKAN
                    </div>
                    <div 
                      v-for="t in teachers" :key="t.id"
                      @click="form.homeroom_teacher_id = t.id; isTeacherDropdownOpen = false"
                      :class="`px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider cursor-pointer transition-all ${form.homeroom_teacher_id === t.id ? 'bg-primary text-white shadow-lg' : 'text-slate-600 hover:bg-slate-50 hover:text-primary'}`"
                    >
                      {{ t.name }} <span v-if="t.nip" class="ml-2 opacity-50 text-[9px] font-black">({{ t.nip }})</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <!-- Submit Buttons -->
          <div class="flex items-center gap-4 pt-6">
            <button 
              type="submit" 
              :disabled="loading"
              class="flex-1 py-4 bg-primary text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-primary-hover transition-all active:scale-95 disabled:opacity-50 shadow-lg shadow-primary/20"
            >
              <div class="flex items-center justify-center gap-2">
                <span v-if="loading" class="material-icons animate-spin text-sm">sync</span>
                <span>{{ loading ? 'Memproses...' : (isEditMode ? 'Perbarui & Simpan' : 'Simpan & Publikasikan') }}</span>
              </div>
            </button>
            <router-link 
              to="/admin/classrooms" 
              class="px-8 py-4 bg-slate-50 dark:bg-slate-800 text-slate-500 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-100 transition-all text-center"
            >
              Batal
            </router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '../../configs/api.js'

const router = useRouter()
const route = useRoute()
const loading = ref(false)
const isEditMode = ref(false)

// Dropdown states
const isGradeDropdownOpen = ref(false)
const isYearDropdownOpen = ref(false)
const isTeacherDropdownOpen = ref(false)

const form = ref({
  name: '',
  grade_level: '10',
  academic_year: '2025/2026',
  homeroom_teacher_id: null
})

const teachers = ref([])

const getGradeLabel = (grade) => {
  const map = { '10': 'Sepuluh', '11': 'Sebelas', '12': 'Dua Belas' }
  return map[grade] || ''
}

const getTeacherName = (id) => {
  if (!id) return 'PILIH WALI KELAS'
  const teacher = teachers.value.find(t => t.id === id)
  return teacher ? teacher.name.toUpperCase() : 'PILIH WALI KELAS'
}

const closeDropdowns = () => {
  isGradeDropdownOpen.value = false
  isYearDropdownOpen.value = false
  isTeacherDropdownOpen.value = false
}

onMounted(async () => {
  fetchTeachers()
  if (route.params.id) {
    isEditMode.value = true
    await fetchClassroomData(route.params.id)
  }
  document.addEventListener('click', closeDropdowns)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdowns)
})

const fetchTeachers = async () => {
  try {
    const response = await api.get('/admin/users?role=guru')
    teachers.value = response.data
  } catch (error) {
    console.error('Failed to fetch teachers:', error)
  }
}

const fetchClassroomData = async (id) => {
  loading.value = true
  try {
    const response = await api.get(`/admin/classrooms/${id}`)
    const data = response.data
    form.value = {
      name: data.name,
      grade_level: data.grade_level.toString(),
      academic_year: data.academic_year,
      homeroom_teacher_id: data.homeroom_teacher_id
    }
  } catch (error) {
    console.error('Failed to fetch classroom data:', error)
    alert('Gagal mengambil data kelas')
    router.push('/admin/classrooms')
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  loading.value = true
  try {
    if (isEditMode.value) {
      await api.patch(`/admin/classrooms/${route.params.id}`, form.value)
    } else {
      await api.post('/admin/classrooms', form.value)
    }
    router.push('/admin/classrooms')
  } catch (error) {
    console.error('Failed to save classroom:', error)
    alert(error.response?.data?.message || 'Gagal menyimpan data kelas')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(0,0,0,0.1);
  border-radius: 10px;
}
</style>
