<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Jadwal Quiz</h1>
        <p class="text-sm text-slate-500 mt-1">Atur kapan quiz dapat dikerjakan oleh masing-masing kelas.</p>
      </div>
      <button @click="isModalOpen = true" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-primary-hover shadow-lg shadow-primary/30 transition-all">
        <span class="material-icons text-lg mr-2">event_available</span>
        Atur Jadwal Baru
      </button>
    </div>

    <!-- Schedules Feed -->
    <div class="bg-surface-light dark:bg-surface-dark rounded-[24px] border border-border-light dark:border-border-dark overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead class="bg-slate-50 dark:bg-slate-800/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-border-light dark:border-border-dark">
            <tr>
              <th class="px-6 py-4">Quiz</th>
              <th class="px-6 py-4">Kelas</th>
              <th class="px-6 py-4">Waktu Mulai</th>
              <th class="px-6 py-4">Waktu Selesai</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light dark:divide-border-dark">
            <tr v-for="sch in schedules" :key="sch.id" class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
              <td class="px-6 py-5">
                <span class="text-slate-900 dark:text-white font-bold">{{ sch.quiz?.title }}</span>
              </td>
              <td class="px-6 py-5 text-slate-600 dark:text-slate-400 font-medium">{{ sch.classroom?.name }}</td>
              <td class="px-6 py-5 text-slate-600 dark:text-slate-400 font-medium">{{ formatDateTime(sch.start_time) }}</td>
              <td class="px-6 py-5 text-slate-600 dark:text-slate-400 font-medium">{{ formatDateTime(sch.end_time) }}</td>
              <td class="px-6 py-5">
                <span :class="`px-2 py-0.5 rounded text-[10px] font-bold uppercase ${sch.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'}`">
                  {{ sch.is_active ? 'Aktif' : 'Non-aktif' }}
                </span>
              </td>
              <td class="px-6 py-5 text-right">
                <button class="p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 rounded text-slate-400 hover:text-red-500 transition-colors">
                  <span class="material-icons text-sm">delete</span>
                </button>
              </td>
            </tr>
            <tr v-if="schedules.length === 0">
              <td colspan="6" class="px-6 py-20 text-center text-slate-400">
                <p class="font-bold">Belum ada jadwal quiz yang diatur.</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <Transition name="fade">
      <div v-if="isModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="isModalOpen = false"></div>
        <div class="relative bg-white dark:bg-surface-dark w-full max-w-lg rounded-[32px] p-8 border border-slate-100 dark:border-slate-800">
          <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-6">Atur Jadwal Quiz</h3>
          <form @submit.prevent="submitSchedule" class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Pilih Quiz</label>
              <select v-model="form.quiz_id" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-sm font-bold">
                <option v-for="q in quizzes" :key="q.id" :value="q.id">{{ q.title }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Pilih Kelas</label>
              <select v-model="form.classroom_id" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-sm font-bold">
                <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Mulai</label>
                <input v-model="form.start_time" type="datetime-local" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-sm font-bold"/>
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Selesai</label>
                <input v-model="form.end_time" type="datetime-local" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-sm font-bold"/>
              </div>
            </div>
            <div class="flex gap-3 pt-6">
              <button type="button" @click="isModalOpen = false" class="flex-1 px-6 py-3 rounded-xl border-2 border-slate-100 dark:border-slate-800 font-bold text-slate-400">Batal</button>
              <button type="submit" :disabled="submitting" class="flex-1 px-6 py-3 rounded-xl bg-primary text-white font-bold shadow-lg shadow-primary/20 disabled:opacity-50">
                {{ submitting ? 'Menyimpan...' : 'Simpan Jadwal' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useNotificationStore } from '../../stores/notification'

const notification = useNotificationStore()
const schedules = ref([])
const quizzes = ref([])
const classrooms = ref([])
const isModalOpen = ref(false)
const submitting = ref(false)
const form = ref({ quiz_id: '', classroom_id: '', start_time: '', end_time: '' })

const fetchData = async () => {
  try {
    const token = localStorage.getItem('access_token')
    const headers = { Authorization: `Bearer ${token}` }
    const [scRes, qzRes, clRes] = await Promise.all([
      axios.get('http://localhost:8000/api/guru/schedules', { headers }),
      axios.get('http://localhost:8000/api/guru/quizzes', { headers }),
      axios.get('http://localhost:8000/api/guru/classrooms', { headers })
    ])
    schedules.value = scRes.data
    quizzes.value = qzRes.data
    classrooms.value = clRes.data
  } catch (err) {
    console.error(err)
  }
}

const submitSchedule = async () => {
  submitting.value = true
  try {
    const token = localStorage.getItem('access_token')
    await axios.post('http://localhost:8000/api/guru/schedules', form.value, {
      headers: { Authorization: `Bearer ${token}` }
    })
    notification.add({ title: 'Berhasil!', message: 'Jadwal quiz telah diatur.', type: 'success' })
    isModalOpen.value = false
    fetchData()
  } catch (err) {
    notification.add({ title: 'Gagal!', message: 'Gagal mengatur jadwal.', type: 'error' })
  } finally {
    submitting.value = false
  }
}

const formatDateTime = (date) => {
  return new Date(date).toLocaleString('id-ID', {
    day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit'
  })
}

onMounted(fetchData)
</script>
