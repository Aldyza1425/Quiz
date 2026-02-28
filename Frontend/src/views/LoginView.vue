<template>
  <div class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-100 min-h-screen flex flex-col antialiased selection:bg-primary/30 relative overflow-hidden">
    <!-- Background Pattern/Gradient Layer -->
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
      <!-- Abstract educational shapes pattern -->
      <div class="absolute top-0 left-0 w-full h-full opacity-40 dark:opacity-10 bg-[radial-gradient(#13a4ec_1px,transparent_1px)] [background-size:20px_20px]"></div>
      <!-- Soft Gradient Orbs -->
      <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-primary/10 rounded-full blur-[120px]"></div>
      <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary/15 rounded-full blur-[100px]"></div>
    </div>

    <!-- Main Content Wrapper -->
    <main class="relative z-10 flex-grow flex items-center justify-center p-4 sm:p-6 lg:p-8">
      <!-- Login Card -->
      <div class="w-full max-w-md bg-white dark:bg-[#1a2c35] rounded-xl shadow-2xl border border-slate-100 dark:border-slate-800 overflow-hidden transition-all duration-300">
        <!-- Card Header / Brand -->
        <div class="pt-10 pb-6 px-8 text-center">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 text-primary mb-6 ring-4 ring-primary/5">
            <span class="material-icons text-4xl">school</span>
          </div>
          <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Welcome Back!</h2>
          <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
            Enter your details to access the <span class="text-primary font-medium">Sekolah Media Belajar</span> quiz portal.
          </p>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleLogin" class="px-8 pb-10 space-y-6">
          <div v-if="error" class="p-3 bg-red-100 border border-red-200 text-red-700 rounded-lg text-sm">
            {{ error }}
          </div>

          <!-- Email Field -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="email">Email Address</label>
            <div class="relative rounded-lg shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="material-icons text-slate-400 text-xl">mail_outline</span>
              </div>
              <input 
                v-model="form.email"
                class="block w-full pl-10 pr-3 py-3 border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary focus:border-primary dark:bg-slate-800/50 sm:text-sm transition-colors duration-200" 
                id="email" 
                placeholder="student@school.com" 
                required 
                type="email"
              />
            </div>
          </div>

          <!-- Password Field -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="password">Password</label>
            </div>
            <div class="relative rounded-lg shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="material-icons text-slate-400 text-xl">lock_outline</span>
              </div>
              <input 
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="block w-full pl-10 pr-10 py-3 border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary focus:border-primary dark:bg-slate-800/50 sm:text-sm transition-colors duration-200" 
                id="password" 
                placeholder="••••••••" 
                required
              />
              <button 
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none" 
                type="button"
              >
                <span class="material-icons text-xl">{{ showPassword ? 'visibility' : 'visibility_off' }}</span>
              </button>
            </div>
          </div>

          <!-- Remember Me & Forgot Password -->
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input class="h-4 w-4 text-primary focus:ring-primary border-slate-300 rounded dark:bg-slate-800 dark:border-slate-600 cursor-pointer" id="remember-me" type="checkbox"/>
              <label class="ml-2 block text-sm text-slate-600 dark:text-slate-400 cursor-pointer select-none" for="remember-me">
                Remember me
              </label>
            </div>
            <div class="text-sm">
              <a class="font-medium text-primary hover:text-primary-hover transition-colors" href="#">
                Forgot password?
              </a>
            </div>
          </div>

          <!-- Submit Button -->
          <div>
            <button 
              :disabled="loading"
              class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200 transform hover:-translate-y-0.5 shadow-primary/30 hover:shadow-lg hover:shadow-primary/40 disabled:opacity-50 disabled:transform-none" 
              type="submit"
            >
              <span v-if="loading">Signing in...</span>
              <span v-else>Sign In</span>
            </button>
          </div>

          <div class="pt-2 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-6">
              Belum punya akun? 
              <router-link to="/" class="text-primary hover:text-primary-hover font-black ml-1 transition-colors">Daftar Sekarang</router-link>
            </p>
          </div>
        </form>
        <!-- Decorative Bottom Stripe -->
        <div class="h-1.5 w-full bg-gradient-to-r from-primary/60 via-primary to-primary/60"></div>
      </div>
    </main>

    <!-- Simple Footer -->
    <footer class="relative z-10 py-6 text-center text-sm text-slate-500 dark:text-slate-400">
      <p class="text-xs text-slate-400 dark:text-slate-500">© 2024 Sekolah Media Belajar. All rights reserved.</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/configs/api'

const router = useRouter()
const showPassword = ref(false)
const loading = ref(false)
const error = ref('')

const form = reactive({
  email: '',
  password: ''
})

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  try {
    const response = await api.post('/login', form)
    localStorage.setItem('access_token', response.data.access_token)
    localStorage.setItem('user', JSON.stringify(response.data.user))
    
    const role = response.data.user.role
    if (role === 'admin') router.push('/admin/dashboard')
    else if (role === 'guru') router.push('/guru/dashboard')
    else router.push('/siswa/quizzes')
    
  } catch (err) {
    error.value = err.response?.data?.message || 'Login gagal. Periksa kembali email dan password Anda.'
  } finally {
    loading.value = false
  }
}
</script>
