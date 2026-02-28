<template>
  <div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-300">
    <!-- Header -->
    <div
      class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-4"
    >
      <div class="flex items-center gap-4">
        <router-link
          to="/admin/students"
          class="w-10 h-10 rounded-full flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-slate-400 hover:text-primary transition-all"
        >
          <span class="material-icons">west</span>
        </router-link>
        <div>
          <h1
            class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight uppercase"
          >
            {{ isEditMode ? "Edit Data Siswa" : "Tambah Siswa Baru" }}
          </h1>
          <p class="text-sm text-slate-500 font-medium">
            {{
              isEditMode
                ? "Perbarui informasi dan penempatan kelas siswa."
                : "Daftarkan siswa baru dan tentukan penempatan kelasnya."
            }}
          </p>
        </div>
      </div>
    </div>

    <!-- Form Card -->
    <div
      class="bg-white dark:bg-surface-dark border border-slate-100 dark:border-slate-800 rounded-[32px] overflow-hidden shadow-sm"
    >
      <div class="p-8 sm:p-12">
        <form @submit.prevent="handleSubmit" class="max-w-2xl space-y-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Nama -->
            <div class="space-y-3 md:col-span-2">
              <label
                class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none"
                >Nama Lengkap</label
              >
              <div class="relative group">
                <span
                  class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors"
                  >person</span
                >
                <input
                  v-model="form.name"
                  type="text"
                  placeholder="Contoh: Ahmad Fauzi"
                  class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-sm font-bold outline-none transition-all"
                  required
                />
              </div>
            </div>

            <!-- Email -->
            <div class="space-y-3">
              <label
                class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none"
                >Alamat Email</label
              >
              <div class="relative group">
                <span
                  class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors"
                  >email</span
                >
                <input
                  v-model="form.email"
                  type="email"
                  placeholder="siswa@sekolah.sch.id"
                  class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-sm font-bold outline-none transition-all"
                  required
                />
              </div>
            </div>

            <!-- NIS -->
            <div class="space-y-3">
              <label
                class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none"
                >NIS (Nomor Induk Siswa)</label
              >
              <div class="relative group">
                <span
                  class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors"
                  >badge</span
                >
                <input
                  v-model="form.nip"
                  type="text"
                  placeholder="Opsional"
                  class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-sm font-bold outline-none transition-all"
                />
              </div>
            </div>

            <!-- Classroom Select -->
            <div class="space-y-3 md:col-span-2 relative">
              <label
                class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none"
                >Penempatan Kelas</label
              >
              <div class="relative group">
                <span
                  class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-primary group-focus-within:text-primary transition-colors"
                  >meeting_room</span
                >
                <button
                  type="button"
                  @click.stop="
                    isClassroomDropdownOpen = !isClassroomDropdownOpen
                  "
                  class="w-full pl-14 pr-12 py-4 bg-slate-50 dark:bg-slate-800 border-2 border-primary/20 rounded-2xl focus:ring-2 focus:ring-primary/40 text-[11px] font-black uppercase tracking-widest outline-none transition-all cursor-pointer text-left flex items-center justify-between"
                >
                  <span class="text-slate-900 dark:text-white">{{
                    getClassroomName(form.classroom_id)
                  }}</span>
                  <span
                    class="material-icons text-primary opacity-60 transition-transform"
                    :class="{ 'rotate-180': isClassroomDropdownOpen }"
                    >expand_more</span
                  >
                </button>

                <Transition
                  enter-active-class="transition duration-200 ease-out"
                  enter-from-class="opacity-0 scale-95 -translate-y-2"
                  enter-to-class="opacity-100 scale-100 translate-y-0"
                  leave-active-class="transition duration-150 ease-in"
                  leave-from-class="opacity-100 scale-100 translate-y-0"
                  leave-to-class="opacity-0 scale-95 -translate-y-2"
                >
                  <div
                    v-if="isClassroomDropdownOpen"
                    class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-1 z-50 overflow-hidden"
                  >
                    <div class="max-h-[200px] overflow-y-auto custom-scrollbar">
                      <div
                        @click="
                          form.classroom_id = null;
                          isClassroomDropdownOpen = false;
                        "
                        :class="`px-5 py-3 text-[10px] font-black uppercase tracking-wider cursor-pointer transition-all ${form.classroom_id === null ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-primary'}`"
                      >
                        Pilih Kelas
                      </div>
                      <div
                        v-for="cls in classrooms"
                        :key="cls.id"
                        @click="
                          form.classroom_id = cls.id;
                          isClassroomDropdownOpen = false;
                        "
                        :class="`px-5 py-3 text-[10px] font-black uppercase tracking-wider cursor-pointer transition-all ${form.classroom_id === cls.id ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-primary'}`"
                      >
                        {{ cls.name }} - {{ cls.academic_year }}
                      </div>
                    </div>
                  </div>
                </Transition>
              </div>
            </div>

            <!-- Password -->
            <div class="space-y-3 md:col-span-2">
              <label
                class="px-1 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none"
              >
                {{
                  isEditMode ? "Ganti Password (Opsional)" : "Password Default"
                }}
              </label>
              <div class="relative group">
                <span
                  class="material-icons absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors"
                  >lock</span
                >
                <input
                  v-model="form.password"
                  type="password"
                  :placeholder="
                    isEditMode
                      ? 'Kosongkan jika tidak ingin mengubah'
                      : 'Minimal 8 karakter'
                  "
                  class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-sm font-bold outline-none transition-all"
                  :required="!isEditMode"
                />
              </div>
            </div>
          </div>

          <!-- Submit Buttons -->
          <div class="flex items-center gap-4 pt-6">
            <button
              type="submit"
              :disabled="loading"
              class="flex-1 py-4 bg-primary text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-primary-hover transition-all active:scale-95 disabled:opacity-50 shadow-lg shadow-primary/20"
            >
              <div class="flex items-center justify-center gap-2">
                <span v-if="loading" class="material-icons animate-spin text-sm"
                  >sync</span
                >
                <span>{{
                  loading
                    ? "Memproses..."
                    : isEditMode
                      ? "Simpan Perubahan"
                      : "Daftarkan Siswa"
                }}</span>
              </div>
            </button>
            <router-link
              to="/admin/students"
              class="px-8 py-4 bg-slate-50 dark:bg-slate-800 text-slate-500 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-100 dark:hover:bg-slate-700 transition-all text-center"
            >
              Batal
            </router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "../../configs/api.js";
