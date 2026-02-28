<template>
  <div class="space-y-6 sm:space-y-8 animate-in fade-in duration-500">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
      <div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight uppercase">Absensi Siswa</h1>
        <p class="text-sm text-slate-500 font-medium tracking-wide mt-1">Pilih kelas dan catat kehadiran siswa hari ini.</p>
      </div>
      <div v-if="selectedClass && !loading" class="flex items-center gap-3">
        <button @click="saveAttendance" class="flex items-center gap-2 px-6 py-3.5 bg-primary border border-transparent rounded-[20px] text-xs font-black uppercase tracking-widest text-white hover:bg-primary-hover shadow-xl shadow-primary/30 hover:scale-[1.02] active:scale-95 transition-all">
          <span class="material-icons text-lg">check_circle</span>
          Simpan Absen
        </button>
      </div>
    </div>

    <!-- Class Selector & Rekap (Custom Premium Dropdown) -->
    <div class="flex flex-col md:flex-row gap-6 items-end bg-white dark:bg-surface-dark p-6 sm:p-8 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm relative z-30">
      <div class="w-full max-w-xs relative">
        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Pilih Kelas</label>
        
        <button 
          @click.stop="isDropdownOpen = !isDropdownOpen"
          class="w-full flex items-center justify-between pl-5 pr-4 py-4 bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent hover:border-primary/30 focus:border-primary rounded-2xl text-sm font-black transition-all group shadow-sm"
          :class="{'border-primary shadow-lg shadow-primary/5': isDropdownOpen}"
        >
          <div class="flex items-center gap-3 text-left">
            <span class="material-icons text-primary text-xl">school</span>
            <span :class="selectedClass ? 'text-slate-900 dark:text-white' : 'text-slate-400'">
              {{ selectedClassName || 'Pilih Kelas...' }}
            </span>
          </div>
          <span class="material-icons text-primary transition-transform duration-300" :class="{'rotate-180': isDropdownOpen}">expand_more</span>
        </button>

        <Transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="opacity-0 scale-95 -translate-y-2"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 -translate-y-2"
        >
          <div v-if="isDropdownOpen" class="absolute top-full left-0 w-full mt-3 bg-white dark:bg-surface-dark rounded-[24px] shadow-2xl border border-slate-100 dark:border-slate-800 py-3 z-40 overflow-hidden">
            <button 
              v-for="c in classes" 
              :key="c.id"
              @click="selectClass(c)"
              class="w-full px-6 py-4 text-left flex items-center justify-between transition-all group"
              :class="selectedClass === c.id ? 'bg-primary/10 text-primary' : 'hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-primary'"
            >
              <span class="text-xs font-black uppercase tracking-widest">{{ c.name }}</span>
              <span v-if="selectedClass === c.id" class="material-icons text-sm">check_circle</span>
            </button>
          </div>
        </Transition>
      </div>

      <div v-if="selectedClass" class="flex gap-2">
        <button 
          @click="openRekap"
          class="flex items-center gap-2 px-6 py-4 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-600 dark:text-slate-300 hover:border-primary/50 hover:text-primary transition-all shadow-sm group"
        >
          <span class="material-icons text-lg group-hover:scale-110 transition-transform">insights</span>
          Rekap Absensi
        </button>
      </div>
    </div>

    <!-- Rekap Modal Overlay -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showRekap" class="fixed inset-0 z-[160] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="showRekap = false"></div>
        
        <div class="relative w-full max-w-4xl max-h-[90vh] bg-white dark:bg-surface-dark rounded-[40px] shadow-2xl border border-slate-100 dark:border-slate-800 flex flex-col overflow-hidden animate-in zoom-in-95 duration-300">
          <div class="p-8 border-b border-slate-50 dark:border-slate-800 flex items-center justify-between shrink-0">
            <div>
              <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Rekap Absensi</h3>
              <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Kelas {{ selectedClassName }} • Periode Ini</p>
            </div>
            <div class="flex items-center gap-3">
              <button @click="exportToExcel" class="flex items-center gap-2 px-5 py-2.5 bg-green-500 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] text-white hover:bg-green-600 transition-all shadow-lg shadow-green-500/20">
                <span class="material-icons text-base">download</span>
                Export Excel
              </button>
              <button @click="showRekap = false" class="w-10 h-10 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                <span class="material-icons">close</span>
              </button>
            </div>
          </div>

          <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            <div v-if="loadingRekap" class="py-20 flex flex-col items-center justify-center gap-4">
              <div class="w-10 h-10 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Menyiapkan Rekap...</p>
            </div>
            
            <div v-else-if="rekapData" class="space-y-6">
              <div class="grid grid-cols-4 gap-4">
                <div v-for="(count, label) in totalRekapCounts" :key="label" class="p-4 rounded-3xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 text-center">
                  <div class="text-2xl font-black" :class="labelColor(label)">{{ count }}</div>
                  <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">{{ label }}</div>
                </div>
              </div>

              <div class="border border-slate-100 dark:border-slate-800 rounded-3xl overflow-hidden">
                <table class="w-full text-left">
                  <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                    <tr>
                      <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Siswa</th>
                      <th class="px-4 py-4 text-[10px] font-black text-green-500 uppercase tracking-widest text-center">H</th>
                      <th class="px-4 py-4 text-[10px] font-black text-blue-500 uppercase tracking-widest text-center">I</th>
                      <th class="px-4 py-4 text-[10px] font-black text-orange-500 uppercase tracking-widest text-center">S</th>
                      <th class="px-4 py-4 text-[10px] font-black text-red-500 uppercase tracking-widest text-center">A</th>
                      <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Persentase</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    <tr v-for="s in rekapData.students" :key="s.id" class="hover:bg-slate-50/50 transition-colors">
                      <td class="px-6 py-4 text-sm font-black text-slate-700 dark:text-slate-300">{{ s.name }}</td>
                      <td class="px-4 py-4 text-center font-bold text-slate-600">{{ s.summary.Hadir }}</td>
                      <td class="px-4 py-4 text-center font-bold text-slate-600">{{ s.summary.Izin }}</td>
                      <td class="px-4 py-4 text-center font-bold text-slate-600">{{ s.summary.Sakit }}</td>
                      <td class="px-4 py-4 text-center font-bold text-slate-600">{{ s.summary.Alpha }}</td>
                      <td class="px-6 py-4 text-right">
                        <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-[10px] font-black">{{ calculateAttendanceRate(s.summary) }}%</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Attendance Content -->
    <div v-if="loading" class="space-y-4">
      <div v-for="i in 5" :key="i" class="h-24 bg-slate-100 dark:bg-slate-800 rounded-[28px] animate-pulse"></div>
    </div>

    <template v-else-if="selectedClass">
      <div class="grid grid-cols-1 gap-4 sm:gap-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
        <!-- List Header Info -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-4">
          <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Daftar Siswa - {{ selectedClassName }}</h2>
            <div class="flex items-center gap-2 mt-1">
               <span class="text-[10px] font-black text-primary uppercase tracking-[0.15em] bg-primary/10 px-2 py-0.5 rounded-lg">{{ todayLabel }}</span>
               <span class="text-slate-300 dark:text-slate-700 mx-1">•</span>
               <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ students.length }} Siswa Terdaftar</span>
            </div>
          </div>
          <div class="flex items-center gap-6">
            <div class="flex flex-col items-center">
              <span class="text-lg font-black text-green-500">{{ counts.Hadir }}</span>
              <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Hadir</span>
            </div>
            <div class="flex flex-col items-center">
              <span class="text-lg font-black text-blue-500">{{ counts.Izin }}</span>
              <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Izin</span>
            </div>
            <div class="flex flex-col items-center">
              <span class="text-lg font-black text-orange-500">{{ counts.Sakit }}</span>
              <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Sakit</span>
            </div>
            <div class="flex flex-col items-center">
              <span class="text-lg font-black text-red-500">{{ counts.Alpha }}</span>
              <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Alpha</span>
            </div>
          </div>
        </div>

        <!-- Student Cards ("Box list-list") -->
        <div class="grid grid-cols-1 gap-4">
          <div 
            v-for="(student, index) in students" 
            :key="student.id" 
            class="group p-5 bg-white dark:bg-surface-dark border border-slate-100 dark:border-slate-800 rounded-[28px] shadow-sm hover:shadow-md hover:border-primary/20 transition-all flex flex-col md:flex-row md:items-center justify-between gap-6"
          >
            <div class="flex items-center gap-5">
              <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 text-slate-400 flex items-center justify-center font-black text-sm group-hover:bg-primary group-hover:text-white transition-all duration-300">
                {{ index + 1 }}
              </div>
              <div class="space-y-1">
                <div class="font-black text-slate-900 dark:text-white uppercase tracking-tight group-hover:text-primary transition-colors">{{ student.name }}</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">NIS: 2324100{{ index + 1 }}</div>
              </div>
            </div>

            <div class="flex flex-wrap gap-2 bg-slate-50 dark:bg-slate-800/50 p-1.5 rounded-[20px] border border-slate-100 dark:border-slate-700 w-fit">
              <button 
                v-for="status in ['Hadir', 'Izin', 'Sakit', 'Alpha']"
                :key="status"
                @click="attendance[student.id] = status"
                :class="`px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-[0.1em] transition-all ${attendance[student.id] === status ? getStatusClass(status) : 'text-slate-400 hover:text-slate-600 hover:bg-white/50 dark:hover:bg-slate-800'}`"
              >
                {{ status }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Empty State -->
    <div v-else class="text-center py-24 bg-white dark:bg-surface-dark rounded-[48px] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden relative group" style="border-radius: 48px;">
      <div class="relative z-10">
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-[32px] bg-primary/10 text-primary mb-8 group-hover:rotate-12 group-hover:scale-110 transition-all duration-500">
          <span class="material-icons text-5xl">fact_check</span>
        </div>
        <h3 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Siap Mencatat?</h3>
        <p class="text-slate-500 mt-3 font-medium max-w-sm mx-auto leading-relaxed">Pilih salah satu kelas di atas untuk mulai memvalidasi kehadiran siswa Anda dengan antarmuka yang lebih modern.</p>
      </div>
      <!-- Decorative background -->
      <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useNotificationStore } from '../../stores/notification'
