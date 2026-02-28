<template>
  <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-300">

    <div
      class="flex flex-col lg:flex-row lg:items-center justify-between gap-6"
    >
      <div>
        <div class="flex items-center gap-3 mb-2">
          <div
            class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center"
          >
            <span class="material-icons">groups</span>
          </div>
          <h1
            class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tighter uppercase"
          >
            Data Siswa
          </h1>
        </div>
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
          Kelola informasi siswa dan akun akses pembelajaran mereka.
        </p>
      </div>

      <div class="flex items-center gap-3">
        <div
          class="hidden sm:flex items-center bg-white dark:bg-slate-800 rounded-2xl px-4 py-2.5 border border-slate-200 dark:border-slate-700 shadow-sm group focus-within:ring-2 focus-within:ring-primary/20 transition-all"
        >
          <span
            class="material-icons text-slate-400 group-focus-within:text-primary transition-colors mr-2 text-xl"
            >search</span
          >
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari siswa..."
            class="bg-transparent border-none p-0 text-sm focus:ring-0 w-48 lg:w-64 font-bold text-slate-700 dark:text-slate-200 placeholder:text-slate-300"
          />
        </div>
        <router-link
          to="/admin/students/add"
          class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-primary-hover shadow-xl shadow-primary/20 transition-all active:scale-95 whitespace-nowrap"
        >
          <span class="material-icons text-lg">add_circle</span>
          Tambah Siswa
        </router-link>
      </div>
    </div>


    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div
        v-for="stat in stats"
        :key="stat.label"
        class="bg-white dark:bg-surface-dark p-6 rounded-[24px] border border-slate-100 dark:border-white/5 shadow-sm overflow-hidden relative group"
      >
        <div
          :class="`absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-5 group-hover:scale-110 transition-transform duration-500 ${stat.color}`"
        ></div>
        <div class="relative z-10">
          <p
            class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1"
          >
            {{ stat.label }}
          </p>
          <div class="flex items-end gap-2">
            <span
              class="text-3xl font-black text-slate-800 dark:text-white tracking-tighter"
              >{{ stat.value }}</span
            >
            <span class="text-xs font-bold text-slate-400 mb-1.5">Siswa</span>
          </div>
        </div>
      </div>
    </div>


    <div
      v-if="loading && students.length === 0"
      class="flex flex-col items-center justify-center py-32 px-8 text-center bg-white dark:bg-surface-dark rounded-[32px] border border-slate-100 dark:border-white/5 shadow-sm"
    >
      <div class="flex flex-col items-center justify-center gap-4">
        <div
          class="w-12 h-12 border-4 border-primary/20 border-t-primary rounded-full animate-spin"
        ></div>
        <p
          class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] animate-pulse"
        >
          Memuat data siswa...
        </p>
      </div>
    </div>


    <div
      v-else-if="!selectedClassId"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-in fade-in slide-in-from-bottom-8 duration-500"
    >

      <button
        @click="selectedClassId = 'all'"
        class="bg-white dark:bg-surface-dark p-8 rounded-[32px] border border-slate-100 dark:border-white/5 shadow-sm hover:shadow-xl hover:shadow-primary/10 hover:border-primary/20 transition-all duration-300 text-left group relative overflow-hidden"
      >
        <div
          class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-primary opacity-[0.03] group-hover:scale-150 transition-transform duration-700"
        ></div>
        <div class="relative z-10 flex flex-col h-full">
          <div
            class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-white transition-all duration-300"
          >
            <span class="material-icons">people</span>
          </div>
          <h3
            class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight mb-1"
          >
            Semua Siswa
          </h3>
          <p
            class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6"
          >
            Total Entitas
          </p>
          <div class="mt-auto flex items-center justify-between">
            <span class="text-3xl font-black text-primary">{{
              students.length
            }}</span>
            <span
              class="material-icons text-slate-200 group-hover:text-primary transition-colors"
              >arrow_forward</span
            >
          </div>
        </div>
      </button>


      <button
        v-for="cls in classroomsWithStudentCount"
        :key="cls.id"
        @click="selectedClassId = cls.id"
        class="bg-white dark:bg-surface-dark p-8 rounded-[32px] border border-slate-100 dark:border-white/5 shadow-sm hover:shadow-xl hover:shadow-primary/10 hover:border-primary/20 transition-all duration-300 text-left group relative overflow-hidden"
      >
        <div
          class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-primary opacity-[0.03] group-hover:scale-150 transition-transform duration-700"
        ></div>
        <div class="relative z-10 flex flex-col h-full">
          <div class="flex items-start justify-between mb-6">
            <div
              class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-slate-400 flex items-center justify-center group-hover:bg-primary/10 group-hover:text-primary group-hover:border-primary/20 transition-all duration-300"
            >
              <span class="material-icons">meeting_room</span>
            </div>
            <div
              class="px-3 py-1 bg-slate-50 dark:bg-slate-800 rounded-full border border-slate-100 dark:border-slate-700"
            >
              <span
                class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                >{{ cls.grade_level || "Kelas" }}</span
              >
            </div>
          </div>
          <h3
            class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight mb-1 truncate"
          >
            {{ cls.name }}
          </h3>
          <p
            class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6"
          >
            Tahun: {{ cls.academic_year || "---" }}
          </p>
          <div class="mt-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span
                class="text-3xl font-black text-slate-800 dark:text-white group-hover:text-primary transition-colors"
                >{{ cls.studentCount }}</span
              >
              <span class="text-xs font-bold text-slate-400 mt-2 italic"
                >Siswa</span
              >
            </div>
            <span
              class="material-icons text-slate-200 group-hover:text-primary transition-colors"
              >arrow_forward</span
            >
          </div>
        </div>
      </button>


      <button
        @click="selectedClassId = 'none'"
        class="bg-white dark:bg-surface-dark p-8 rounded-[32px] border border-dashed border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-xl hover:shadow-orange-500/10 hover:border-orange-500/50 transition-all duration-300 text-left group relative overflow-hidden"
      >
        <div class="relative z-10 flex flex-col h-full">
          <div
            class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-500/10 text-orange-400 flex items-center justify-center mb-6 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300"
          >
            <span class="material-icons">no_accounts</span>
          </div>
          <h3
            class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight mb-1"
          >
            Unassigned
          </h3>
          <p
            class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6"
          >
            Belum Ada Kelas
          </p>
          <div class="mt-auto flex items-center justify-between">
            <span
              class="text-3xl font-black text-orange-500 transition-colors"
              >{{ students.filter((s) => !s.classroom_id).length }}</span
            >
            <span
              class="material-icons text-slate-200 group-hover:text-orange-500 transition-colors"
              >arrow_forward</span
            >
          </div>
        </div>
      </button>
    </div>


    <div v-else class="space-y-6 animate-in fade-in zoom-in-95 duration-500">

      <div
        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-surface-dark p-6 rounded-[32px] border border-slate-100 dark:border-white/5"
      >
        <div class="flex items-center gap-4">
          <button
            @click="selectedClassId = null"
            class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-slate-400 hover:text-primary transition-all flex items-center justify-center"
          >
            <span class="material-icons">arrow_back</span>
          </button>
          <div>
            <h3
              class="font-black text-slate-800 dark:text-white uppercase tracking-tight"
            >
              {{
                selectedClassId === "all"
                  ? "Semua Siswa"
                  : selectedClassId === "none"
                    ? "Siswa Tanpa Kelas"
                    : classrooms.find((c) => c.id === selectedClassId)?.name
              }}
            </h3>
            <p
              class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
            >
              {{ filteredStudents.length }} Siswa Ditemukan
            </p>
          </div>
        </div>


        <div
          v-if="selectedIds.length > 0"
          class="flex items-center gap-3 animate-in fade-in slide-in-from-right-4 duration-300"
        >
          <span
            class="text-xs font-bold text-slate-500 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700"
          >
            {{ selectedIds.length }} Terpilih
          </span>
          <button
            @click="bulkDeleteSelected"
            class="flex items-center gap-2 px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-red-500/20 active:scale-95"
          >
            <span class="material-icons text-sm">delete_sweep</span>
            Hapus Massal
          </button>
          <button
            @click="selectedIds = []"
            class="text-xs font-bold text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors px-2"
          >
            Batal
          </button>
        </div>
      </div>


      <div
        class="bg-white dark:bg-surface-dark rounded-[32px] border border-slate-100 dark:border-white/5 shadow-sm overflow-hidden min-h-[400px]"
      >

        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="border-b border-slate-50 dark:border-white/5">
                <th class="pl-8 pr-4 py-6 w-10">
                  <div
                    @click="toggleSelectAll"
                    class="w-5 h-5 rounded border-2 flex items-center justify-center cursor-pointer transition-all"
                    :class="
                      selectedIds.length === filteredStudents.length &&
                      filteredStudents.length > 0
                        ? 'bg-primary border-primary'
                        : 'border-slate-200 dark:border-slate-700'
                    "
                  >
                    <span
                      v-if="
                        selectedIds.length === filteredStudents.length &&
                        filteredStudents.length > 0
                      "
                      class="material-icons text-white text-[14px]"
                      >done</span
                    >
                  </div>
                </th>
                <th
                  class="px-4 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]"
                >
                  Profil Siswa
                </th>
                <th
                  class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]"
                >
                  Email & Kontak
                </th>
                <th
                  v-if="selectedClassId === 'all'"
                  class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]"
                >
                  Kelas
                </th>
                <th
                  class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right"
                >
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
              <tr
                v-for="student in filteredStudents"
                :key="student.id"
                class="group transition-all"
                :class="
                  selectedIds.includes(student.id)
                    ? 'bg-primary/[0.03]'
                    : 'hover:bg-slate-50/50 dark:hover:bg-slate-800/20'
                "
              >
                <td class="pl-8 pr-4 py-5 w-10">
                  <div
                    @click="toggleSelection(student.id)"
                    class="w-5 h-5 rounded border-2 flex items-center justify-center cursor-pointer transition-all"
                    :class="
                      selectedIds.includes(student.id)
                        ? 'bg-primary border-primary'
                        : 'border-slate-200 dark:border-slate-700 group-hover:border-primary/40'
                    "
                  >
                    <span
                      v-if="selectedIds.includes(student.id)"
                      class="material-icons text-white text-[14px]"
                      >done</span
                    >
                  </div>
                </td>
                <td class="px-4 py-5">
                  <div class="flex items-center gap-4">
                    <div
                      class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 group-hover:bg-primary/10 group-hover:text-primary group-hover:border-primary/20 transition-all overflow-hidden shadow-inner"
                    >
                      <span
                        v-if="!student.avatar"
                        class="material-icons text-2xl"
                        >person</span
                      >
                      <img
                        v-else
                        :src="student.avatar"
                        alt=""
                        class="w-full h-full object-cover"
                      />
                    </div>
                    <div>
                      <p
                        class="text-sm font-black text-slate-800 dark:text-white group-hover:text-primary transition-colors leading-none mb-1 uppercase tracking-tight"
                      >
                        {{ student.name }}
                      </p>
                      <div class="flex items-center gap-2">
                        <p
                          class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                        >
                          NIS: {{ student.nip || "---" }}
                        </p>
                        <span
                          v-if="!student.classroom_id"
                          class="text-[8px] font-black bg-orange-500/10 text-orange-500 px-1.5 py-0.5 rounded uppercase tracking-tighter"
                          >Belum Ada Kelas</span
                        >
                        <span
                          v-else
                          class="text-[8px] font-black bg-green-500/10 text-green-500 px-1.5 py-0.5 rounded uppercase tracking-tighter"
                          >Terdaftar</span
                        >
                      </div>
                    </div>
                  </div>
                </td>
                <td
                  class="px-8 py-5 text-sm font-bold text-slate-600 dark:text-slate-400 font-mono italic"
                >
                  {{ student.email }}
                </td>
                <td v-if="selectedClassId === 'all'" class="px-8 py-5">
                  <div class="flex items-center gap-2">
                    <span
                      class="w-2 h-2 rounded-full bg-primary animate-pulse"
                    ></span>
                    <p
                      class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider"
                    >
                      {{ student.classroom?.name || "Belum Ada Kelas" }}
                    </p>
                  </div>
                </td>
                <td class="px-8 py-5 text-right">
                  <div
                    class="flex items-center justify-end gap-2 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300"
                  >
                    <router-link
                      :to="`/admin/students/edit/${student.id}`"
                      class="w-10 h-10 rounded-xl bg-white dark:bg-slate-700 text-slate-400 hover:text-primary hover:shadow-lg border border-slate-200 dark:border-slate-600 flex items-center justify-center transition-all"
                      title="Edit Data"
                    >
                      <span class="material-icons text-lg">edit</span>
                    </router-link>
                    <button
                      @click="confirmDelete(student)"
                      class="w-10 h-10 rounded-xl bg-white dark:bg-slate-700 text-slate-400 hover:text-red-500 hover:shadow-lg border border-slate-200 dark:border-slate-600 flex items-center justify-center transition-all"
                      title="Hapus Siswa"
                    >
                      <span class="material-icons text-lg">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>


        <div class="md:hidden space-y-px p-4">
          <div
            v-for="student in filteredStudents"
            :key="student.id"
            class="p-5 rounded-3xl border transition-all mb-4 relative"
            :class="
              selectedIds.includes(student.id)
                ? 'bg-primary/5 border-primary/20'
                : 'bg-slate-50/50 dark:bg-slate-800/10 border-transparent'
            "
          >

            <div
              @click="toggleSelection(student.id)"
              class="absolute top-4 right-4 w-7 h-7 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all z-10"
              :class="
                selectedIds.includes(student.id)
                  ? 'bg-primary border-primary shadow-lg shadow-primary/20 text-white'
                  : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700'
              "
            >
              <span
                v-if="selectedIds.includes(student.id)"
                class="material-icons text-[14px]"
                >done</span
              >
              <span v-else class="material-icons text-[14px] opacity-0"
                >done</span
              >
            </div>

            <div class="flex items-start justify-between mb-4 pr-10">
              <div class="flex items-center gap-4">
                <div
                  class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-300 shadow-sm overflow-hidden"
                >
                  <span v-if="!student.avatar" class="material-icons text-2xl"
                    >person</span
                  >
                  <img
                    v-else
                    :src="student.avatar"
                    alt=""
                    class="w-full h-full object-cover"
                  />
                </div>
                <div>
                  <h3
                    class="font-black text-slate-800 dark:text-white uppercase tracking-tight"
                  >
                    {{ student.name }}
                  </h3>
                  <p
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5"
                  >
                    NIS: {{ student.nip || "---" }}
                  </p>
                </div>
              </div>
              <div class="flex gap-2">
                <router-link
                  :to="`/admin/students/edit/${student.id}`"
                  class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 text-slate-400 shadow-sm flex items-center justify-center"
                >
                  <span class="material-icons text-sm">edit</span>
                </router-link>
                <button
                  @click="confirmDelete(student)"
                  class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 text-red-400 shadow-sm flex items-center justify-center"
                >
                  <span class="material-icons text-sm">delete</span>
                </button>
              </div>
            </div>
            <div
              class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100 dark:border-white/5"
            >
              <div class="space-y-1">
                <p
                  class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none"
                >
                  Email
                </p>
                <p
                  class="text-[11px] font-bold text-slate-600 dark:text-slate-400 truncate"
                >
                  {{ student.email }}
                </p>
              </div>
              <div class="space-y-1">
                <p
                  class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none"
                >
                  Kelas
                </p>
                <p
                  class="text-[11px] font-bold text-slate-600 dark:text-slate-400"
                >
                  {{ student.classroom?.name || "---" }}
                </p>
              </div>
            </div>
          </div>
        </div>


        <div
          v-if="filteredStudents.length === 0"
          class="flex flex-col items-center justify-center py-20 px-8 text-center animate-in fade-in zoom-in duration-500"
        >
          <div
            class="w-24 h-24 rounded-full bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center mb-6 border-2 border-dashed border-slate-200 dark:border-slate-700"
          >
            <span
              class="material-icons text-4xl text-slate-200 dark:text-slate-700"
              >person_off</span
            >
          </div>
          <h3
            class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight mb-2"
          >
            Tidak Ada Data Siswa
          </h3>
          <p class="text-sm text-slate-500 max-w-xs mx-auto">
            Kelas ini belum memiliki siswa yang terdaftar.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from "../../configs/api.js";
