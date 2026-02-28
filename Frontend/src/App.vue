<template>
  <main class="min-h-screen bg-slate-50 dark:bg-background-dark">
    <FlashMessage />
    <ConfirmModal />
    <router-view />
  </main>
</template>

<script setup>
import FlashMessage from './components/FlashMessage.vue'
import ConfirmModal from './components/ConfirmModal.vue'
// Main App logic
</script>
