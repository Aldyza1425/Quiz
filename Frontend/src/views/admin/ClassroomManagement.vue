<template>
  <div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight uppercase">Manajemen Kelas</h1>
        <p class="text-sm text-slate-500 font-medium">Pusat kendali seluruh kelas, guru pengajar, dan data siswa.</p>
      </div>
      <div class="flex flex-wrap items-center gap-3">

        <router-link to="/admin/classrooms/add" class="flex items-center gap-2 px-6 py-3 bg-primary border border-transparent rounded-2xl text-[11px] sm:text-sm font-bold text-white hover:bg-primary-hover transition-all whitespace-nowrap shadow-lg shadow-primary/20">
          <span class="material-icons text-sm sm:text-lg">add_business</span>
          Tambah Kelas
        </router-link>
      </div>
    </div>




    <div key="list" class="space-y-6">

      <div class="flex flex-col sm:flex-row gap-4 items-center justify-between bg-white dark:bg-surface-dark p-4 rounded-[24px] border border-slate-100 dark:border-slate-800">
          <div class="flex items-center gap-4 w-full sm:w-auto">
             <div class="relative w-full sm:w-64 group">
               <button 
                 @click.stop="isYearDropdownOpen = !isYearDropdownOpen"
                 class="w-full flex items-center justify-between pl-12 pr-4 py-2.5 bg-white dark:bg-slate-800 border-2 border-primary rounded-2xl text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-white transition-all shadow-sm active:scale-[0.98]"
               >
                 <span class="material-icons absolute left-4 text-primary text-sm">calendar_month</span>
                 <span>{{ selectedYear === 'All' ? 'Semua Tahun' : 'Tahun ' + selectedYear }}</span>
                 <span class="material-icons text-primary text-lg transition-transform duration-300" :class="{ 'rotate-180': isYearDropdownOpen }">expand_more</span>
               </button>

               <Transition
                 enter-active-class="transition duration-200 ease-out"
                 enter-from-class="opacity-0 scale-95 -translate-y-2"
                 enter-to-class="opacity-100 scale-100 translate-y-0"
                 leave-active-class="transition duration-150 ease-in"
                 leave-from-class="opacity-100 scale-100 translate-y-0"
                 leave-to-class="opacity-0 scale-95 -translate-y-2"
               >
                 <div v-if="isYearDropdownOpen" class="absolute top-full left-0 w-full mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 py-2 z-50 overflow-hidden">
                   <button 
                     v-for="year in ['All', '2025/2026', '2024/2025', '2023/2024']" 
                     :key="year"
                     @click="selectedYear = year; isYearDropdownOpen = false"
                     :class="`w-full px-5 py-3 text-left text-[11px] font-black uppercase tracking-widest transition-all ${selectedYear === year ? 'bg-primary text-white' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-primary'}`"
                   >
                     {{ year === 'All' ? 'Semua Tahun' : 'Tahun ' + year }}
                   </button>
                 </div>
               </Transition>
             </div>
             <div class="h-8 w-px bg-slate-100 dark:bg-slate-800"></div>
             <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Total {{ filteredClassroomsList.length }} Kelas</span>
          </div>
        <div class="flex items-center gap-2 w-full sm:w-auto bg-slate-50 dark:bg-slate-800 px-4 py-2.5 rounded-2xl border border-slate-100 dark:border-slate-700 group focus-within:ring-2 focus-within:ring-primary/20 transition-all">
          <span class="material-icons text-slate-400 group-focus-within:text-primary transition-colors text-sm">search</span>
          <input v-model="searchQuery" type="text" placeholder="Cari nama kelas..." class="bg-transparent border-none p-0 text-[11px] focus:ring-0 w-full font-black uppercase tracking-wider placeholder:text-slate-300 dark:placeholder:text-slate-600" />
        </div>
        </div>



        <div class="hidden lg:block bg-white dark:bg-surface-dark rounded-[40px] border border-slate-100 dark:border-slate-800 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                  <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800 w-16 text-center">No</th>
                  <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800">Nama Kelas</th>
                  <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800">Tingkat</th>
                  <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800">Tahun Ajaran</th>
                  <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800">Wali Kelas</th>
                  <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800 text-center">Siswa</th>
                  <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800 text-center">Guru</th>
                  <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50 dark:divide-slate-800">

                <tr v-for="(c, idx) in filteredClassrooms" :key="c.id" class="hover:bg-slate-50/30 dark:hover:bg-slate-800/20 transition-all group">
                  <td class="px-8 py-5 text-sm font-black text-slate-400 text-center">{{ (currentPage - 1) * itemsPerPage + idx + 1 }}</td>
                  <td class="px-8 py-5">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-2xl bg-primary/5 text-primary flex items-center justify-center font-bold text-xs shrink-0">
                        {{ toRoman(c.grade_level) }}
                      </div>
                      <span class="font-bold text-slate-900 dark:text-white capitalize whitespace-nowrap">{{ c.name }}</span>
                    </div>
                  </td>
                  <td class="px-8 py-5">
                    <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-500 uppercase tracking-tighter whitespace-nowrap">Kelas {{ c.grade_level }}</span>
                  </td>
                  <td class="px-8 py-5 text-xs font-bold text-slate-400 tracking-widest">{{ c.academic_year }}</td>
                  <td class="px-8 py-5">
                    <div class="flex items-center gap-2">
                      <div class="w-6 h-6 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center text-[10px] font-bold uppercase shrink-0">
                        {{ c.homeroom_teacher_name?.charAt(0) || '?' }}
                      </div>
                      <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[150px]">{{ c.homeroom_teacher_name || 'BELUM ADA' }}</span>
                    </div>
                  </td>
                  <td class="px-8 py-5 text-center">
                    <div class="inline-flex px-3 py-1 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 font-black text-xs">
                      {{ c.students_count }}
                    </div>
                  </td>
                  <td class="px-8 py-5 text-center">
                    <div class="inline-flex px-3 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 font-black text-xs">
                      {{ c.teachers_count || 0 }}
                    </div>
                  </td>
                  <td class="px-8 py-5 text-right">
                    <div class="flex justify-end gap-2">
                      <button @click="$router.push(`/admin/classrooms/${c.id}`)" title="Lihat Detail Kelas" class="w-9 h-9 flex items-center justify-center rounded-xl bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all active:scale-90">
                        <span class="material-icons text-sm">visibility</span>
                      </button>
                      <router-link :to="`/admin/classrooms/edit/${c.id}`" title="Edit Kelas" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-primary transition-all active:scale-90 border border-slate-100 dark:border-slate-700">
                        <span class="material-icons text-sm">edit</span>
                      </router-link>
                      <button @click="deleteClass(c)" title="Hapus Kelas" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-red-500 transition-all active:scale-90 border border-slate-100 dark:border-slate-700">
                        <span class="material-icons text-sm">delete_outline</span>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>


        <div class="lg:hidden space-y-4">
          <div v-for="(c, idx) in filteredClassrooms" :key="c.id" class="bg-white dark:bg-surface-dark border border-slate-100 dark:border-slate-800 rounded-3xl p-6 space-y-4 animate-in fade-in slide-in-from-bottom-2 duration-300" :style="{ animationDelay: `${idx * 50}ms` }">
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-black text-sm">
                  {{ toRoman(c.grade_level) }}
                </div>
                <div>
                  <h3 class="font-black text-slate-900 dark:text-white uppercase tracking-[0.05em]">{{ c.name }}</h3>
                  <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-secondary">{{ c.academic_year }}</span>
                </div>
              </div>
              <span class="px-3 py-1 rounded-lg bg-slate-50 dark:bg-slate-800 text-[9px] font-black text-slate-500 uppercase tracking-widest">Kelas {{ c.grade_level }}</span>
            </div>

            <div class="grid grid-cols-2 gap-3 py-4 border-y border-slate-50 dark:border-slate-800/50">
              <div class="space-y-1">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Wali Kelas</p>
                <div class="flex items-center gap-2">
                  <div class="w-5 h-5 rounded bg-secondary/10 text-secondary flex items-center justify-center text-[8px] font-black uppercase">
                    {{ c.homeroom_teacher_name?.charAt(0) || '?' }}
                  </div>
                  <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300 truncate">{{ c.homeroom_teacher_name || 'BELUM ADA' }}</span>
                </div>
              </div>
              <div class="flex items-center justify-end gap-3 text-right">
                <div class="space-y-1 text-center">
                  <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Siswa</p>
                  <span class="inline-block px-2 py-0.5 rounded bg-green-50 dark:bg-green-900/20 text-green-600 font-black text-[10px]">{{ c.students_count }}</span>
                </div>
                <div class="space-y-1 text-center">
                  <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Guru</p>
                  <span class="inline-block px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-900/20 text-blue-600 font-black text-[10px]">{{ c.teachers_count || 0 }}</span>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
              <button @click="$router.push(`/admin/classrooms/${c.id}`)" class="flex-1 py-3 bg-primary/10 text-primary rounded-2xl flex items-center justify-center gap-2 font-black text-[10px] uppercase tracking-widest hover:bg-primary hover:text-white transition-all">
                <span class="material-icons text-sm">visibility</span>
                Detail
              </button>
              <router-link :to="`/admin/classrooms/edit/${c.id}`" class="flex-1 py-3 bg-slate-50 dark:bg-slate-800 text-slate-500 rounded-2xl flex items-center justify-center gap-2 font-black text-[10px] uppercase tracking-widest hover:text-primary transition-all border border-slate-100 dark:border-slate-700">
                <span class="material-icons text-sm">edit</span>
                Edit
              </router-link>
              <button @click="deleteClass(c)" class="w-12 h-12 bg-slate-50 dark:bg-slate-800 text-slate-400 rounded-2xl flex items-center justify-center hover:text-red-500 transition-all border border-slate-100 dark:border-slate-700">
                <span class="material-icons text-sm">delete_outline</span>
              </button>
            </div>
          </div>
        </div>


        <div v-if="loading" class="bg-white dark:bg-surface-dark rounded-[40px] border border-slate-100 dark:border-slate-800 p-20 text-center">
          <div class="flex flex-col items-center justify-center gap-4">
            <div class="w-12 h-12 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] animate-pulse">Memuat data kelas...</p>
          </div>
        </div>

        <div v-else-if="filteredClassrooms.length === 0" class="bg-white dark:bg-surface-dark rounded-[40px] border border-slate-100 dark:border-slate-800 p-20 text-center">
            <div class="flex flex-col items-center justify-center gap-4 opacity-40">
              <span class="material-icons text-5xl text-slate-300">inventory_2</span>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Tidak Ada Data Kelas</p>
            </div>
        </div>


          <div class="flex flex-col lg:flex-row items-center justify-between px-8 py-6 bg-white dark:bg-surface-dark border border-slate-100 dark:border-white/5 rounded-[24px] shadow-sm gap-6">
            <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 w-full lg:w-auto">
              <div class="flex items-center gap-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Baris:</span>
                <div class="relative items-per-page-selector">
                  <button 
                    @click.stop="isItemsPerPageOpen = !isItemsPerPageOpen"
                    class="flex items-center gap-2 px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-white/5 rounded-xl text-[10px] font-black text-slate-700 dark:text-slate-200 transition-all hover:border-primary/50 shadow-sm"
                  >
                    <span>{{ itemsPerPage }}</span>
                    <span class="material-icons text-sm text-slate-400 transition-transform" :class="{'rotate-180': isItemsPerPageOpen}">expand_more</span>
                  </button>
                  
                  <div v-if="isItemsPerPageOpen" class="absolute bottom-full left-0 mb-2 w-20 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-100 dark:border-slate-700 py-1 z-50 overflow-hidden animate-in fade-in slide-in-from-bottom-1">
                    <button 
                      v-for="size in [5, 10, 15, 20, 50]" :key="size"
                      @click="itemsPerPage = size; currentPage = 1; isItemsPerPageOpen = false"
                      :class="`w-full px-4 py-2 text-left text-[10px] font-black transition-all ${itemsPerPage === size ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700'}`"
                    >
                      {{ size }}
                    </button>
                  </div>
                </div>
              </div>

              <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-center sm:text-left">
                MENAMPILKAN {{ filteredClassroomsList.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0 }} - {{ Math.min(currentPage * itemsPerPage, filteredClassroomsList.length) }} <span class="hidden sm:inline">DARI {{ filteredClassroomsList.length }} KELAS</span>
              </div>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2">
              <button 
                @click="currentPage > 1 && (currentPage--)"
                :disabled="currentPage === 1"
                class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-white/5 text-slate-400 hover:text-primary disabled:opacity-30 disabled:hover:text-slate-400 transition-all active:scale-90"
              >
                <span class="material-icons text-sm">chevron_left</span>
              </button>
              <div class="flex items-center gap-1 overflow-x-auto max-w-[150px] sm:max-w-none no-scrollbar">
                <button 
                  v-for="p in Math.ceil(filteredClassroomsList.length / itemsPerPage)" 
                  :key="p"
                  @click="currentPage = p"
                  :class="`min-w-[36px] min-h-[36px] sm:w-10 sm:h-10 flex items-center justify-center rounded-xl text-[10px] font-black transition-all ${currentPage === p ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-white dark:bg-slate-800 border border-slate-100 dark:border-white/5 text-slate-400 hover:bg-slate-50 transition-all'}`"
                >
                  {{ p }}
                </button>
              </div>
              <button 
                @click="currentPage < Math.ceil(filteredClassroomsList.length / itemsPerPage) && (currentPage++)"
                :disabled="currentPage === Math.ceil(filteredClassroomsList.length / itemsPerPage) || filteredClassroomsList.length === 0"
                class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-white/5 text-slate-400 hover:text-primary disabled:opacity-30 disabled:hover:text-slate-400 transition-all active:scale-90"
              >
                <span class="material-icons text-sm">chevron_right</span>
              </button>
            </div>
          </div>
      </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '../../configs/api.js'

