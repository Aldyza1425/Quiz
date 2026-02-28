<template>
  <div class="space-y-6">
    <!-- Header -->
    <div
      class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"
    >
      <div>
        <h1
          class="text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase"
        >
          Penilaian Siswa
        </h1>
        <p class="text-sm text-slate-500 font-medium">
          Input dan kelola nilai akademik siswa per kelas.
        </p>
      </div>
      <div class="flex items-center gap-2">
        <button
          @click="exportToExcel"
          :disabled="!selectedClass || !selectedSubject"
          class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-600 dark:text-white hover:bg-slate-50 transition-all disabled:opacity-50"
        >
          <span class="material-icons text-lg">download</span>
          Ekspor
        </button>
        <button
          @click="saveScores"
          :disabled="loading || !selectedClass || !selectedSubject"
          class="flex items-center gap-2 px-6 py-2 bg-primary border border-transparent rounded-xl text-sm font-black text-white hover:bg-primary-hover shadow-lg shadow-primary/30 transition-all disabled:opacity-50 disabled:shadow-none"
        >
          <span class="material-icons text-lg">{{
            loading ? "sync" : "save"
          }}</span>
          {{ loading ? "Menyimpan..." : "Simpan Nilai" }}
        </button>
      </div>
    </div>

    <!-- Filters & Loading -->
    <div
      class="bg-white dark:bg-surface-dark p-6 sm:p-8 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm flex flex-wrap gap-6 relative z-50"
    >
      <div class="flex-1 min-w-[240px] relative">
        <label
          class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1"
          >Pilih Kelas</label
        >

        <button
          @click.stop="isClassDropdownOpen = !isClassDropdownOpen"
          class="w-full flex items-center justify-between pl-5 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent hover:border-primary/30 focus:border-primary rounded-xl text-sm font-bold transition-all group"
          :class="{
            'border-primary shadow-lg shadow-primary/5': isClassDropdownOpen,
          }"
        >
          <span
            :class="
              selectedClass
                ? 'text-slate-900 dark:text-white'
                : 'text-slate-400'
            "
          >
            {{ selectedClassName || "Pilih Kelas..." }}
          </span>
          <span
            class="material-icons text-primary transition-transform duration-300"
            :class="{ 'rotate-180': isClassDropdownOpen }"
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
            v-if="isClassDropdownOpen"
            class="absolute top-full left-0 w-full mt-2 bg-white dark:bg-surface-dark rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-800 py-2 z-50 overflow-hidden"
          >
            <button
              v-for="c in classes"
              :key="c.id"
              @click="selectClass(c)"
              class="w-full px-5 py-3 text-left text-xs font-bold transition-all border-l-4 border-transparent"
              :class="
                selectedClass === c.id
                  ? 'bg-primary/5 text-primary border-primary'
                  : 'hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400'
              "
            >
              {{ c.name }}
            </button>
          </div>
        </Transition>
      </div>

      <div class="flex-1 min-w-[200px] relative">
        <label
          class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1"
          >Mata Pelajaran</label
        >
        <button
          @click.stop="isSubjectDropdownOpen = !isSubjectDropdownOpen"
          class="w-full flex items-center justify-between px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent hover:border-primary/30 focus:border-primary rounded-xl text-sm font-bold transition-all group"
          :class="{
            'border-primary shadow-lg shadow-primary/5': isSubjectDropdownOpen,
          }"
        >
          <span
            :class="
              selectedSubject
                ? 'text-slate-900 dark:text-white'
                : 'text-slate-400'
            "
          >
            {{ selectedSubjectName || "Pilih Mapel..." }}
          </span>
          <span
            class="material-icons text-primary transition-transform duration-300"
            :class="{ 'rotate-180': isSubjectDropdownOpen }"
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
            v-if="isSubjectDropdownOpen"
            class="absolute top-full left-0 w-full mt-2 bg-white dark:bg-surface-dark rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-800 py-2 z-50 overflow-hidden"
          >
            <button
              v-for="s in subjects"
              :key="s.id"
              @click="selectSubject(s)"
              class="w-full px-5 py-3 text-left text-xs font-bold transition-all border-l-4 border-transparent"
              :class="
                selectedSubject === s.id
                  ? 'bg-primary/5 text-primary border-primary'
                  : 'hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400'
              "
            >
              {{ s.name }}
            </button>
          </div>
        </Transition>
      </div>

      <div class="flex-1 min-w-[200px]">
        <label
          class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1"
          >Semester</label
        >
        <div
          class="px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 rounded-xl text-sm font-bold text-slate-900 dark:text-white border-2 border-transparent"
        >
          Ganjil 2025/2026
        </div>
      </div>
    </div>

    <!-- Warning/Info State -->
    <div
      v-if="!selectedSubject && !loading"
      class="bg-orange-50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-800 rounded-[28px] p-6 flex items-start gap-4 animate-in slide-in-from-top-4 duration-500"
    >
      <div
        class="p-2 bg-orange-100 dark:bg-orange-900/20 rounded-xl text-orange-600"
      >
        <span class="material-icons">info</span>
      </div>
      <div>
        <h3
          class="text-sm font-black text-orange-900 dark:text-orange-200 uppercase tracking-tight"
        >
          Perhatian: Pilih Mata Pelajaran
        </h3>
        <p
          class="text-xs text-orange-700 dark:text-orange-300 font-medium mt-1"
        >
          Anda dapat melihat daftar siswa, namun untuk menginput dan menyimpan
          nilai, silakan pilih
          <span class="font-black underline">Mata Pelajaran</span> terlebih
          dahulu.
        </p>
      </div>
    </div>

    <!-- Loading State -->
    <div
      v-if="loading"
      class="bg-white dark:bg-surface-dark rounded-[32px] border border-slate-100 dark:border-slate-800 p-20 text-center"
    >
      <div class="flex flex-col items-center justify-center gap-4">
        <div
          class="w-10 h-10 border-4 border-primary/20 border-t-primary rounded-full animate-spin"
        ></div>
        <p
          class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] animate-pulse"
        >
          Memuat data nilai...
        </p>
      </div>
    </div>

    <!-- Grade Table -->
    <div
      v-else
      class="bg-white dark:bg-surface-dark rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden animate-in fade-in duration-500"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-100/50 dark:bg-slate-800/50">
              <th
                rowspan="2"
                class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-r border-slate-200 dark:border-slate-800 w-16 text-center"
              >
                No
              </th>
              <th
                rowspan="2"
                class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-r border-slate-200 dark:border-slate-800"
              >
                Nama Siswa
              </th>
              <th
                colspan="3"
                class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-r border-slate-200 dark:border-slate-800 text-center bg-blue-50/30 dark:bg-blue-900/10"
              >
                Formatif
              </th>
              <th
                colspan="2"
                class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-r border-slate-200 dark:border-slate-800 text-center bg-orange-50/30 dark:bg-orange-900/10"
              >
                Sumatif
              </th>
              <th
                rowspan="2"
                class="px-6 py-4 text-[10px] font-black text-primary uppercase tracking-widest border-b border-slate-200 dark:border-slate-800 text-center bg-slate-50/50 dark:bg-slate-800/50"
              >
                Rerata
              </th>
            </tr>
            <tr class="bg-slate-50/50 dark:bg-slate-800/50">
              <th
                class="px-4 py-3 text-[9px] font-black text-slate-500 uppercase tracking-tighter border-b border-r border-slate-200 dark:border-slate-800 text-center"
              >
                Tugas
              </th>
              <th
                class="px-4 py-3 text-[9px] font-black text-slate-500 uppercase tracking-tighter border-b border-r border-slate-200 dark:border-slate-800 text-center"
              >
                Harian
              </th>
              <th
                class="px-4 py-3 text-[9px] font-black text-slate-500 uppercase tracking-tighter border-b border-r border-slate-200 dark:border-slate-800 text-center whitespace-nowrap"
              >
                Proyek
              </th>
              <th
                class="px-4 py-3 text-[9px] font-black text-slate-500 uppercase tracking-tighter border-b border-r border-slate-200 dark:border-slate-800 text-center"
              >
                PTS
              </th>
              <th
                class="px-4 py-3 text-[9px] font-black text-slate-500 uppercase tracking-tighter border-b border-r border-slate-200 dark:border-slate-800 text-center"
              >
                PAS
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr
              v-for="(student, index) in students"
              :key="index"
              class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors"
            >
              <td
                class="px-6 py-4 text-center text-sm font-black text-slate-400 border-r border-slate-50 dark:border-slate-800"
              >
                {{ index + 1 }}
              </td>
              <td
                class="px-6 py-4 text-sm font-bold text-slate-900 dark:text-white border-r border-slate-50 dark:border-slate-800"
              >
                {{ student.name }}
                <div class="text-[10px] font-medium text-slate-400">
                  NIS: {{ student.nip || "---" }}
                </div>
              </td>
              <td
                class="px-3 py-4 text-center border-r border-slate-50 dark:border-slate-800"
              >
                <div class="flex items-center justify-center gap-1 group/input">
                  <button
                    @click="adjustScore(student, 'tugas', -1)"
                    class="w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-300 hover:text-primary transition-all"
                  >
                    <span class="material-icons text-[14px]">remove</span>
                  </button>
                  <input
                    type="number"
                    v-model="student.scores.tugas"
                    min="0"
                    max="100"
                    :disabled="!selectedSubject"
                    @input="validateScore(student, 'tugas')"
                    class="w-10 text-center bg-transparent border-none text-sm font-black focus:ring-0 text-slate-700 dark:text-slate-200 p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none disabled:opacity-30"
                  />
                  <button
                    @click="adjustScore(student, 'tugas', 1)"
                    class="w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-300 hover:text-primary transition-all"
                  >
                    <span class="material-icons text-[14px]">add</span>
                  </button>
                </div>
              </td>
              <td
                class="px-3 py-4 text-center border-r border-slate-50 dark:border-slate-800"
              >
                <div class="flex items-center justify-center gap-1">
                  <button
                    @click="adjustScore(student, 'harian', -1)"
                    class="w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-300 hover:text-primary transition-all"
                  >
                    <span class="material-icons text-[14px]">remove</span>
                  </button>
                  <input
                    type="number"
                    v-model="student.scores.harian"
                    min="0"
                    max="100"
                    :disabled="!selectedSubject"
                    @input="validateScore(student, 'harian')"
                    class="w-10 text-center bg-transparent border-none text-sm font-black focus:ring-0 text-slate-700 dark:text-slate-200 p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none disabled:opacity-30"
                  />
                  <button
                    @click="adjustScore(student, 'harian', 1)"
                    class="w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-300 hover:text-primary transition-all"
                  >
                    <span class="material-icons text-[14px]">add</span>
                  </button>
                </div>
              </td>
              <td
                class="px-3 py-4 text-center border-r border-slate-50 dark:border-slate-800"
              >
                <div class="flex items-center justify-center gap-1">
                  <button
                    @click="adjustScore(student, 'proyek', -1)"
                    class="w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-300 hover:text-primary transition-all"
                  >
                    <span class="material-icons text-[14px]">remove</span>
                  </button>
                  <input
                    type="number"
                    v-model="student.scores.proyek"
                    min="0"
                    max="100"
                    :disabled="!selectedSubject"
                    @input="validateScore(student, 'proyek')"
                    class="w-10 text-center bg-transparent border-none text-sm font-black focus:ring-0 text-slate-700 dark:text-slate-200 p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none disabled:opacity-30"
                  />
                  <button
                    @click="adjustScore(student, 'proyek', 1)"
                    class="w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-300 hover:text-primary transition-all"
                  >
                    <span class="material-icons text-[14px]">add</span>
                  </button>
                </div>
              </td>
              <td
                class="px-3 py-4 text-center border-r border-slate-50 dark:border-slate-800"
              >
                <div class="flex items-center justify-center gap-1">
                  <button
                    @click="adjustScore(student, 'pts', -1)"
                    class="w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-300 hover:text-primary transition-all"
                  >
                    <span class="material-icons text-[14px]">remove</span>
                  </button>
                  <input
                    type="number"
                    v-model="student.scores.pts"
                    min="0"
                    max="100"
                    :disabled="!selectedSubject"
                    @input="validateScore(student, 'pts')"
                    class="w-10 text-center bg-transparent border-none text-sm font-black focus:ring-0 text-slate-700 dark:text-slate-200 p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none disabled:opacity-30"
                  />
                  <button
                    @click="adjustScore(student, 'pts', 1)"
                    class="w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-300 hover:text-primary transition-all"
                  >
                    <span class="material-icons text-[14px]">add</span>
                  </button>
                </div>
              </td>
              <td
                class="px-3 py-4 text-center border-r border-slate-50 dark:border-slate-800"
              >
                <div class="flex items-center justify-center gap-1">
                  <button
                    @click="adjustScore(student, 'pas', -1)"
                    class="w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-300 hover:text-primary transition-all"
                  >
                    <span class="material-icons text-[14px]">remove</span>
                  </button>
                  <input
                    type="number"
                    v-model="student.scores.pas"
                    min="0"
                    max="100"
                    :disabled="!selectedSubject"
                    @input="validateScore(student, 'pas')"
                    class="w-10 text-center bg-transparent border-none text-sm font-black focus:ring-0 text-slate-700 dark:text-slate-200 p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none disabled:opacity-30"
                  />
                  <button
                    @click="adjustScore(student, 'pas', 1)"
                    class="w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-300 hover:text-primary transition-all"
                  >
                    <span class="material-icons text-[14px]">add</span>
                  </button>
                </div>
              </td>
              <td
                class="px-6 py-4 text-center bg-slate-50/30 dark:bg-slate-800/20"
              >
                <span class="text-sm font-black text-primary">{{
                  calculateAverage(student.scores)
                }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from "../../configs/api.js";
import { useNotificationStore } from "../../stores/notification";

const notification = useNotificationStore();

const isClassDropdownOpen = ref(false);
const isSubjectDropdownOpen = ref(false);
const selectedClass = ref(null);
const selectedSubject = ref(null);
const loading = ref(false);
const classes = ref([]);
const subjects = ref([]);
const students = ref([]);

const selectedClassName = computed(() => {
  const c = classes.value.find((c) => c.id === selectedClass.value);
  return c ? c.name : "";
});

const selectedSubjectName = computed(() => {
  const s = subjects.value.find((s) => s.id === selectedSubject.value);
  return s ? s.name : "";
});

const fetchClasses = async () => {
  try {
    const response = await api.get("/guru/classrooms");
    classes.value = response.data;
  } catch (err) {
    notification.add({
      title: "Gagal",
      message: "Gagal mengambil data kelas",
      type: "error",
    });
  }
};

const fetchSubjects = async () => {
  try {
    const response = await api.get("/guru/subjects");
    subjects.value = response.data;
  } catch (err) {
    notification.add({
      title: "Gagal",
      message: "Gagal mengambil data mata pelajaran",
      type: "error",
    });
  }
};

const selectClass = (c) => {
  selectedClass.value = c.id;
  isClassDropdownOpen.value = false;
  fetchScores();
};

const selectSubject = (s) => {
  selectedSubject.value = s.id;
  isSubjectDropdownOpen.value = false;
  if (selectedClass.value) {
    fetchScores();
  }
};

const fetchScores = async () => {
  if (!selectedClass.value) return;
  loading.value = true;
  try {
    const params = {
      class_id: selectedClass.value,
    };
    if (selectedSubject.value) {
      params.subject_id = selectedSubject.value;
    }
    const response = await api.get("/guru/scores", { params });
    students.value = response.data.students;
  } catch (err) {
    notification.add({
      title: "Gagal",
      message: "Gagal mengambil data nilai",
      type: "error",
    });
  } finally {
    loading.value = false;
  }
};

const exportToExcel = () => {
  if (!selectedClass.value || !selectedSubject.value) return;

  const schoolName = "SEKOLAH MEDIA BELAJAR";
  const title = `DAFTAR NILAI ${selectedSubjectName.value.toUpperCase()} KELAS ${selectedClassName.value.toUpperCase()} - GANJIL 2025/2026`;
  const metadata = `Mata Pelajaran: ${selectedSubjectName.value} | Kelas: ${selectedClassName.value} | Semester: Ganjil | Tahun Pelajaran: 2025/2026`;

  // HTML Template for Excel
  let html = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
      <meta charset="utf-8">
      <!--[if gte mso 9]>
      <xml>
        <x:ExcelWorkbook>
          <x:ExcelWorksheets>
            <x:ExcelWorksheet>
              <x:Name>Nilai_${selectedSubjectName.value}</x:Name>
              <x:WorksheetOptions>
                <x:DisplayGridlines/>
                <x:FreezePanes/>
              </x:WorksheetOptions>
            </x:ExcelWorksheet>
          </x:ExcelWorksheets>
        </x:ExcelWorkbook>
      </xml>
      <![endif]-->
      <style>
        table { border-collapse: collapse; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .title { font-size: 16pt; font-weight: bold; text-align: center; }
        .school { font-size: 12pt; font-style: italic; text-align: center; }
        .metadata { font-size: 10pt; text-align: center; }
        .header { background-color: #003366; color: #ffffff; font-weight: bold; text-align: center; height: 35px; border: 1px solid #000000; }
        .cell { border: 1px solid #cccccc; padding: 5px; height: 25px; }
        .cell-center { border: 1px solid #cccccc; padding: 5px; text-align: center; height: 25px; }
        .cell-name { border: 1px solid #cccccc; padding: 5px; font-weight: bold; height: 25px; }
        .row-alt { background-color: #f2f2f2; }
        .footer-label { font-weight: bold; background-color: #eeeeee; border: 1px solid #000000; }
        .footer-val { font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #eeeeee; }
        .primary-text { color: #0ea5e9; font-weight: bold; }
      </style>
    </head>
    <body>
      <table>
        <!-- Header Section -->
        <tr><td colspan="10" class="title">${title}</td></tr>
        <tr><td colspan="10" class="school">${schoolName}</td></tr>
        <tr><td colspan="10" class="metadata">${metadata}</td></tr>
        <tr><td colspan="10"></td></tr>

        <!-- Table Header -->
        <tr>
          <td class="header">NO</td>
          <td class="header">NIS/NISN</td>
          <td class="header" width="250">NAMA SISWA</td>
          <td class="header">TUGAS</td>
          <td class="header">HARIAN</td>
          <td class="header">PROYEK</td>
          <td class="header">PTS</td>
          <td class="header">PAS</td>
          <td class="header">RERATA</td>
          <td class="header">PREDIKAT</td>
        </tr>
  `;

  // Student Rows
  students.value.forEach((s, index) => {
    const avg = calculateAverage(s.scores);
    const getPredicate = (score) => {
      if (score >= 90) return "A";
      if (score >= 80) return "B";
      if (score >= 70) return "C";
      if (score >= 60) return "D";
      return "E";
    };
    
    const isAlt = index % 2 !== 0;
    const rowClass = isAlt ? 'row-alt' : '';

    html += `
      <tr class="${rowClass}">
        <td class="cell-center">${index + 1}</td>
        <td class="cell-center">${s.nip || "-"}</td>
        <td class="cell-name">${s.name}</td>
        <td class="cell-center">${s.scores.tugas || 0}</td>
        <td class="cell-center">${s.scores.harian || 0}</td>
        <td class="cell-center">${s.scores.proyek || 0}</td>
        <td class="cell-center">${s.scores.pts || 0}</td>
        <td class="cell-center">${s.scores.pas || 0}</td>
        <td class="cell-center primary-text">${avg}</td>
        <td class="cell-center">${getPredicate(avg)}</td>
      </tr>
    `;
  });

  // Footer Stats
  const calculateStat = (type, fn) => {
    const scores = students.value.map(s => Number(s.scores[type]) || 0);
    if (scores.length === 0) return 0;
    return fn(...scores);
  };

  const calculateAvgStat = (type) => {
    const scores = students.value.map(s => Number(s.scores[type]) || 0);
    if (scores.length === 0) return 0;
    const sum = scores.reduce((a, b) => a + b, 0);
    return (sum / scores.length).toFixed(1);
  };

  html += `
    <tr><td colspan="10"></td></tr>
    <tr>
      <td colspan="3" class="footer-label" align="right">RATA-RATA KELAS &nbsp;</td>
      <td class="footer-val">${calculateAvgStat('tugas')}</td>
      <td class="footer-val">${calculateAvgStat('harian')}</td>
      <td class="footer-val">${calculateAvgStat('proyek')}</td>
      <td class="footer-val">${calculateAvgStat('pts')}</td>
      <td class="footer-val">${calculateAvgStat('pas')}</td>
      <td colspan="2" class="footer-val"></td>
    </tr>
    <tr>
      <td colspan="3" class="footer-label" align="right">NILAI TERTINGGI &nbsp;</td>
      <td class="footer-val">${calculateStat('tugas', Math.max)}</td>
      <td class="footer-val">${calculateStat('harian', Math.max)}</td>
      <td class="footer-val">${calculateStat('proyek', Math.max)}</td>
      <td class="footer-val">${calculateStat('pts', Math.max)}</td>
      <td class="footer-val">${calculateStat('pas', Math.max)}</td>
      <td colspan="2" class="footer-val"></td>
    </tr>
    <tr>
      <td colspan="3" class="footer-label" align="right">NILAI TERENDAH &nbsp;</td>
      <td class="footer-val">${calculateStat('tugas', Math.min)}</td>
      <td class="footer-val">${calculateStat('harian', Math.min)}</td>
      <td class="footer-val">${calculateStat('proyek', Math.min)}</td>
      <td class="footer-val">${calculateStat('pts', Math.min)}</td>
      <td class="footer-val">${calculateStat('pas', Math.min)}</td>
      <td colspan="2" class="footer-val"></td>
    </tr>
  `;

  html += "</table></body></html>";

  // Download logic
  const uri = 'data:application/vnd.ms-excel;base64,';
  const downloadLink = document.createElement("a");
  document.body.appendChild(downloadLink);
  
  const base64 = btoa(unescape(encodeURIComponent(html)));
  downloadLink.href = uri + base64;
  downloadLink.download = `Rekap_Nilai_${selectedSubjectName.value}_${selectedClassName.value}.xls`;
  downloadLink.click();
  document.body.removeChild(downloadLink);

  notification.add({
    title: "Ekspor Berhasil",
    message: "Laporan Excel profesional telah diunduh.",
    type: "success",
  });
};

const saveScores = async () => {
  if (!selectedClass.value || !selectedSubject.value) return;
  loading.value = true;
  try {
    const payload = {
      class_id: selectedClass.value,
      subject_id: selectedSubject.value,
      scores: students.value.map((s) => ({
        student_id: s.id,
        tugas: s.scores.tugas,
        harian: s.scores.harian,
        proyek: s.scores.proyek,
        pts: s.scores.pts,
        pas: s.scores.pas,
      })),
    };
    await api.post("/guru/scores", payload);
    notification.add({
      title: "Berhasil",
      message: "Nilai berhasil disimpan",
      type: "success",
    });
  } catch (err) {
    notification.add({
      title: "Gagal",
      message: "Gagal menyimpan nilai",
      type: "error",
    });
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  await Promise.all([fetchClasses(), fetchSubjects()]);
  window.addEventListener("click", () => {
    isClassDropdownOpen.value = false;
    isSubjectDropdownOpen.value = false;
  });
});

const calculateAverage = (scores) => {
  const values = Object.values(scores);
  if (values.length === 0) return 0;
  const sum = values.reduce((a, b) => a + (Number(b) || 0), 0);
  return (sum / values.length).toFixed(1);
};

const adjustScore = (student, type, amount) => {
  if (!selectedSubject.value) return;
  let val = Number(student.scores[type]) || 0;
  val = Math.max(0, Math.min(100, val + amount));
  student.scores[type] = val;
};

const validateScore = (student, type) => {
  let val = student.scores[type];
  if (val > 100) student.scores[type] = 100;
  if (val < 0) student.scores[type] = 0;
};
</script>
