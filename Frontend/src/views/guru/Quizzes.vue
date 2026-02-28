<template>
  <div class="space-y-6">
    <div
      class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"
    >
      <div>
        <h1
          class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight"
        >
          Daftar Quiz Saya
        </h1>
        <p class="text-sm text-slate-500 font-medium mt-1">
          Kelola dan lihat performa quiz yang telah Anda buat.
        </p>
      </div>
      <button
        @click="router.push('/guru/quizzes/create')"
        class="inline-flex items-center px-4 py-3 bg-primary border border-transparent rounded-xl text-xs font-black uppercase tracking-widest text-white hover:bg-primary-hover shadow-lg shadow-primary/30 transition-all"
      >
        <span class="material-icons text-lg mr-2">add</span>
        Buat Quiz Baru
      </button>
    </div>

    <!-- Loading State -->
    <div
      v-if="loading"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
    >
      <div
        v-for="i in 6"
        :key="i"
        class="h-64 bg-slate-100 dark:bg-slate-800 rounded-[28px] animate-pulse"
      ></div>
    </div>

    <template v-else>
      <!-- Subject Filters -->
      <div class="flex items-center gap-3 overflow-x-auto pb-4 scrollbar-none">
        <button 
          @click="selectedSubject = null"
          :class="`px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap border-2 ${!selectedSubject ? 'bg-primary border-primary text-white shadow-lg shadow-primary/20' : 'bg-white dark:bg-surface-dark border-slate-100 dark:border-slate-800 text-slate-400 hover:border-primary/30'}`"
        >
          Semua
        </button>
        <button 
          v-for="sub in subjects" 
          :key="sub.id"
          @click="selectedSubject = sub.id"
          :class="`px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap border-2 ${selectedSubject === sub.id ? 'bg-primary border-primary text-white shadow-lg shadow-primary/20' : 'bg-white dark:bg-surface-dark border-slate-100 dark:border-slate-800 text-slate-400 hover:border-primary/30'}`"
        >
          {{ sub.name }}
        </button>
      </div>

      <!-- Quiz Grid -->
      <div
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"
      >
        <div
          v-for="quiz in filteredQuizzes"
          :key="quiz.id"
          class="bg-white dark:bg-surface-dark rounded-[28px] border border-slate-100 dark:border-slate-800 overflow-hidden hover:shadow-xl hover:border-primary/20 transition-all group"
        >
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <span
                class="px-2.5 py-1 bg-primary text-white text-[10px] font-black uppercase rounded-lg tracking-wider truncate max-w-[120px] shadow-sm shadow-primary/20"
              >
                {{ quiz.subject?.name || quiz.category?.name || "Umum" }}
              </span>
              <div
                class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity"
              >
                <button
                  @click="editQuiz(quiz.id)"
                  class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl text-slate-400 hover:text-primary transition-all"
                >
                  <span class="material-icons text-sm">edit</span>
                </button>
                <button
                  @click="confirmDelete(quiz.id)"
                  class="p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl text-slate-400 hover:text-red-500 transition-all"
                >
                  <span class="material-icons text-sm">delete</span>
                </button>
              </div>
            </div>

            <h3
              class="font-black text-lg text-slate-900 dark:text-white mb-2 leading-tight truncate uppercase tracking-tight"
            >
              {{ quiz.title }}
            </h3>
            <p
              class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 mb-6 h-10 font-medium leading-relaxed"
            >
              {{ quiz.description }}
            </p>

            <div
              class="flex items-center gap-4 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6"
            >
              <span class="flex items-center gap-1.5"
                ><span class="material-icons text-sm">help_outline</span>
                {{ quiz.questions_count || 0 }} Soal</span
              >
              <span class="flex items-center gap-1.5"
                ><span class="material-icons text-sm">timer</span>
                {{ formatDuration(quiz.duration_seconds) }}</span
              >
            </div>

            <div
              class="flex items-center justify-between pt-5 border-t border-slate-50 dark:border-slate-800"
            >
              <div class="flex items-center gap-2">
                <div
                  :class="`w-2 h-2 rounded-full ${quiz.is_active ? 'bg-green-500' : 'bg-slate-300'}`"
                ></div>
                <span
                  class="text-[10px] font-black uppercase tracking-widest text-slate-500"
                  >{{ quiz.is_active ? "Aktif" : "Draft" }}</span
                >
              </div>
              <button
                @click="router.push(`/guru/quizzes/${quiz.id}/edit`)"
                class="px-4 py-2 bg-primary/10 text-primary text-[10px] font-black uppercase rounded-xl hover:bg-primary hover:text-white transition-all active:scale-95 shadow-sm shadow-primary/5"
              >
                Kelola Detail
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-if="quizzes.length === 0"
        class="text-center py-24 bg-white dark:bg-surface-dark rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm"
      >
        <div
          class="inline-flex items-center justify-center w-20 h-20 rounded-[24px] bg-slate-50 dark:bg-slate-800 text-slate-300 mb-6 group hover:rotate-12 transition-transform duration-500"
        >
          <span class="material-icons text-5xl">auto_awesome</span>
        </div>
        <h3
          class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight"
        >
          Belum ada quiz
        </h3>
        <p class="text-slate-400 mt-2 font-medium">
          Mulai buat quiz pertama Anda untuk siswa dangan menekan tombol di
          atas.
        </p>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from '@/configs/api'