const selectedYear = ref('All')
const isYearDropdownOpen = ref(false)
const isItemsPerPageOpen = ref(false)
const selectedClass = ref(null)


const classrooms = ref([])
const loading = ref(false)

onMounted(async () => {
  await fetchClassrooms()
  window.addEventListener('click', () => {
    isYearDropdownOpen.value = false
    isItemsPerPageOpen.value = false
  })
})

const fetchClassrooms = async () => {
  loading.value = true
  try {
    const response = await api.get('/admin/classrooms')
    classrooms.value = response.data
  } catch (error) {
    console.error('Failed to fetch classrooms:', error)
  } finally {
    loading.value = false
  }
}

// Edit mode is now handled by AddClassroom.vue

const toRoman = (grade) => {
  const map = { 10: 'X', 11: 'XI', 12: 'XII', '10': 'X', '11': 'XI', '12': 'XII' }
  return map[grade] || grade
}



// Removed drawer-based add/edit logic

const deleteClass = async (classroom) => {
  if (!confirm(`Apakah Anda yakin ingin menghapus kelas ${classroom.name}?`)) return
  
  try {
    await api.delete(`/admin/classrooms/${classroom.id}`)
    classrooms.value = classrooms.value.filter(c => c.id !== classroom.id)
  } catch (error) {
    alert(error.response?.data?.message || 'Gagal menghapus kelas')
  }
}

const currentPage = ref(1)
const itemsPerPage = ref(5)


const searchQuery = ref('')

const filteredClassroomsList = computed(() => {
  let list = classrooms.value
  
  // Filter by Search Query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    list = list.filter(c => 
      c.name.toLowerCase().includes(query) || 
      c.grade_level.toString().includes(query) ||
      c.academic_year.toLowerCase().includes(query)
    )
  }

  // Filter by Year
  if (selectedYear.value !== 'All') {
    list = list.filter(c => c.academic_year === selectedYear.value)
  }

  return list
})

const filteredClassrooms = computed(() => {
  const list = filteredClassroomsList.value
  return list.slice((currentPage.value - 1) * itemsPerPage.value, currentPage.value * itemsPerPage.value)
})

</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(0,0,0,0.05);
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.05);
}

.scrollbar-thin::-webkit-scrollbar {
  width: 4px;
}
.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.dark .scrollbar-thin::-webkit-scrollbar-thumb {
  background: #1e293b;
}
</style>
