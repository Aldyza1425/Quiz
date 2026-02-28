<template>
  <button 
    @click="themeStore.toggleTheme()" 
    class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-all flex items-center justify-center group relative shadow-sm border border-slate-200 dark:border-slate-700 active:scale-95"
    :title="`Theme: ${themeStore.theme.toUpperCase()}`"
  >
    <div class="relative w-6 h-6 flex items-center justify-center">
      <!-- Transition wrapper for icons -->
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 scale-50 rotate-90"
        enter-to-class="opacity-100 scale-100 rotate-0"
        leave-active-class="transition duration-150 ease-in absolute"
        leave-from-class="opacity-100 scale-100 rotate-0"
        leave-to-class="opacity-0 scale-50 -rotate-90"
      >
        <span v-if="themeStore.theme === 'light'" key="light" class="material-icons text-xl">wb_sunny</span>
        <span v-else-if="themeStore.theme === 'dark'" key="dark" class="material-icons text-xl">dark_mode</span>
        <span v-else key="system" class="material-icons text-xl">devices</span>
      </Transition>
    </div>
    
    <!-- Tooltip (subtle) -->
    <span class="absolute -bottom-10 left-1/2 -translate-x-1/2 px-2 py-1 bg-slate-800 text-white text-[10px] font-bold rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap uppercase tracking-widest z-[100]">
      {{ themeStore.theme }}
    </span>
  </button>
</template>

<script setup>
import { useThemeStore } from '../stores/theme'

const themeStore = useThemeStore()
</script>
