<template>
  <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="space-y-1">
        <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
          <router-link to="/admin/dashboard" class="hover:text-primary transition-colors">Beranda</router-link>
          <span class="material-icons text-[12px]">chevron_right</span>
          <span class="text-slate-600 dark:text-slate-300">Profile</span>
        </nav>
      </div>
      
      <div class="flex items-center gap-4">

        <div class="hidden sm:flex items-center bg-white dark:bg-surface-dark rounded-2xl px-4 py-2.5 border border-slate-200 dark:border-slate-700 shadow-sm group focus-within:ring-2 focus-within:ring-primary/20 transition-all">
          <span class="material-icons text-slate-400 group-focus-within:text-primary transition-colors mr-2 text-xl">search</span>
          <input 
            type="text" 
            placeholder="Cari pengaturan..." 
            class="bg-transparent border-none p-0 text-sm focus:ring-0 w-48 lg:w-64 font-bold text-slate-700 dark:text-slate-200 placeholder:text-slate-300" 
          />
        </div>
      </div>
    </div>


    <div>
      <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tighter uppercase mb-2">
        Profile
      </h1>
      <p class="text-sm font-medium text-slate-500 dark:text-slate-400 max-w-2xl">
        {{ currentTabDescription }}
      </p>
    </div>


    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">

      <div class="xl:col-span-12 space-y-8">
        <div v-if="activeTab === 'profile'" class="space-y-8 animate-in fade-in slide-in-from-right-4 duration-500">

          <div class="bg-white dark:bg-surface-dark rounded-[32px] border border-slate-100 dark:border-white/5 shadow-sm overflow-hidden">
            <div class="p-8 sm:p-10 border-b border-slate-50 dark:border-white/5">
              <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight mb-1">Identitas Sekolah</h3>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Informasi resmi mengenai instansi Anda.</p>
            </div>
            
            <div class="p-8 sm:p-10 space-y-8">
              <div class="space-y-2.5">
                <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Nama Instansi</label>
                <div class="relative group">
                  <input 
                    v-model="settings.school_name"
                    type="text" 
                    class="w-full px-6 py-4 bg-slate-100 dark:bg-slate-800 border-none focus:ring-2 focus:ring-cyan-500/20 rounded-2xl text-sm font-bold text-slate-800 dark:text-white transition-all outline-none"
                    placeholder="Masukkan nama sekolah"
                  />
                </div>
              </div>

              <div class="space-y-2.5">
                <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Alamat Sekolah</label>
                <textarea 
                  v-model="settings.address"
                  rows="3"
                  class="w-full px-6 py-4 bg-slate-100 dark:bg-slate-800 border-none focus:ring-2 focus:ring-cyan-500/20 rounded-2xl text-sm font-bold text-slate-800 dark:text-white transition-all outline-none resize-none"
                  placeholder="Masukkan alamat lengkap"
                ></textarea>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2.5">
                  <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Email Kontak</label>
                  <input 
                    v-model="settings.contact_email"
                    type="email" 
                    class="w-full px-6 py-4 bg-slate-100 dark:bg-slate-800 border-none focus:ring-2 focus:ring-cyan-500/20 rounded-2xl text-sm font-bold text-slate-800 dark:text-white transition-all outline-none"
                    placeholder="admin@sekolah.sch.id"
                  />
                </div>
                <div class="space-y-2.5">
                  <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Nomor Telepon</label>
                  <input 
                    v-model="settings.phone"
                    type="text" 
                    class="w-full px-6 py-4 bg-slate-100 dark:bg-slate-800 border-none focus:ring-2 focus:ring-cyan-500/20 rounded-2xl text-sm font-bold text-slate-800 dark:text-white transition-all outline-none"
                    placeholder="+62 21 555 0123"
                  />
                </div>
              </div>
            </div>
          </div>


          <div class="bg-white dark:bg-surface-dark rounded-[32px] border border-slate-100 dark:border-white/5 shadow-sm overflow-hidden">
            <div class="p-8 sm:p-10 border-b border-slate-50 dark:border-white/5">
              <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight mb-1">Branding & Logo</h3>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ubah tampilan brand agar sesuai dengan identitas sekolah.</p>
            </div>

            <div class="p-8 sm:p-10 space-y-8">
              <div class="space-y-4">
                <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Logo Sekolah</label>
                <div class="flex flex-col md:flex-row items-center gap-10">
                  <div class="w-32 h-32 rounded-[32px] bg-slate-50 dark:bg-slate-800 border-2 border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center overflow-hidden shrink-0 group hover:border-cyan-500/50 transition-colors">
                    <img v-if="logoPreview" :src="logoPreview" alt="Logo" class="w-full h-full object-contain p-4" />
                    <span v-else class="material-icons text-4xl text-slate-300">image</span>
                  </div>
                  
                  <div class="flex-1 w-full">
                    <div class="border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-3xl p-8 text-center group hover:border-cyan-500/50 transition-all cursor-pointer bg-slate-50/50 dark:bg-slate-800/50 relative">
                      <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" @change="handleLogoChange" accept="image/*" />
                      <div class="flex flex-col items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center text-slate-400 group-hover:text-cyan-500 transition-colors">
                          <span class="material-icons">cloud_upload</span>
                        </div>
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-relaxed">
                          Tarik dan lepas file di sini atau <span class="text-cyan-500">Pilih File</span>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <div class="p-8 sm:p-10 bg-slate-50/50 dark:bg-slate-800/50 border-t border-slate-50 dark:border-white/5 flex justify-end">
               <button 
                @click="saveSettings"
                :disabled="loading"
                class="flex items-center gap-2 px-8 py-4 bg-primary text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-primary-hover shadow-xl shadow-primary/20 transition-all active:scale-95 disabled:opacity-50"
              >
                <span class="material-icons text-lg">check_circle</span>
                Simpan Konfigurasi
              </button>
            </div>
          </div>
        </div>


        <div v-if="showSuccessToast" class="fixed bottom-8 right-8 z-[100] animate-in slide-in-from-right-10 duration-500">
           <div class="bg-white dark:bg-surface-dark border border-slate-100 dark:border-white/5 rounded-[24px] shadow-2xl p-6 flex items-center gap-4 min-w-[320px]">
              <div class="w-12 h-12 rounded-2xl bg-green-500/10 text-green-500 flex items-center justify-center shrink-0">
                <span class="material-icons">check_circle</span>
              </div>
              <div class="flex-1">
                <h4 class="text-sm font-black text-slate-800 dark:text-white mb-0.5">Berhasil Disimpan!</h4>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pengaturan profil sekolah telah diperbarui.</p>
              </div>
              <button @click="showSuccessToast = false" class="text-slate-300 hover:text-slate-500 transition-colors">
                <span class="material-icons text-lg">close</span>
              </button>
           </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useNotificationStore } from '../../stores/notification'
import { useBrandingStore } from '../../stores/branding'

const notification = useNotificationStore()
const branding = useBrandingStore()
const loading = ref(false)
const activeTab = ref('profile')
const showSuccessToast = ref(false)
const logoPreview = ref(branding.logo)

const tabs = [
  { id: 'profile', name: 'Profil', icon: 'person' }
]

const settings = ref({
  school_name: branding.schoolName,
  address: branding.schoolAddress,
  contact_email: branding.schoolEmail,
  phone: branding.schoolPhone
})

const currentTabName = computed(() => 'Profile')

const currentTabDescription = computed(() => {
  return 'Konfigurasi identitas sekolah dan sistem utama platform Sekolah Media Belajar Quiz.'
})

const handleLogoChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (event) => {
      logoPreview.value = event.target.result
      // Immediate navbar update
      branding.setLogo(logoPreview.value)
    }
    reader.readAsDataURL(file)
  }
}

const saveSettings = async () => {
  loading.value = true
  try {
    // Simulate API call
    setTimeout(() => {
      // Update global store
      branding.updateBranding({
        schoolName: settings.value.school_name,
        address: settings.value.address,
        email: settings.value.contact_email,
        phone: settings.value.phone,
        logo: logoPreview.value
      })
      
      showSuccessToast.value = true
      loading.value = false
      setTimeout(() => {
        showSuccessToast.value = false
      }, 5000)
    }, 800)
  } catch (err) {
    notification.add({ title: 'Gagal', message: 'Gagal menyimpan pengaturan', type: 'error' })
    loading.value = false
  }
}

onMounted(() => {
  // Load initial settings if any
})
</script>

<style scoped>
.animate-in {
  animation-duration: 0.5s;
  animation-fill-mode: both;
}

textarea:focus {
  border-color: rgba(6, 182, 212, 0.2);
}

/* Custom transitions */
.router-link-active {
  background: transparent !important;
  color: inherit !important;
}
</style>
