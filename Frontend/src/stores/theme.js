import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  const theme = ref(localStorage.getItem('theme') || 'system')

  const applyTheme = (newTheme) => {
    const root = window.document.documentElement
    root.classList.remove('light', 'dark')

    let effectiveTheme = newTheme
    if (newTheme === 'system') {
      effectiveTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    }

    root.classList.add(effectiveTheme)
    localStorage.setItem('theme', newTheme)
  }

  // Handle system preference changes
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (theme.value === 'system') {
      applyTheme('system')
    }
  })

  const toggleTheme = () => {
    if (theme.value === 'light') theme.value = 'dark'
    else if (theme.value === 'dark') theme.value = 'system'
    else theme.value = 'light'
    
    applyTheme(theme.value)
  }

  const setTheme = (newTheme) => {
    theme.value = newTheme
    applyTheme(newTheme)
  }

  // Initialize
  applyTheme(theme.value)

  return {
    theme,
    toggleTheme,
    setTheme
  }
})