import { useNotificationStore } from "../../stores/notification";

const notification = useNotificationStore();
const students = ref([]);
const classrooms = ref([]);
const loading = ref(false);
const searchQuery = ref("");
const selectedIds = ref([]);

const toggleSelectAll = () => {
  if (selectedIds.value.length === filteredStudents.value.length) {
    selectedIds.value = [];
  } else {
    selectedIds.value = filteredStudents.value.map((s) => s.id);
  }
};

const toggleSelection = (studentId) => {
  const index = selectedIds.value.indexOf(studentId);
  if (index > -1) {
    selectedIds.value.splice(index, 1);
  } else {
    selectedIds.value.push(studentId);
  }
};
const selectedClassId = ref(null); // State for classroom filtering

const stats = computed(() => {
  const total = students.value.length;
  return [
    { label: "Total Siswa", value: total, color: "bg-primary" },
    {
      label: "Kelas Aktif",
      value: classrooms.value.length,
      color: "bg-green-500",
    },
    {
      label: "Pendaftar Baru",
      value: students.value.filter((s) => isNew(s.created_at)).length,
      color: "bg-orange-500",
    },
  ];
});

const classroomsWithStudentCount = computed(() => {
  return classrooms.value.map((cls) => ({
    ...cls,
    studentCount: students.value.filter((s) => s.classroom_id === cls.id)
      .length,
  }));
});

