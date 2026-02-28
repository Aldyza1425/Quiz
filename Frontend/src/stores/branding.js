import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export const useBrandingStore = defineStore('branding', () => {
  // Helper to get string from localStorage Safely
  const getSafeStorage = (key, fallback) => {
    const val = localStorage.getItem(key)
    if (val === '[object Object]' || !val) return fallback
    return val
  }

  const schoolName = ref(getSafeStorage('school_name', 'SMB Admin'))
  const schoolAddress = ref(getSafeStorage('school_address', 'Jl. Pendidikan No. 123, Kota Cerdas, Indonesia, 10110'))
  const schoolEmail = ref(getSafeStorage('school_email', 'admin@sekolahmediabelajar.sch.id'))
  const schoolPhone = ref(getSafeStorage('school_phone', '62 21 555 0123'))
  const logo = ref(localStorage.getItem('school_logo') || null)

  const updateBranding = (data) => {
    // If data is accidentally a string (old signature call), ignore or handle it
    if (typeof data !== 'object') return

    if (data.schoolName && typeof data.schoolName === 'string') {
      schoolName.value = data.schoolName
      localStorage.setItem('school_name', data.schoolName)
    }
    if (data.address !== undefined) {
      schoolAddress.value = data.address
      localStorage.setItem('school_address', data.address)
    }
    if (data.email !== undefined) {
      schoolEmail.value = data.email
      localStorage.setItem('school_email', data.email)
    }
    if (data.phone !== undefined) {
      schoolPhone.value = data.phone
      localStorage.setItem('school_phone', data.phone)
    }
    if (data.logo !== undefined) {
      logo.value = data.logo
      if (data.logo) {
        localStorage.setItem('school_logo', data.logo)
      } else {
        localStorage.removeItem('school_logo')
      }
    }
  }

  const setLogo = (newLogo) => {
    logo.value = newLogo
    if (newLogo) {
      localStorage.setItem('school_logo', newLogo)
    } else {
      localStorage.removeItem('school_logo')
    }
  }

  return {
    schoolName,
    schoolAddress,
    schoolEmail,
    schoolPhone,
    logo,
    updateBranding,
    setLogo
  }
})