import { useNotificationStore } from "../../stores/notification";

const router = useRouter();
const route = useRoute();
const notification = useNotificationStore();
const loading = ref(false);
const isEditMode = ref(false);
const classrooms = ref([]);
const isClassroomDropdownOpen = ref(false);

const form = ref({
  name: "",
  email: "",
  password: "",
  nip: "",
  classroom_id: null,
  role: "siswa",
});

onMounted(async () => {
  await fetchClassrooms();
  if (route.params.id) {
    isEditMode.value = true;
    await fetchStudentData(route.params.id);
  }
  document.addEventListener("click", closeDropdowns);
});

onUnmounted(() => {
  document.removeEventListener("click", closeDropdowns);
});

const closeDropdowns = () => {
  isClassroomDropdownOpen.value = false;
};

const getClassroomName = (id) => {
  if (!id) return "PILIH KELAS";
  const classroom = classrooms.value.find((c) => c.id === id);
  return classroom
    ? `${classroom.name} - ${classroom.academic_year}`.toUpperCase()
    : "PILIH KELAS";
};

const fetchClassrooms = async () => {
  try {
    const response = await api.get("/admin/classrooms");
    classrooms.value = response.data.data || response.data;
  } catch (error) {
    console.error("Failed to fetch classrooms:", error);
  }
};

const fetchStudentData = async (id) => {
  loading.value = true;
  try {
    const response = await api.get(`/admin/users/${id}`);
    const student = response.data.data || response.data;
    form.value = {
      name: student.name,
      email: student.email,
      nip: student.nip || "",
      classroom_id: student.classroom_id || null,
      role: "siswa",
    };
  } catch (error) {
    console.error("Failed to fetch student data:", error);
    notification.add({
      title: "Gagal",
      message: "Gagal mengambil data siswa",
      type: "error",
    });
    router.push("/admin/students");
  } finally {
    loading.value = false;
  }
};

const handleSubmit = async () => {
  if (loading.value) return;
  loading.value = true;
  try {
    const payload = { ...form.value };
    if (isEditMode.value && !payload.password) {
      delete payload.password;
    }

    if (isEditMode.value) {
      await api.patch(`/admin/users/${route.params.id}`, payload);
      notification.add({
        title: "Berhasil",
        message: "Data siswa berhasil diperbarui",
        type: "success",
      });
    } else {
      await api.post("/admin/users", payload);
      notification.add({
        title: "Berhasil",
        message: "Siswa berhasil didaftarkan",
        type: "success",
      });
    }
    // Redirect back to list
    setTimeout(() => {
      router.push("/admin/students");
    }, 500);
  } catch (error) {
    console.error("Failed to save student:", error);
    notification.add({
      title: "Gagal",
      message: error.response?.data?.message || "Gagal menyimpan data siswa",
      type: "error",
    });
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 10px;
}
</style>
