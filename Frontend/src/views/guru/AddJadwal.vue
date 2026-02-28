<template>
  <div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-4">
      <div class="flex items-center gap-4">
        <router-link 
          to="/guru/jadwal" 
          class="w-10 h-10 rounded-xl flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-slate-400 hover:text-primary transition-all shadow-sm"
        >
          <span class="material-icons">west</span>
        </router-link>
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase">{{ isEditMode ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h1>
          <p class="text-sm text-slate-500 font-medium">Konfigurasi jadwal mengajar mingguan Anda.</p>
        </div>
      </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-surface-dark border border-slate-100 dark:border-slate-800 rounded-[40px] overflow-hidden shadow-sm">
      <div class="p-8 sm:p-12">
        <form @submit.prevent="handleSubmit" class="max-w-3xl space-y-10">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Hari Mengajar -->
            <div class="space-y-3 relative">
              <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">Hari Mengajar</label>
              <div class="relative group">
                <span class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-primary group-focus-within:text-primary transition-colors">calendar_today</span>
                <button 
                  type="button"
                  @click.stop="toggleDropdown('day')"
                  class="w-full pl-14 pr-12 py-4 bg-slate-50 dark:bg-slate-800 border-2 border-primary/20 rounded-3xl focus:ring-2 focus:ring-primary/40 text-[11px] font-black uppercase tracking-widest outline-none transition-all cursor-pointer text-left flex items-center justify-between shadow-sm"
                >
                  <span class="text-slate-900 dark:text-white">{{ form.day_of_week }}</span>
                  <span class="material-icons text-primary opacity-60 transition-transform" :class="{'rotate-180': dropdowns.day}">expand_more</span>
                </button>
                
                <div v-if="dropdowns.day" class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-1 z-50 overflow-hidden animate-in fade-in slide-in-from-top-1">
                  <div 
                    v-for="d in days" :key="d"
                    @click="form.day_of_week = d; closeDropdowns()"
                    :class="`px-5 py-3 text-[10px] font-black uppercase tracking-wider cursor-pointer transition-all ${form.day_of_week === d ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-primary'}`"
                  >
                    {{ d }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Ruangan -->
            <div class="space-y-3">
              <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">Ruangan</label>
              <div class="relative group">
                <span class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-primary group-focus-within:text-primary transition-colors">meeting_room</span>
                <input 
                  v-model="form.room" 
                  type="text" 
                  placeholder="Contoh: R.101" 
                  class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-800 border-2 border-primary/10 rounded-3xl focus:ring-2 focus:ring-primary/40 text-[11px] font-black uppercase tracking-widest outline-none transition-all placeholder:text-slate-300 shadow-sm"
                />
              </div>
            </div>

            <!-- Mata Pelajaran -->
            <div class="space-y-3 relative md:col-span-2">
              <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">Mata Pelajaran</label>
              <div class="relative group">
                <span class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-primary group-focus-within:text-primary transition-colors">auto_stories</span>
                <button 
                  type="button"
                  @click.stop="toggleDropdown('subject')"
                  class="w-full pl-14 pr-12 py-4 bg-slate-50 dark:bg-slate-800 border-2 border-primary/20 rounded-3xl focus:ring-2 focus:ring-primary/40 text-[11px] font-black uppercase tracking-widest outline-none transition-all cursor-pointer text-left flex items-center justify-between shadow-sm"
                >
                  <span :class="form.subject_id ? 'text-slate-900 dark:text-white' : 'text-slate-300'">{{ selectedSubjectName || 'Pilih Mata Pelajaran' }}</span>
                  <span class="material-icons text-primary opacity-60 transition-transform" :class="{'rotate-180': dropdowns.subject}">expand_more</span>
                </button>
                
                <div v-if="dropdowns.subject" class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-1 z-50 max-h-60 overflow-y-auto animate-in fade-in slide-in-from-top-1 custom-scrollbar">
                  <div 
                    v-for="s in subjects" :key="s.id"
                    @click="form.subject_id = s.id; closeDropdowns()"
                    :class="`px-5 py-3 text-[10px] font-black uppercase tracking-wider cursor-pointer transition-all ${form.subject_id === s.id ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-primary'}`"
                  >
                    {{ s.name }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Target Kelas -->
            <div class="space-y-3 relative md:col-span-2">
              <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">Target Kelas</label>
              <div class="relative group">
                <span class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-primary group-focus-within:text-primary transition-colors">home_work</span>
                <button 
                  type="button"
                  @click.stop="toggleDropdown('class')"
                  class="w-full pl-14 pr-12 py-4 bg-slate-50 dark:bg-slate-800 border-2 border-primary/20 rounded-3xl focus:ring-2 focus:ring-primary/40 text-[11px] font-black uppercase tracking-widest outline-none transition-all cursor-pointer text-left flex items-center justify-between shadow-sm"
                >
                  <span :class="form.class_id ? 'text-slate-900 dark:text-white' : 'text-slate-300'">{{ selectedClassName || 'Pilih Kelas' }}</span>
                  <span class="material-icons text-primary opacity-60 transition-transform" :class="{'rotate-180': dropdowns.class}">expand_more</span>
                </button>
                
                <div v-if="dropdowns.class" class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-1 z-50 max-h-60 overflow-y-auto animate-in fade-in slide-in-from-top-1 custom-scrollbar">
                  <div 
                    v-for="c in classrooms" :key="c.id"
                    @click="form.class_id = c.id; closeDropdowns()"
                    :class="`px-5 py-3 text-[10px] font-black uppercase tracking-wider cursor-pointer transition-all ${form.class_id === c.id ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-primary'}`"
                  >
                    {{ c.name }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Jam Mulai -->
            <div class="space-y-3 relative">
              <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">Jam Mulai</label>
              <div class="relative group">
                <span class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-primary group-focus-within:text-primary transition-colors">schedule</span>
                <button 
                  type="button"
                  @click.stop="toggleDropdown('startTime')"
                  class="w-full pl-14 pr-12 py-4 bg-slate-50 dark:bg-slate-800 border-2 border-primary/10 rounded-3xl focus:ring-2 focus:ring-primary/40 text-[11px] font-black uppercase tracking-widest outline-none transition-all cursor-pointer text-left flex items-center justify-between shadow-sm"
                >
                  <span class="text-slate-900 dark:text-white">{{ formatTimeWithPeriod(form.start_time) }}</span>
                  <span class="material-icons text-primary opacity-40">edit</span>
                </button>
                
                <div v-if="dropdowns.startTime" class="absolute top-full left-0 mt-2 w-72 bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 p-6 z-[60] animate-in fade-in slide-in-from-top-2">
                  <div class="flex gap-4">
                    <div class="flex-1 space-y-2">
                      <div class="text-[8px] font-black text-slate-400 uppercase text-center">Jam</div>
                      <div class="h-40 overflow-y-auto custom-scrollbar pr-1">
                        <div v-for="h in 12" :key="h" 
                          @click="setTime('start', 'h', h)"
                          :class="`py-2 text-center text-xs font-black rounded-lg cursor-pointer ${get12H(form.start_time) === h ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-primary/5 text-slate-500'}`">
                          {{ String(h).padStart(2, '0') }}
                        </div>
                      </div>
                    </div>
                    <div class="w-px bg-slate-100 dark:bg-slate-700"></div>
                    <div class="flex-1 space-y-2">
                      <div class="text-[8px] font-black text-slate-400 uppercase text-center">Menit</div>
                      <div class="h-40 overflow-y-auto custom-scrollbar pr-1">
                        <div v-for="m in ['00', '15', '30', '45']" :key="m" 
                          @click="setTime('start', 'm', m)"
                          :class="`py-2 text-center text-xs font-black rounded-lg cursor-pointer ${form.start_time.split(':')[1] === m ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-primary/5 text-slate-500'}`">
                          {{ m }}
                        </div>
                      </div>
                    </div>
                    <div class="w-px bg-slate-100 dark:bg-slate-700"></div>
                    <div class="flex-1 space-y-2">
                      <div class="text-[8px] font-black text-slate-400 uppercase text-center">AM/PM</div>
                      <div class="space-y-2">
                        <div v-for="p in ['AM', 'PM']" :key="p" 
                          @click="setTime('start', 'p', p)"
                          :class="`py-2 text-center text-[10px] font-black rounded-lg cursor-pointer ${getPeriod(form.start_time) === p ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-primary/5 text-slate-500'}`">
                          {{ p }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Jam Selesai -->
            <div class="space-y-3 relative">
              <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">Jam Selesai</label>
              <div class="relative group">
                <span class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-primary group-focus-within:text-primary transition-colors">history</span>
                <button 
                  type="button"
                  @click.stop="toggleDropdown('endTime')"
                  class="w-full pl-14 pr-12 py-4 bg-slate-50 dark:bg-slate-800 border-2 border-primary/10 rounded-3xl focus:ring-2 focus:ring-primary/40 text-[11px] font-black uppercase tracking-widest outline-none transition-all cursor-pointer text-left flex items-center justify-between shadow-sm"
                >
                  <span class="text-slate-900 dark:text-white">{{ formatTimeWithPeriod(form.end_time) }}</span>
                  <span class="material-icons text-primary opacity-40">edit</span>
                </button>
                
                <div v-if="dropdowns.endTime" class="absolute top-full left-0 mt-2 w-72 bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 p-6 z-[60] animate-in fade-in slide-in-from-top-2">
                  <div class="flex gap-4">
                    <div class="flex-1 space-y-2">
                      <div class="text-[8px] font-black text-slate-400 uppercase text-center">Jam</div>
                      <div class="h-40 overflow-y-auto custom-scrollbar pr-1">
                        <div v-for="h in 12" :key="h" 
                          @click="setTime('end', 'h', h)"
                          :class="`py-2 text-center text-xs font-black rounded-lg cursor-pointer ${get12H(form.end_time) === h ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-primary/5 text-slate-500'}`">
                          {{ String(h).padStart(2, '0') }}
                        </div>
                      </div>
                    </div>
                    <div class="w-px bg-slate-100 dark:bg-slate-700"></div>
                    <div class="flex-1 space-y-2">
                      <div class="text-[8px] font-black text-slate-400 uppercase text-center">Menit</div>
                      <div class="h-40 overflow-y-auto custom-scrollbar pr-1">
                        <div v-for="m in ['00', '15', '30', '45']" :key="m" 
                          @click="setTime('end', 'm', m)"
                          :class="`py-2 text-center text-xs font-black rounded-lg cursor-pointer ${form.end_time.split(':')[1] === m ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-primary/5 text-slate-500'}`">
                          {{ m }}
                        </div>
                      </div>
                    </div>
                    <div class="w-px bg-slate-100 dark:bg-slate-700"></div>
                    <div class="flex-1 space-y-2">
                      <div class="text-[8px] font-black text-slate-400 uppercase text-center">AM/PM</div>
                      <div class="space-y-2">
                        <div v-for="p in ['AM', 'PM']" :key="p" 
                          @click="setTime('end', 'p', p)"
                          :class="`py-2 text-center text-[10px] font-black rounded-lg cursor-pointer ${getPeriod(form.end_time) === p ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-primary/5 text-slate-500'}`">
                          {{ p }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div class="space-y-3">
            <label class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">Catatan Tambahan (Opsional)</label>
            <div class="relative">
              <textarea 
                v-model="form.notes"
                rows="3"
                placeholder="Misal: Pertemuan di Lab Komputer"
                class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border-2 border-primary/5 rounded-3xl focus:ring-2 focus:ring-primary/40 text-[11px] font-bold uppercase tracking-widest outline-none transition-all placeholder:text-slate-300 shadow-sm resize-none"
              ></textarea>
            </div>
          </div>

          <!-- Submit Buttons -->
          <div class="flex items-center gap-4 pt-6">
            <button 
              type="submit" 
              :disabled="loading"
              class="flex-1 py-4 bg-primary text-white rounded-3xl font-black text-xs uppercase tracking-[0.2em] hover:bg-primary-hover transition-all active:scale-95 disabled:opacity-50 shadow-xl shadow-primary/30"
            >
              <div class="flex items-center justify-center gap-2">
                <span v-if="loading" class="material-icons animate-spin text-sm">sync</span>
                <span>{{ loading ? 'Memproses...' : (isEditMode ? 'Simpan Perubahan' : 'Buat Jadwal Baru') }}</span>
              </div>
            </button>
            <router-link 
              to="/guru/jadwal" 
              class="px-8 py-4 bg-slate-50 dark:bg-slate-800 text-slate-500 rounded-3xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-100 transition-all text-center border-2 border-transparent hover:border-slate-200"
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
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/configs/api'
import { useNotificationStore } from '../../stores/notification'

const router = useRouter()
const route = useRoute()
const notification = useNotificationStore()
const loading = ref(false)
const isEditMode = ref(false)

const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
const subjects = ref([])
const classrooms = ref([])

const dropdowns = ref({
  day: false,
  subject: false,
  class: false,
  startTime: false,
  endTime: false
})

const form = ref({
  day_of_week: 'Senin',
  subject_id: '',
  class_id: '',
  start_time: '08:00',
  end_time: '09:30',
  room: '',
  notes: ''
})

const selectedSubjectName = computed(() => {
  const sub = subjects.value.find(s => s.id === form.value.subject_id)
  return sub ? sub.name : ''
})

const selectedClassName = computed(() => {
  const cls = classrooms.value.find(c => c.id === form.value.class_id)
  return cls ? cls.name : ''
})

const toggleDropdown = (key) => {
  Object.keys(dropdowns.value).forEach(k => {
    dropdowns.value[k] = k === key ? !dropdowns.value[k] : false
  })
}

const closeDropdowns = () => {
  Object.keys(dropdowns.value).forEach(k => dropdowns.value[k] = false)
}

onMounted(async () => {
  const promises = [fetchFormData()]
  if (route.params.id) {
    isEditMode.value = true
    promises.push(fetchScheduleData(route.params.id))
  }
  
  await Promise.all(promises)
  document.addEventListener('click', closeDropdowns)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdowns)
})

const fetchFormData = async () => {
  try {
    const token = localStorage.getItem('access_token')
    const [subRes, clsRes] = await Promise.all([
      api.get('/guru/subjects'),
      api.get('/public/classrooms')
    ])
    subjects.value = subRes.data
    classrooms.value = clsRes.data
  } catch (err) {
    console.error('Fetch form data failed', err)
  }
}

const fetchScheduleData = async (id) => {
  loading.value = true
  try {
    const token = localStorage.getItem('access_token')
    const res = await api.get(`/guru/teaching-schedules/${id}`)
    const item = res.data
    form.value = {
      day_of_week: item.day_of_week,
      subject_id: item.subject_id,
      class_id: item.class_id,
      start_time: item.start_time.substring(0, 5),
      end_time: item.end_time.substring(0, 5),
      room: item.room,
      notes: item.notes
    }
  } catch (error) {
    console.error('Failed to fetch schedule data:', error)
    notification.add({ title: 'Gagal!', message: 'Data tidak ditemukan.', type: 'error' })
    router.push('/guru/jadwal')
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('access_token')
    if (isEditMode.value) {
      await api.put(`/guru/teaching-schedules/${route.params.id}`, form.value)
      notification.add({ title: 'Berhasil!', message: 'Jadwal diperbarui.', type: 'success' })
    } else {
      await api.post('/guru/teaching-schedules', form.value)
      notification.add({ title: 'Berhasil!', message: 'Jadwal ditambahkan.', type: 'success' })
    }
    router.push('/guru/jadwal')
  } catch (err) {
    console.error('Save failed', err)
    notification.add({ title: 'Gagal!', message: 'Periksa kembali data Anda.', type: 'error' })
  } finally {
    loading.value = false
  }
}

// Time Picker Helpers
const formatTimeWithPeriod = (time) => {
  if (!time) return ''
  const [hours, minutes] = time.split(':')
  const h = parseInt(hours)
  const period = h >= 12 ? 'PM' : 'AM'
  const h12 = h % 12 || 12
  return `${String(h12).padStart(2, '0')}:${minutes} ${period}`
}

const get12H = (time) => {
  if (!time) return 12
  const h = parseInt(time.split(':')[0])
  return h % 12 || 12
}

const getPeriod = (time) => {
  if (!time) return 'AM'
  const h = parseInt(time.split(':')[0])
  return h >= 12 ? 'PM' : 'AM'
}

const setTime = (type, unit, val) => {
  const currentKey = type === 'start' ? 'start_time' : 'end_time'
  const [currentH, currentM] = form.value[currentKey].split(':')
  let finalH = parseInt(currentH)
  let finalM = currentM
  
  if (unit === 'h') {
    const period = finalH >= 12 ? 'PM' : 'AM'
    if (period === 'AM') finalH = val === 12 ? 0 : val
    else finalH = val === 12 ? 12 : val + 12
  } else if (unit === 'm') {
    finalM = val
  } else if (unit === 'p') {
    const currentPeriod = finalH >= 12 ? 'PM' : 'AM'
    if (currentPeriod !== val) {
      if (val === 'AM') finalH -= 12
      else finalH += 12
    }
  }
  
  form.value[currentKey] = `${String(finalH).padStart(2, '0')}:${finalM}`
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #334155;
}
</style>
