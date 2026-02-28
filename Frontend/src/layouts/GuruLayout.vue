<template>
  <div class="flex h-screen bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200">

    <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[100] md:hidden"></div>


    <aside :class="`fixed md:static inset-y-0 left-0 z-[110] w-64 bg-surface-light dark:bg-surface-dark border-r border-border-light dark:border-border-dark transition-transform duration-300 transform ${isSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'}`">
      <div class="p-6 h-full flex flex-col">
        <div class="flex items-center justify-between mb-10">
          <div class="flex items-center gap-3 text-primary font-bold text-xl">
            <template v-if="branding.logo">
              <img :src="branding.logo" alt="Logo" class="w-8 h-8 object-contain" />
            </template>
            <span v-else class="material-icons">school</span>
            <span class="truncate max-w-[120px]">{{ branding.schoolName }}</span>
          </div>
          <button @click="isSidebarOpen = false" class="md:hidden text-slate-400 hover:text-primary">
            <span class="material-icons">close</span>
          </button>
        </div>
        
        <nav class="space-y-1 flex-1 overflow-y-auto pr-2 custom-scrollbar">
          <router-link to="/guru/dashboard" @click="isSidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-primary/10 hover:text-primary transition-all group font-bold">
            <span class="material-icons text-slate-400 group-hover:text-primary">dashboard</span>
            <span>Dashboard</span>
          </router-link>
          
          <div class="pt-4 pb-2 text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Akademik</div>
          
          <router-link to="/guru/jadwal" @click="isSidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-primary/10 hover:text-primary transition-all group font-bold text-sm">
            <span class="material-icons text-slate-400 group-hover:text-primary">event_note</span>
            <span>Jadwal Mengajar</span>
          </router-link>
          <router-link to="/guru/absen" @click="isSidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-primary/10 hover:text-primary transition-all group font-bold text-sm">
            <span class="material-icons text-slate-400 group-hover:text-primary">fact_check</span>
            <span>Absensi Siswa</span>
          </router-link>
          <router-link to="/guru/nilai" @click="isSidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-primary/10 hover:text-primary transition-all group font-bold text-sm">
            <span class="material-icons text-slate-400 group-hover:text-primary">analytics</span>
            <span>Pengelolaan Nilai</span>
          </router-link>


          <div class="pt-4 pb-2 text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Konten</div>

          <router-link to="/guru/quizzes" @click="isSidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-primary/10 hover:text-primary transition-all group font-bold text-sm">
            <span class="material-icons text-slate-400 group-hover:text-primary">quiz</span>
            <span>Bank Quiz</span>
          </router-link>
          <router-link to="/guru/activity-logs" @click="isSidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-primary/10 hover:text-primary transition-all group font-bold text-sm">
            <span class="material-icons text-slate-400 group-hover:text-primary">history</span>
            <span>Aktivitas Saya</span>
          </router-link>
          
          <div class="pt-6 mt-4 border-t border-slate-50 dark:border-slate-800">
            <button @click="handleLogout" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-50 hover:text-red-500 transition-all text-left font-black text-sm uppercase tracking-widest">
              <span class="material-icons">logout</span>
              <span>Keluar</span>
            </button>
          </div>
        </nav>
      </div>
    </aside>


    <main class="flex-1 flex flex-col overflow-hidden w-full">

      <header class="h-16 bg-surface-light dark:bg-surface-dark border-b border-border-light dark:border-border-dark flex items-center justify-between px-4 sm:px-8 z-[50] shrink-0">
        <div class="flex items-center gap-4">
          <button @click="isSidebarOpen = true" class="md:hidden text-slate-500 hover:text-primary p-1">
            <span class="material-icons">menu</span>
          </button>
          <div class="flex items-center gap-3">
             <template v-if="branding.logo">
                <img :src="branding.logo" alt="Logo" class="w-8 h-8 object-contain md:hidden" />
             </template>
             <h1 class="font-bold text-lg hidden sm:block text-primary">{{ branding.schoolName }} Guru Panel</h1>
          </div>
        </div>
        
        <div class="flex items-center gap-2 sm:gap-4">
          <ThemeToggle />
          <div class="text-right hidden xs:block">
            <div class="text-xs sm:text-sm font-semibold truncate max-w-[120px]">{{ user?.name }}</div>
            <div class="text-[10px] sm:text-xs text-slate-500 uppercase">{{ user?.role }}</div>
          </div>
          <div class="w-8 h-8 sm:w-10 h-10 rounded-full bg-secondary/20 flex items-center justify-center text-secondary font-bold text-sm sm:text-base shrink-0 border border-border-light dark:border-border-dark">
            {{ user?.name?.charAt(0) }}
          </div>
        </div>
      </header>


      <div class="flex-1 overflow-y-auto p-4 sm:p-8">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import ThemeToggle from '../components/ThemeToggle.vue'
import { useBrandingStore } from '../stores/branding'

const branding = useBrandingStore()
const router = useRouter()
const user = ref(null)
const isSidebarOpen = ref(false)

onMounted(() => {
  const userData = localStorage.getItem('user')
  if (userData) {
    user.value = JSON.parse(userData)
  }
})

const handleLogout = () => {
  localStorage.removeItem('access_token')
  localStorage.removeItem('user')
  router.push('/login')
}
</script>

<style scoped>
.router-link-active {
  background-color: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
  font-weight: 800;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: transparent;
  border-radius: 10px;
}
.custom-scrollbar:hover::-webkit-scrollbar-thumb {
  background: rgba(0,0,0,0.1);
}
</style>