import api from '../../configs/api'

const notification = useNotificationStore()
const selectedClass = ref('')
const isDropdownOpen = ref(false)
const loading = ref(false)
const submitting = ref(false)

const classes = ref([])
const students = ref([])
const attendance = ref({})
const todayLabel = ref('')

const selectedClassName = computed(() => {
  const c = classes.value.find(c => c.id === selectedClass.value)
  return c ? c.name : ''
})

const showRekap = ref(false)
const rekapData = ref(null)
const loadingRekap = ref(false)

const openRekap = async () => {
  if (!selectedClass.value) return
  showRekap.value = true
  loadingRekap.value = true
  try {
    const response = await api.get(`/guru/attendances/rekap/${selectedClass.value}`)
    rekapData.value = response.data
  } catch (err) {
    console.error('Gagal mengambil rekap', err)
    notification.add({
      title: 'Error',
      message: 'Gagal mengambil data rekap absensi.',
      type: 'error'
    })
  } finally {
    loadingRekap.value = false
  }
}

const totalRekapCounts = computed(() => {
  if (!rekapData.value) return { Hadir: 0, Izin: 0, Sakit: 0, Alpha: 0 }
  return rekapData.value.students.reduce((acc, s) => {
    acc.Hadir += s.summary.Hadir
    acc.Izin += s.summary.Izin
    acc.Sakit += s.summary.Sakit
    acc.Alpha += s.summary.Alpha
    return acc
  }, { Hadir: 0, Izin: 0, Sakit: 0, Alpha: 0 })
})

