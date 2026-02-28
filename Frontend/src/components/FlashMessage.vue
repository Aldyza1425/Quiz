<template>
  <div class="fixed top-6 left-1/2 -translate-x-1/2 z-[150] flex flex-col gap-3 w-full max-w-sm pointer-events-none px-4">
      <div 
        v-for="n in store.notifications" 
        :key="n.id"
        class="pointer-events-auto bg-white dark:bg-surface-dark rounded-[24px] shadow-[0_25px_60px_-15px_rgba(0,0,0,0.15)] border border-slate-100 dark:border-slate-800 overflow-hidden relative group"
      >
        <div class="p-5 flex items-start gap-4">

          <div :class="`shrink-0 w-11 h-11 rounded-full flex items-center justify-center ${iconBg(n.type)}`">
            <span class="material-icons text-white text-2xl">
              {{ n.type === 'success' ? 'check' : (n.type === 'error' ? 'close' : 'priority_high') }}
            </span>
          </div>


          <div class="flex-1 pt-1">
            <h4 class="font-bold text-slate-900 dark:text-white leading-tight mb-1 text-base">{{ n.title }}</h4>
            <p class="text-[13px] text-slate-500 dark:text-slate-400 leading-snug font-medium">{{ n.message }}</p>
          </div>


          <button @click="store.remove(n.id)" class="text-slate-300 hover:text-slate-500 transition-colors mt-1">
            <span class="material-icons text-xl">close</span>
          </button>
        </div>


        <div class="absolute bottom-0 left-0 h-1 w-full bg-slate-50 dark:bg-slate-900/10">
          <div 
            :class="`h-full transition-all duration-[5000ms] ease-linear ${progressBg(n.type)}`"
            :style="{ width: '100%', animation: `shrink ${n.duration}ms linear forwards` }"
          ></div>
        </div>
      </div>
  </div>
</template>

<script setup>
import { useNotificationStore } from '../stores/notification'

const store = useNotificationStore()

const iconBg = (type) => {
  if (type === 'success') return 'bg-[#10b981]' // Vibrant Emerald
  if (type === 'error') return 'bg-[#ef4444]'
  return 'bg-[#3b82f6]'
}

const progressBg = (type) => {
  if (type === 'success') return 'bg-[#10b981]'
  if (type === 'error') return 'bg-[#ef4444]'
  return 'bg-[#3b82f6]'
}
</script>

<style scoped>
@keyframes shrink {
  from { width: 100%; }
  to { width: 0%; }
}
</style>