import { useNotificationStore } from "../../stores/notification";

const router = useRouter();
const notification = useNotificationStore();
const quizzes = ref([]);
const subjects = ref([]);
const classrooms = ref([]);
const isCreating = ref(false);
const loading = ref(false);
const selectedSubject = ref(null);

const filteredQuizzes = computed(() => {
  if (!selectedSubject.value) return quizzes.value;
  return quizzes.value.filter(q => q.subject_id === selectedSubject.value);
});

const createNewQuiz = async () => {
  if (isCreating.value) return;
  isCreating.value = true;

  try {
    const token = localStorage.getItem("access_token");
    // Get first category ID as default if available
    let defaultCatId = categories.value[0]?.id || 1;

    const response = await api.post(
      "/guru/quizzes",
      {
        title: "Draft Quiz Baru",
        description: "Deskripsi quiz...",
        category_id: defaultCatId,
        classroom_id: null,
        duration_seconds: 1800,
      }
    );

    notification.add({
      title: "Quiz Dibuat",
      message: "Mengarahkan ke editor...",
      type: "success",
    });

    router.push(`/guru/quizzes/${response.data.quiz.id}/edit`);
  } catch (err) {
    console.error("Gagal membuat quiz", err);
    notification.add({
      title: "Gagal!",
      message: "Tidak dapat membuat quiz baru.",
      type: "error",
    });
  } finally {
    isCreating.value = false;
  }
};

const fetchClassrooms = async () => {
  try {
    const token = localStorage.getItem("access_token");
    const response = await api.get(
      "/public/classrooms"
    );
    classrooms.value = response.data;
  } catch (err) {
    console.error("Gagal mengambil data kelas", err);
  }
};

const fetchSubjects = async () => {
  try {
    const token = localStorage.getItem("access_token");
    const response = await api.get(
      "/guru/subjects"
    );
    console.log("Subjects fetched:", response.data);
    subjects.value = response.data;
  } catch (err) {
    console.error("Gagal mengambil mata pelajaran", err);
  }
};

const fetchQuizzes = async () => {
  loading.value = true;
  try {
    const token = localStorage.getItem("access_token");
    const response = await api.get("/guru/quizzes");
    quizzes.value = response.data;
  } catch (err) {
    console.error("Gagal mengambil data quiz", err);
  } finally {
    loading.value = false;
  }
};

const confirmDelete = (id) => {
  if (confirm("Apakah Anda yakin ingin menghapus quiz ini?")) {
    deleteQuiz(id);
  }
};

const deleteQuiz = async (id) => {
  try {
    const token = localStorage.getItem("access_token");
    await api.delete(`/guru/quizzes/${id}`);

    notification.add({
      title: "Berhasil",
      message: "Quiz telah dihapus",
      type: "success",
    });

    // Remove from local list
    quizzes.value = quizzes.value.filter((q) => q.id !== id);
  } catch (err) {
    console.error("Gagal menghapus quiz", err);
    notification.add({
      title: "Gagal",
      message: "Tidak dapat menghapus quiz.",
      type: "error",
    });
  }
};

onMounted(() => {
  fetchQuizzes();
  fetchClassrooms();
  fetchSubjects();
});

const formatDuration = (seconds) => {
  if (!seconds) return "0 menit";
  const minutes = Math.floor(seconds / 60);
  return minutes + " menit";
};

import { onUnmounted } from "vue";
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}
</style>