const calculateAttendanceRate = (summary) => {
  const total = Object.values(summary).reduce((a, b) => a + b, 0)
  if (total === 0) return 0
  return Math.round((summary.Hadir / total) * 100)
}

const labelColor = (label) => {
  switch(label) {
    case 'Hadir': return 'text-green-500'
    case 'Izin': return 'text-blue-500'
    case 'Sakit': return 'text-orange-500'
    case 'Alpha': return 'text-red-500'
    default: return 'text-slate-400'
  }
}

const exportToExcel = () => {
  if (!rekapData.value) return
  
  const headers = ['Nama Siswa', 'Hadir', 'Izin', 'Sakit', 'Alpha', 'Persentase']
  const rows = rekapData.value.students.map(s => [
    `"${s.name}"`,
    s.summary.Hadir,
    s.summary.Izin,
    s.summary.Sakit,
    s.summary.Alpha,
    `${calculateAttendanceRate(s.summary)}%`
  ])
  
  const csvContent = [
    headers.join(','),
    ...rows.map(r => r.join(','))
  ].join('\n')
  
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  const url = URL.createObjectURL(blob)
  link.setAttribute('href', url)
  link.setAttribute('download', `Rekap_Absensi_${selectedClassName.value}_${new Date().toISOString().split('T')[0]}.csv`)
  link.style.visibility = 'hidden'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  
  notification.add({
    title: 'Export Berhasil',
    message: 'Data rekap telah diunduh.',
    type: 'success'
  })
}