const studentsInSelectedClass = computed(() => {
  if (selectedClassId.value === "all") return students.value;
  if (selectedClassId.value === "none")
    return students.value.filter((s) => !s.classroom_id);
  if (!selectedClassId.value) return [];
  return students.value.filter((s) => s.classroom_id === selectedClassId.value);
});

const filteredStudents = computed(() => {
  const baseList = selectedClassId.value
    ? studentsInSelectedClass.value
    : students.value;
  if (!searchQuery.value) return baseList;
  const q = searchQuery.value.toLowerCase();
  return baseList.filter(
    (s) =>
      s.name.toLowerCase().includes(q) ||
      s.email.toLowerCase().includes(q) ||
      (s.nip && s.nip.includes(q)) ||
      (s.classroom?.name && s.classroom.name.toLowerCase().includes(q)),
  );
});

const isNew = (date) => {
  const joinDate = new Date(date);
  const today = new Date();
  const diffTime = Math.abs(today - joinDate);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays <= 7;
};

const fetchStudents = async () => {
  try {
    const response = await api.get("/admin/users", {
      params: { role: "siswa" },
    });
    const allUsers = response.data.data || response.data;
    students.value = allUsers.filter((u) => u.role === "siswa");
  } catch (err) {
    notification.add({
      title: "Gagal",
      message: "Gagal mengambil data siswa",
      type: "error",
    });
  }
};

