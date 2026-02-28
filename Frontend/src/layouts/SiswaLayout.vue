<template>
  <div class="min-h-screen bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200">
    <!-- Student Header -->
    <header class="sticky top-0 h-16 bg-surface-light dark:bg-surface-dark border-b border-border-light dark:border-border-dark flex items-center justify-between px-4 sm:px-8 z-[100] backdrop-blur-md bg-white/80 dark:bg-surface-dark/80">
      <div class="flex items-center gap-3">
        <button v-if="!router.currentRoute.value.path.includes('/siswa/quiz/')" @click="isMobileMenuOpen = !isMobileMenuOpen" class="md:hidden text-slate-500 hover:text-primary">
          <span class="material-icons">{{ isMobileMenuOpen ? 'close' : 'menu' }}</span>
        </button>
        <div class="flex items-center gap-3 text-primary font-bold text-lg sm:text-xl">
          <span class="material-icons">school</span>
          <span class="hidden xs:inline">SMB Quiz</span>
        </div>
      </div>
      
      <nav v-if="!router.currentRoute.value.path.includes('/siswa/quiz/')" class="hidden md:flex items-center gap-6">
        <router-link to="/siswa/quizzes" active-class="text-primary font-bold" class="text-sm font-medium hover:text-primary transition-all px-3 py-2 rounded-lg hover:bg-primary/5 active:scale-95">Daftar Quiz</router-link>
        <router-link to="/siswa/attempts" active-class="text-primary font-bold" class="text-sm font-medium hover:text-primary transition-all px-3 py-2 rounded-lg hover:bg-primary/5 active:scale-95">Riwayat Saya</router-link>
        <router-link to="/siswa/profile" active-class="text-primary font-bold" class="text-sm font-medium hover:text-primary transition-all px-3 py-2 rounded-lg hover:bg-primary/5 active:scale-95">Profil Saya</router-link>
      </nav>

      <div v-else class="hidden md:block">
        <div class="px-6 py-2 bg-slate-100 dark:bg-slate-800 rounded-full text-[10px] font-black uppercase tracking-widest text-slate-400">Quiz In Progress</div>
      </div>

      <div class="flex items-center gap-4">
        <ThemeToggle />
        <router-link to="/siswa/profile" class="flex items-center gap-3 group">
          <div class="text-right hidden sm:block">
            <div class="text-sm font-bold text-slate-900 dark:text-white leading-tight group-hover:text-primary transition-colors">{{ user?.name }}</div>
            <div class="text-[10px] text-slate-400 font-black uppercase tracking-widest">{{ user?.role }}</div>
          </div>
          
          <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 border-2 border-white dark:border-slate-700 shadow-sm overflow-hidden group-hover:border-primary/30 transition-all">
            <img v-if="user?.avatar_url" :src="getFullUrl(user.avatar_url)" class="w-full h-full object-cover" />
            <div v-else class="w-full h-full flex items-center justify-center text-primary">
              <span class="material-icons">account_circle</span>
            </div>
          </div>
        </router-link>

        <button v-if="!router.currentRoute.value.path.includes('/siswa/quiz/')" @click="handleLogout" class="p-2 rounded-full hover:bg-red-50 hover:text-red-500 transition-all text-slate-400">
          <span class="material-icons">logout</span>
        </button>
      </div>

      <!-- Mobile Menu Overlay -->
      <div v-if="isMobileMenuOpen" class="absolute top-16 left-0 w-full bg-white dark:bg-surface-dark border-b border-slate-200 dark:border-slate-800 p-4 flex flex-col gap-2 md:hidden animate-in slide-in-from-top duration-300">
        <router-link to="/siswa/quizzes" @click="isMobileMenuOpen = false" class="px-4 py-3 rounded-xl hover:bg-primary/10 hover:text-primary font-bold transition-all flex items-center gap-3">
          <span class="material-icons text-lg">quiz</span> Daftar Quiz
        </router-link>
        <router-link to="/siswa/attempts" @click="isMobileMenuOpen = false" class="px-4 py-3 rounded-xl hover:bg-primary/10 hover:text-primary font-bold transition-all flex items-center gap-3">
          <span class="material-icons text-lg">history</span> Riwayat Saya
        </router-link>
        <router-link to="/siswa/profile" @click="isMobileMenuOpen = false" class="px-4 py-3 rounded-xl hover:bg-primary/10 hover:text-primary font-bold transition-all flex items-center gap-3">
          <span class="material-icons text-lg">person</span> Profil Saya
        </router-link>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto p-4 sm:p-8">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import ThemeToggle from '../components/ThemeToggle.vue'

const router = useRouter()
const user = ref(null)
const isMobileMenuOpen = ref(false)

onMounted(() => {
  refreshUser()
  window.addEventListener('storage', refreshUser)
})

const refreshUser = () => {
  const userData = localStorage.getItem('user')
  if (userData) {
    user.value = JSON.parse(userData)
  }
}

const handleLogout = () => {
  localStorage.removeItem('access_token')
  localStorage.removeItem('user')
  router.push('/login')
}

const getFullUrl = (url) => {
  if (!url) return ''
  if (url.startsWith('http')) return url
  return `http://localhost:8000${url}`
}
</script>