const counts = computed(() => {
  const result = { Hadir: 0, Izin: 0, Sakit: 0, Alpha: 0 }
  Object.values(attendance.value).forEach(v => {
    if (result[v] !== undefined) result[v]++
  })
  return result
})

const fetchClassrooms = async () => {
  try {
    const response = await api.get('/guru/classrooms')
    classes.value = response.data
  } catch (err) {
    console.error('Gagal mengambil data kelas', err)
    notification.add({
      title: 'Error',
      message: 'Gagal mengambil data kelas. Pastikan Anda memiliki akses.',
      type: 'error'
    })
  }
}

const selectClass = async (c) => {
  selectedClass.value = c.id
  isDropdownOpen.value = false
  loading.value = true
  
  try {
    const response = await api.get(`/guru/classrooms/${c.id}`)
    const data = response.data
    students.value = data.students || []
    
    // Initialize attendance for the new class students
    const newAttendance = {}
    students.value.forEach(s => {
      newAttendance[s.id] = 'Hadir'
    })
    attendance.value = newAttendance
  } catch (err) {
    console.error('Gagal mengambil data siswa', err)
    notification.add({
      title: 'Error',
      message: 'Gagal memuat daftar siswa.',
      type: 'error'
    })
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  todayLabel.value = new Date().toLocaleDateString('id-ID', options)
  
  fetchClassrooms()
  
  window.addEventListener('click', () => {
    isDropdownOpen.value = false
  })
})

const getStatusClass = (status) => {
  switch(status) {
    case 'Hadir': return 'bg-green-500 text-white shadow-lg shadow-green-200 dark:shadow-none scale-[1.05]'
    case 'Izin': return 'bg-blue-500 text-white shadow-lg shadow-blue-200 dark:shadow-none scale-[1.05]'
    case 'Sakit': return 'bg-orange-500 text-white shadow-lg shadow-orange-200 dark:shadow-none scale-[1.05]'
    case 'Alpha': return 'bg-red-500 text-white shadow-lg shadow-red-200 dark:shadow-none scale-[1.05]'
    default: return ''
  }
}

const saveAttendance = async () => {
  if (submitting.value) return
  submitting.value = true
  
  const payload = {
    class_id: selectedClass.value,
    date: new Date().toISOString().split('T')[0],
    attendances: students.value.map(s => ({
      student_id: s.id,
      status: attendance.value[s.id] || 'Hadir',
      notes: ''
    }))
  }
  
  try {
    const response = await api.post('/guru/attendances', payload)
    notification.add({
      title: 'Sukses!',
      message: response.data.message || `Absensi ${selectedClassName.value} telah disimpan ke database.`,
      type: 'success'
    })
  } catch (err) {
    console.error('Gagal menyimpan absensi', err)
    const errorMsg = err.response?.data?.message || 'Terjadi kesalahan saat menyimpan absensi.'
    notification.add({
      title: 'Gagal',
      message: errorMsg,
      type: 'error'
    })
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: .5; }
}
</style>