const fetchClassrooms = async () => {
  try {
    const response = await api.get("/admin/classrooms");
    classrooms.value = response.data.data || response.data;
  } catch (err) {
    notification.add({
      title: "Gagal",
      message: "Gagal mengambil data kelas",
      type: "error",
    });
  }
};

const saveStudent = async () => {
  // Logic moved to AddStudent.vue
};

const confirmDelete = async (student) => {
  if (
    confirm(
      `Hapus data siswa "${student.name}"? Segala log pengerjaan quiz juga akan terpengaruh.`,
    )
  ) {
    try {
      await api.delete(`/admin/users/${student.id}`);
      notification.add({
        title: "Berhasil",
        message: "Data siswa berhasil dihapus",
        type: "success",
      });
      fetchStudents();
    } catch (err) {
      notification.add({
        title: "Gagal",
        message: "Gagal menghapus data",
        type: "error",
      });
    }
  }
};

const bulkDeleteSelected = async () => {
  if (selectedIds.value.length === 0) return;

  if (
    confirm(
      `Hapus ${selectedIds.value.length} siswa terpilih? Tindakan ini tidak dapat dibatalkan.`,
    )
  ) {
    loading.value = true;
    try {
      await api.post("/admin/users/bulk-delete", { ids: selectedIds.value });
      notification.add({
        title: "Berhasil",
        message: `${selectedIds.value.length} siswa berhasil dihapus`,
        type: "success",
      });
      selectedIds.value = [];
      fetchStudents();
    } catch (err) {
      notification.add({
        title: "Gagal",
        message: "Gagal menghapus data massal",
        type: "error",
      });
    } finally {
      loading.value = false;
    }
  }
};

onMounted(async () => {
  loading.value = true;
  try {
    await Promise.all([fetchStudents(), fetchClassrooms()]);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.05);
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.05);
}
</style>
