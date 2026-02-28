<template>
  <main class="min-h-screen bg-slate-50 dark:bg-background-dark">
    <FlashMessage />
    <ConfirmModal />
    <router-view />
  </main>
</template>

<script setup>
import { watch, onMounted } from 'vue'
import { useBrandingStore } from './stores/branding'
import FlashMessage from './components/FlashMessage.vue'
import ConfirmModal from './components/ConfirmModal.vue'

const branding = useBrandingStore()

// Update title and favicon
const updateBrowserTab = () => {
  if (branding.schoolName) {
    document.title = branding.schoolName + ' - Quiz'
  }
  
  if (branding.logo) {
    let link = document.querySelector("link[rel~='icon']")
    if (!link) {
      link = document.createElement('link')
      link.rel = 'icon'
      document.getElementsByTagName('head')[0].appendChild(link)
    }
    link.href = branding.logo
  }
}

// Watch for changes in branding logo or school name
watch(() => [branding.logo, branding.schoolName], () => {
  updateBrowserTab()
})

onMounted(() => {
  updateBrowserTab()
})
</script>
