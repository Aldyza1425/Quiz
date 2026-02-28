<template>
  <div v-if="store.confirmData">
    <div class="fixed inset-0 z-[160] flex items-center justify-center p-4">

      <div class="absolute inset-0 bg-slate-900/60" @click="store.resolveConfirm(false)"></div>
      

      <div class="relative bg-white dark:bg-surface-dark w-full max-w-md rounded-[32px] shadow-[0_40px_100px_-20px_rgba(0,0,0,0.3)] border border-slate-100 dark:border-slate-800 p-8 sm:p-10 overflow-hidden group text-center">

          <div class="absolute top-0 left-1/2 -translate-x-1/2 w-40 h-1 bg-primary/20 blur-xl"></div>

          <div class="flex flex-col items-center text-center">

            <div class="w-20 h-20 rounded-[28px] bg-primary/10 text-primary flex items-center justify-center mb-6 shadow-sm ring-8 ring-primary/5">
              <span class="material-icons text-4xl">help_outline</span>
            </div>

            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mb-3 tracking-tight">
              {{ store.confirmData.title }}
            </h3>
            <p class="text-slate-500 dark:text-slate-400 font-medium leading-relaxed mb-10 text-base sm:text-lg">
              {{ store.confirmData.message }}
            </p>

            <div class="flex flex-col sm:flex-row gap-3 w-full">
              <button 
                @click="store.resolveConfirm(false)"
                class="flex-1 px-8 py-4 rounded-2xl bg-red-50 dark:bg-red-500/10 border-2 border-red-100 dark:border-red-500/20 font-bold text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20 transition-all active:scale-95"
              >
                {{ store.confirmData.cancelText }}
              </button>
              <button 
                @click="store.resolveConfirm(true)"
                class="flex-1 px-8 py-4 rounded-2xl bg-slate-900 dark:bg-slate-800 hover:bg-primary text-white font-bold transition-all shadow-xl shadow-slate-900/10 active:scale-95 flex items-center justify-center"
              >
                {{ store.confirmData.confirmText }}
              </button>
            </div>
          </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useNotificationStore } from '../stores/notification'

const store = useNotificationStore()
</script>
