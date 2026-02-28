<template>
  <div class="space-y-6">
    <!-- Header & Navigation -->
    <div
      class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"
    >
      <div class="flex items-center gap-4">
        <button
          @click="$router.push('/admin/classrooms')"
          class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:text-primary transition-all active:scale-95 shadow-sm"
        >
          <span class="material-icons">arrow_back</span>
        </button>
        <div>
          <h1
            class="text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase"
          >
            Detail Kelas
          </h1>
          <div v-if="classroom" class="flex items-center gap-2 mt-1">
            <span
              class="px-3 py-1 bg-primary/10 text-primary text-[10px] font-black rounded-lg uppercase tracking-widest"
              >Kelas {{ classroom.grade_level }}</span
            >
            <span class="text-slate-400 font-bold text-sm"
              >{{ classroom.name }} • {{ classroom.academic_year }}</span
            >
          </div>
        </div>
      </div>

      <div
        v-if="classroom"
        class="flex items-center gap-4 px-6 py-3 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm"
      >
        <div class="text-right">
          <p
            class="text-[9px] font-black text-slate-400 uppercase tracking-widest"
          >
            Wali Kelas
          </p>
          <p class="text-sm font-bold text-slate-700 dark:text-slate-200">
            {{ classroom.homeroom_teacher_name || "Belum Ditentukan" }}
          </p>
        </div>
        <div
          class="w-10 h-10 rounded-2xl bg-secondary/10 text-secondary flex items-center justify-center font-black"
        >
          {{ classroom.homeroom_teacher_name?.charAt(0) || "?" }}
        </div>
      </div>
    </div>

    <div
      v-if="loading"
      class="py-20 flex flex-col items-center justify-center space-y-4"
    >
      <div
        class="w-12 h-12 border-4 border-primary/20 border-t-primary rounded-full animate-spin"
      ></div>
      <p class="text-xs font-black text-slate-400 uppercase tracking-widest">
        Memuat Data Kelas...
      </p>
    </div>

    <div
      v-else-if="classroom"
      class="grid grid-cols-1 gap-8 animate-in fade-in slide-in-from-bottom-4 duration-700"
    >
      <!-- Section 1: Data Siswa (TOP) -->
      <div
        class="bg-white dark:bg-surface-dark rounded-[40px] border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm"
      >
        <div
          class="p-8 border-b border-slate-50 dark:border-slate-800 flex items-center justify-between"
        >
          <div>
            <h3
              class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight"
            >
              Daftar Siswa Terdaftar
            </h3>
            <p
              class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1"
            >
              Total {{ classroom.students?.length || 0 }} Siswa aktif di kelas
              ini
            </p>
          </div>
          <div class="flex items-center gap-4">
            <div class="relative group hidden sm:block">
              <span
                class="material-icons absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-primary transition-colors"
                >search</span
              >
              <input
                v-model="studentSearch"
                type="text"
                placeholder="Cari Siswa..."
                class="pl-12 pr-6 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-[11px] font-black focus:ring-2 focus:ring-primary/20 transition-all outline-none uppercase tracking-widest min-w-[240px]"
              />
            </div>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                <th
                  class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800 w-16 text-center"
                >
                  No
                </th>
                <th
                  class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800"
                >
                  Nama Siswa
                </th>
                <th
                  class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800"
                >
                  NIS / Identitas
                </th>
                <th
                  class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800"
                >
                  Email
                </th>
                <th
                  class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800 text-right"
                >
                  Status
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
              <tr
                v-for="(s, idx) in filteredStudents"
                :key="s.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-all group"
              >
                <td
                  class="px-8 py-5 text-sm font-black text-slate-400 text-center"
                >
                  {{ idx + 1 }}
                </td>
                <td class="px-8 py-5">
                  <div class="flex items-center gap-4">
                    <div
                      class="w-10 h-10 rounded-xl bg-primary/5 text-primary flex items-center justify-center font-black text-xs group-hover:bg-primary group-hover:text-white transition-all"
                    >
                      {{ s.name.charAt(0) }}
                    </div>
                    <span
                      class="font-bold text-slate-700 dark:text-slate-200 capitalize whitespace-nowrap"
                      >{{ s.name }}</span
                    >
                  </div>
                </td>
                <td class="px-8 py-5">
                  <span
                    class="text-xs font-black text-slate-400 uppercase tracking-widest"
                    >{{ s.nip || s.nis || "---" }}</span
                  >
                </td>
                <td class="px-8 py-5 text-sm font-medium text-slate-500">
                  {{ s.email || "Belum Ada Email" }}
                </td>
                <td class="px-8 py-5 text-right">
                  <span
                    class="px-3 py-1 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 text-[10px] font-black uppercase tracking-widest"
                    >Aktif</span
                  >
                </td>
              </tr>
              <tr v-if="filteredStudents.length === 0">
                <td colspan="5" class="py-20 text-center">
                  <div
                    class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4 text-slate-200"
                  >
                    <span class="material-icons text-3xl"
                      >sentiment_very_dissatisfied</span
                    >
                  </div>
                  <h4
                    class="font-black text-slate-400 uppercase tracking-widest text-[10px]"
                  >
                    Basis data siswa kosong atau tidak ditemukan
                  </h4>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Section 2: Guru Pengajar (BOTTOM) -->
      <div
        class="bg-white dark:bg-surface-dark rounded-[40px] border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm"
      >
        <div
          class="p-8 border-b border-slate-50 dark:border-slate-800 flex items-center justify-between"
        >
          <div>
            <h3
              class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight"
            >
              Guru Pengampu Mata Pelajaran
            </h3>
            <p
              class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1"
            >
              Daftar guru yang terjadwal mengajar di kelas ini
            </p>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                <th
                  class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800 w-16 text-center"
                >
                  No
                </th>
                <th
                  class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800"
                >
                  Nama Lengkap Guru
                </th>
                <th
                  class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800"
                >
                  NIP / Email
                </th>
                <th
                  class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800 text-center"
                >
                  Status Mengajar
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
              <tr
                v-for="(t, idx) in classroom.teachers"
                :key="t.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-all group"
              >
                <td
                  class="px-8 py-5 text-sm font-black text-slate-400 text-center"
                >
                  {{ idx + 1 }}
                </td>
                <td class="px-8 py-5">
                  <div class="flex items-center gap-4">
                    <div
                      class="w-10 h-10 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center font-black text-xs group-hover:bg-secondary group-hover:text-white transition-all"
                    >
                      {{ t.name.charAt(0) }}
                    </div>
                    <span
                      class="font-bold text-slate-700 dark:text-slate-200 capitalize whitespace-nowrap"
                      >{{ t.name }}</span
                    >
                  </div>
                </td>
                <td class="px-8 py-5">
                  <p
                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                  >
                    {{ t.nip || "---" }}
                  </p>
                  <p class="text-[10px] font-medium text-slate-400 lowercase">
                    {{ t.email }}
                  </p>
                </td>
                <td class="px-8 py-5 text-center">
                  <span
                    v-if="t.id === classroom.homeroom_teacher_id"
                    class="px-3 py-1 rounded-lg bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest whitespace-nowrap"
                  >
                    Walikelas
                  </span>
                </td>
              </tr>
              <tr v-if="!classroom.teachers || classroom.teachers.length === 0">
                <td colspan="4" class="py-20 text-center">
                  <div
                    class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4 text-slate-200"
                  >
                    <span class="material-icons text-3xl">person_off</span>
                  </div>
                  <h4
                    class="font-black text-slate-400 uppercase tracking-widest text-[10px]"
                  >
                    Belum ada guru yang dijadwalkan di kelas ini
                  </h4>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import api from "../../configs/api.js";

const route = useRoute();
const classroom = ref(null);
const loading = ref(true);
const studentSearch = ref("");

onMounted(async () => {
  await fetchClassroomDetail();
});

const fetchClassroomDetail = async () => {
  loading.value = true;
  try {
    const response = await api.get(`/admin/classrooms/${route.params.id}`);
    classroom.value = response.data;
  } catch (error) {
    console.error("Failed to fetch classroom detail:", error);
  } finally {
    loading.value = false;
  }
};

const filteredStudents = computed(() => {
  if (!classroom.value || !classroom.value.students) return [];
  if (!studentSearch.value) return classroom.value.students;

  const query = studentSearch.value.toLowerCase();
  return classroom.value.students.filter(
    (s) =>
      s.name.toLowerCase().includes(query) ||
      (s.nip && s.nip.toLowerCase().includes(query)) ||
      (s.nis && s.nis.toLowerCase().includes(query)) ||
      (s.email && s.email.toLowerCase().includes(query)),
  );
});
</script>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
  width: 4px;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 10px;
}
</style>
