<template>
  <div
    class="h-screen flex flex-col overflow-hidden bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-100"
  >
    <!-- Header -->
    <header
      class="bg-surface-light dark:bg-surface-dark border-b border-border-light dark:border-border-dark h-16 flex items-center justify-between px-4 sm:px-6 shadow-sm z-20 shrink-0"
    >
      <div class="flex items-center gap-2 sm:gap-4">
        <button
          @click="isQuestionListOpen = true"
          class="lg:hidden text-slate-500 hover:text-primary p-1"
        >
          <span class="material-icons">menu_open</span>
        </button>
        <button
          @click="router.back()"
          class="text-slate-400 hover:text-primary transition-colors hidden xs:block"
        >
          <span class="material-icons">arrow_back</span>
        </button>
        <div class="flex flex-col min-w-0">
          <nav
            class="flex text-[9px] sm:text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-0.5 whitespace-nowrap overflow-hidden"
          >
            <span>My Quizzes</span>
            <span class="mx-1">/</span>
            <span class="truncate">{{
              quiz.category?.name || "Category"
            }}</span>
          </nav>
          <h1
            class="text-sm sm:text-lg font-bold leading-tight truncate max-w-[120px] sm:max-w-none"
          >
            {{ quiz.id ? (quiz.title || "Untitled Quiz") : "Buat Quiz Baru" }}
          </h1>
        </div>
      </div>

      <div class="flex items-center gap-2 sm:gap-4 ml-auto">
        <!-- Timer Quiz -->
        <div
          class="hidden sm:flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-100 dark:border-slate-700 hover:border-primary/30 transition-all cursor-default group"
        >
          <span
            class="material-icons text-slate-400 group-hover:text-primary transition-colors text-sm"
            >timer</span
          >
          <div
            class="flex items-center bg-white dark:bg-slate-900 rounded-lg px-1.5 border border-slate-100 dark:border-slate-700"
          >
            <input
              v-model="durationMin"
              @change="updateDuration"
              type="number"
              class="w-7 bg-transparent border-none p-1 text-center text-[11px] font-black focus:ring-0"
              placeholder="00"
            />
            <span class="text-[10px] font-bold text-slate-300">:</span>
            <input
              v-model="durationSec"
              @change="updateDuration"
              type="number"
              class="w-7 bg-transparent border-none p-1 text-center text-[11px] font-black focus:ring-0"
              placeholder="00"
            />
          </div>
          <span
            class="text-[9px] font-black uppercase text-slate-400 tracking-tighter"
            >Limit</span
          >
        </div>

        <!-- Class Selection Custom Dropdown -->
        <div class="hidden md:block relative">
          <button
            @click.stop="isClassDropdownOpen = !isClassDropdownOpen"
            class="flex items-center gap-2 bg-white dark:bg-slate-800 px-4 py-2.5 rounded-xl border-2 border-transparent hover:border-primary/30 transition-all group/cls shadow-sm"
            :class="{
              'border-primary shadow-lg shadow-primary/5': isClassDropdownOpen,
            }"
          >
            <span
              class="material-icons text-primary transition-colors text-sm sm:text-lg"
              >class</span
            >
            <div class="flex flex-col items-start leading-none">
              <span
                class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5"
                >Target</span
              >
              <span
                class="text-[10px] font-black uppercase tracking-widest text-slate-700 dark:text-slate-200"
              >
                {{ selectedClassroomName }}
              </span>
            </div>
            <span
              class="material-icons text-slate-400 text-sm transition-transform"
              :class="{ 'rotate-180': isClassDropdownOpen }"
              >expand_more</span
            >
          </button>

          <!-- Dropdown Menu -->
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
              class="absolute top-full right-0 mt-2 w-64 bg-white/90 dark:bg-surface-dark/95 backdrop-blur-xl border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl py-2 z-50 overflow-hidden"
            >
              <div
                @click="
                  quiz.classroom_id = null;
                  isClassDropdownOpen = false;
                "
                :class="`px-5 py-3.5 text-[10px] font-black uppercase tracking-widest hover:bg-primary/5 hover:text-primary transition-all flex items-center gap-3 cursor-pointer ${quiz.classroom_id === null ? 'text-primary bg-primary/5' : 'text-slate-500 hover:pl-6'}`"
              >
                <div
                  :class="`w-8 h-8 rounded-lg flex items-center justify-center ${quiz.classroom_id === null ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800'}`"
                >
                  <span class="material-icons text-sm">public</span>
                </div>
                Semua Kelas (Public)
              </div>
              <div
                class="h-px bg-slate-50 dark:bg-slate-800/50 my-1 mx-2"
              ></div>
              <div class="max-h-[240px] overflow-y-auto custom-scrollbar">
                <div
                  v-for="cls in classrooms"
                  :key="cls.id"
                  @click="
                    quiz.classroom_id = cls.id;
                    isClassDropdownOpen = false;
                  "
                  :class="`px-5 py-3.5 text-[10px] font-black uppercase tracking-widest hover:bg-primary/5 hover:text-primary transition-all flex items-center gap-3 cursor-pointer ${quiz.classroom_id === cls.id ? 'text-primary bg-primary/5' : 'text-slate-500 hover:pl-6'}`"
                >
                  <div
                    :class="`w-8 h-8 rounded-lg flex items-center justify-center ${quiz.classroom_id === cls.id ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800'}`"
                  >
                    <span class="material-icons text-sm">class</span>
                  </div>
                  {{ cls.name }}
                </div>
                <div
                  v-if="classrooms.length === 0"
                  class="px-5 py-8 text-center"
                >
                  <span class="material-icons text-slate-300 text-3xl mb-2"
                    >sensor_door</span
                  >
                  <p
                    class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                  >
                    Belum ada kelas
                  </p>
                </div>
              </div>
            </div>
          </Transition>
        </div>

        <button
          @click="saveQuiz"
          :disabled="saving"
          class="px-3 py-2 sm:px-6 sm:py-2 text-sm font-bold text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/30 rounded-lg transition-all flex items-center gap-2 disabled:opacity-50"
        >
          <span class="material-icons text-lg">{{
            saving ? "hourglass_empty" : "publish"
          }}</span>
          <span class="hidden xs:inline">{{
            saving ? "Publishing..." : "Publish"
          }}</span>
        </button>
      </div>
    </header>

    <main class="flex flex-1 overflow-hidden relative">
      <!-- Sidebar Overlay -->
      <div
        v-if="isQuestionListOpen"
        @click="isQuestionListOpen = false"
        class="lg:hidden absolute inset-0 bg-slate-900/40 backdrop-blur-sm z-30"
      ></div>

      <!-- Left Sidebar: Question List -->
      <aside
        :class="`fixed lg:static inset-y-0 left-0 z-40 w-80 bg-background-light dark:bg-background-dark border-r border-border-light dark:border-border-dark flex flex-col shrink-0 overflow-hidden transition-transform duration-300 transform ${isQuestionListOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}`"
      >
        <div
          class="p-4 border-b border-border-light dark:border-border-dark flex items-center justify-between bg-white/50 dark:bg-surface-dark/50"
        >
          <span
            class="text-xs font-bold text-slate-400 uppercase tracking-widest"
            >Questions ({{ quiz.questions?.length || 0 }})</span
          >
          <button
            @click="isQuestionListOpen = false"
            class="lg:hidden text-slate-400 hover:text-primary"
          >
            <span class="material-icons">close</span>
          </button>
        </div>

        <div class="p-4 space-y-3">
          <!-- Quiz Info & Settings Trigger -->
          <div
            @click="
              activeQuestionIdx = -1;
              isQuestionListOpen = false;
            "
            :class="`group relative p-4 rounded-xl border-2 transition-all cursor-pointer ${activeQuestionIdx === -1 ? 'bg-primary/5 border-primary shadow-sm' : 'bg-white/40 border-transparent hover:border-slate-200'}`"
          >
            <div class="flex items-center gap-3">
              <div
                :class="`w-10 h-10 rounded-xl flex items-center justify-center transition-colors ${activeQuestionIdx === -1 ? 'bg-primary text-white' : 'bg-slate-100 text-slate-400 group-hover:text-primary'}`"
              >
                <span class="material-icons">settings</span>
              </div>
              <div class="flex-1 min-w-0">
                <p
                  :class="`text-xs font-black uppercase tracking-widest ${activeQuestionIdx === -1 ? 'text-primary' : 'text-slate-500'}`"
                >
                  Quiz Settings
                </p>
                <p class="text-[10px] font-bold text-slate-400 truncate">
                  {{ quiz.title || "Untitled" }}
                </p>
              </div>
            </div>
          </div>

          <button
            @click="
              addNewQuestion();
              isQuestionListOpen = false;
            "
            class="w-full py-3 px-4 bg-primary/5 hover:bg-primary/10 text-primary font-bold rounded-xl border-2 border-primary/20 border-dashed flex items-center justify-center gap-2 transition-all group"
          >
            <span
              class="material-icons group-hover:scale-110 transition-transform"
              >add_circle_outline</span
            >
            Add New Question
          </button>
        </div>

        <div class="flex-1 overflow-y-auto px-4 pb-4 space-y-3 scrollbar-thin">
          <div
            v-for="(q, idx) in quiz.questions"
            :key="idx"
            @click="
              activeQuestionIdx = idx;
              isQuestionListOpen = false;
            "
            :class="`group relative p-4 rounded-xl border-2 transition-all cursor-pointer ${activeQuestionIdx === idx ? 'bg-white dark:bg-surface-dark border-primary shadow-md' : 'bg-white/40 border-transparent hover:border-slate-200'}`"
          >
            <div
              class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1"
            >
              <button
                @click.stop="deleteQuestion(idx)"
                class="p-1 hover:bg-red-50 dark:hover:bg-red-900/20 rounded text-slate-400 hover:text-red-500"
              >
                <span class="material-icons text-sm">delete</span>
              </button>
            </div>

            <div class="flex gap-3">
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1.5">
                  <span
                    :class="`text-[10px] font-bold px-1.5 py-0.5 rounded ${activeQuestionIdx === idx ? 'bg-primary text-white' : 'bg-slate-100 text-slate-500'}`"
                    >Q{{ idx + 1 }}</span
                  >
                  <span
                    class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                    >{{ q.type.replace("_", " ") }}</span
                  >
                </div>
                <p
                  class="text-sm font-semibold truncate text-slate-700 dark:text-slate-200"
                >
                  {{ q.question_text || "Untitled Question" }}
                </p>
                <div
                  class="mt-2 flex items-center gap-3 text-[10px] font-bold text-slate-400 uppercase"
                >
                  <span class="flex items-center gap-1"
                    ><span class="material-icons text-xs">schedule</span>
                    30s</span
                  >
                  <span class="flex items-center gap-1"
                    ><span class="material-icons text-xs">grade</span>
                    {{ q.points }}pts</span
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </aside>

      <!-- Main Editor -->
      <section
        v-if="activeQuestion"
        class="flex-1 bg-white dark:bg-surface-dark overflow-y-auto relative flex flex-col"
      >
        <!-- Editor Toolbar -->
        <div
          class="px-4 sm:px-8 py-4 sm:py-5 border-b border-border-light dark:border-border-dark flex flex-wrap items-center justify-between sticky top-0 bg-white/95 dark:bg-surface-dark/95 backdrop-blur z-10 gap-4"
        >
          <div class="flex items-center gap-3 sm:gap-4">
            <span
              class="bg-primary text-white w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center font-bold text-sm"
              >{{ activeQuestionIdx + 1 }}</span
            >
            <div class="relative min-w-[170px] sm:min-w-[200px]">
              <button
                @click.stop="isTypeDropdownOpen = !isTypeDropdownOpen"
                class="w-full flex items-center justify-between px-4 py-2 sm:py-2.5 bg-slate-50 dark:bg-slate-800 border-2 border-transparent hover:border-primary/30 rounded-xl text-slate-700 dark:text-slate-200 text-xs sm:text-sm font-black transition-all group/type"
                :class="{
                  'border-primary shadow-lg shadow-primary/5':
                    isTypeDropdownOpen,
                }"
              >
                <div class="flex items-center gap-2">
                  <span
                    class="material-icons text-primary text-sm sm:text-lg"
                    >{{ getQuestionTypeIcon(activeQuestion.type) }}</span
                  >
                  <span class="uppercase tracking-widest">{{
                    formatQuestionType(activeQuestion.type)
                  }}</span>
                </div>
                <span
                  class="material-icons text-slate-400 text-sm transition-transform"
                  :class="{ 'rotate-180': isTypeDropdownOpen }"
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
                  v-if="isTypeDropdownOpen"
                  class="absolute top-full left-0 mt-2 w-full bg-white/90 dark:bg-surface-dark/95 backdrop-blur-xl border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl py-2 z-50 overflow-hidden"
                >
                  <div
                    v-for="type in ['multiple_choice', 'short_answer']"
                    :key="type"
                    @click="
                      activeQuestion.type = type;
                      isTypeDropdownOpen = false;
                    "
                    :class="`px-5 py-3.5 text-[10px] sm:text-[11px] font-black uppercase tracking-[0.1em] hover:bg-primary/5 hover:text-primary transition-all flex items-center gap-3 cursor-pointer border-b border-slate-50 dark:border-white/5 last:border-0 ${activeQuestion.type === type ? 'text-primary bg-primary/5' : 'text-slate-500 hover:pl-6'}`"
                  >
                    <span class="material-icons text-base sm:text-lg">{{
                      getQuestionTypeIcon(type)
                    }}</span>
                    {{ formatQuestionType(type) }}
                  </div>
                </div>
              </Transition>
            </div>
          </div>

          <div class="flex items-center gap-4 sm:gap-6 ml-auto sm:ml-0">
            <div class="flex flex-col items-end">
              <label
                class="text-[8px] sm:text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-1"
              >
                Points
                {{
                  activeQuestion.type === "multiple_choice"
                    ? "(Auto)"
                    : "(Manual)"
                }}
              </label>
              <div class="relative">
                <input
                  v-model="activeQuestion.points"
                  type="number"
                  :disabled="activeQuestion.type === 'multiple_choice'"
                  class="w-16 sm:w-20 text-center py-2 bg-slate-50/50 dark:bg-slate-800/50 border-none rounded-lg text-xs sm:text-sm font-bold focus:ring-2 focus:ring-primary shadow-inner disabled:opacity-70"
                />
                <div
                  v-if="activeQuestion.type === 'multiple_choice'"
                  class="absolute -top-1 -right-1"
                >
                  <span class="flex h-2 w-2">
                    <span
                      class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"
                    ></span>
                    <span
                      class="relative inline-flex rounded-full h-2 w-2 bg-primary"
                    ></span>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div
          class="p-4 sm:p-8 max-w-4xl mx-auto w-full space-y-6 sm:space-y-10"
        >
          <!-- Question Content -->
          <div class="space-y-3">
            <label
              class="block text-xs font-bold text-slate-400 uppercase tracking-widest"
              >Question</label
            >
            <textarea
              v-model="activeQuestion.question_text"
              class="w-full p-4 sm:p-6 text-base sm:text-xl font-medium text-slate-800 dark:text-slate-100 bg-slate-50/30 dark:bg-slate-800/20 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all resize-none shadow-sm"
              placeholder="Type your question here..."
              rows="4"
            ></textarea>
          </div>

          <!-- Media Section -->
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <label
                class="block text-xs font-bold text-slate-400 uppercase tracking-widest"
                >Question Media</label
              >
              <div class="flex gap-2">
                <button
                  v-for="t in ['image', 'video', 'youtube']"
                  :key="t"
                  @click="
                    activeQuestion.media_type =
                      activeQuestion.media_type === t ? 'none' : t
                  "
                  :class="`px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-tighter transition-all ${activeQuestion.media_type === t ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600'}`"
                >
                  {{ t }}
                </button>
              </div>
            </div>

            <!-- YouTube Input -->
            <div
              v-if="activeQuestion.media_type === 'youtube'"
              class="space-y-3"
            >
              <div class="relative">
                <input
                  v-model="activeQuestion.media_url"
                  class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary text-sm font-bold"
                  placeholder="Paste YouTube Video URL (e.g. https://youtu.be/...)"
                />
                <span class="material-icons absolute left-4 top-3 text-red-500"
                  >play_circle</span
                >
              </div>

              <div
                class="flex items-center gap-4 bg-slate-50 dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-700"
              >
                <div class="flex-1">
                  <label
                    class="block text-[10px] font-black uppercase text-slate-400 mb-1"
                    >Start (MM:SS)</label
                  >
                  <div
                    class="flex items-center gap-1 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-lg px-2"
                  >
                    <input
                      v-model="ytStartMin"
                      @change="updateYTTimer"
                      type="number"
                      class="w-8 bg-transparent border-none p-1.5 text-center text-xs font-bold focus:ring-0"
                      placeholder="00"
                    />
                    <span class="text-slate-300 font-bold">:</span>
                    <input
                      v-model="ytStartSec"
                      @change="updateYTTimer"
                      type="number"
                      class="w-8 bg-transparent border-none p-1.5 text-center text-xs font-bold focus:ring-0"
                      placeholder="00"
                    />
                  </div>
                </div>
                <div class="flex-1">
                  <label
                    class="block text-[10px] font-black uppercase text-slate-400 mb-1"
                    >End (MM:SS)</label
                  >
                  <div
                    class="flex items-center gap-1 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-lg px-2"
                  >
                    <input
                      v-model="ytEndMin"
                      @change="updateYTTimer"
                      type="number"
                      class="w-8 bg-transparent border-none p-1.5 text-center text-xs font-bold focus:ring-0"
                      placeholder="00"
                    />
                    <span class="text-slate-300 font-bold">:</span>
                    <input
                      v-model="ytEndSec"
                      @change="updateYTTimer"
                      type="number"
                      class="w-8 bg-transparent border-none p-1.5 text-center text-xs font-bold focus:ring-0"
                      placeholder="00"
                    />
                  </div>
                </div>
                <div class="text-[10px] text-slate-400 font-bold max-w-[120px]">
                  kosongkan jika ingin memutar penuh
                </div>
              </div>
            </div>

            <!-- File Upload -->
            <div
              v-else-if="['image', 'video'].includes(activeQuestion.media_type)"
              @click="triggerUpload"
              class="relative border-2 border-dashed border-slate-200 dark:border-slate-800 hover:border-primary/40 rounded-2xl p-8 transition-all cursor-pointer group flex flex-col items-center justify-center bg-white dark:bg-surface-dark overflow-hidden"
            >
              <input
                ref="fileInput"
                type="file"
                class="hidden"
                @change="handleFileUpload"
                :accept="mediaAccept"
              />

              <template v-if="!activeQuestion.media_url">
                <div v-if="uploading" class="flex flex-col items-center">
                  <span
                    class="material-icons animate-spin text-primary text-3xl mb-2"
                    >refresh</span
                  >
                  <p class="text-xs font-bold text-slate-400">Uploading...</p>
                </div>
                <template v-else>
                  <div
                    class="w-12 h-12 rounded-2xl bg-primary/5 group-hover:bg-primary/10 flex items-center justify-center mb-3"
                  >
                    <span class="material-icons text-primary">{{
                      mediaIcon
                    }}</span>
                  </div>
                  <p class="text-[11px] font-bold text-slate-500">
                    Upload {{ activeQuestion.media_type.toUpperCase() }}
                  </p>
                </template>
              </template>

              <template v-else>
                <div
                  class="group/preview relative w-full aspect-video sm:aspect-[21/9] rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-900 flex items-center justify-center"
                >
                  <img
                    v-if="activeQuestion.media_type === 'image'"
                    :src="getFullUrl(activeQuestion.media_url)"
                    class="w-full h-full object-cover"
                  />
                  <video
                    v-else-if="activeQuestion.media_type === 'video'"
                    :src="getFullUrl(activeQuestion.media_url)"
                    class="w-full h-full object-contain"
                    preload="metadata"
                  ></video>
                  <div v-else class="flex flex-col items-center">
                    <span class="material-icons text-primary text-4xl mb-2">{{
                      mediaIcon
                    }}</span>
                    <p
                      class="text-[10px] font-bold text-slate-500 truncate max-w-[200px]"
                    >
                      {{ activeQuestion.media_url.split("/").pop() }}
                    </p>
                  </div>
                  <div
                    class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover/preview:opacity-100 transition-opacity flex items-center justify-center gap-3"
                  >
                    <button
                      @click.stop="triggerUpload"
                      class="w-10 h-10 rounded-full bg-white text-slate-900 flex items-center justify-center hover:scale-110 shadow-lg"
                    >
                      <span class="material-icons">edit</span>
                    </button>
                    <button
                      @click.stop="activeQuestion.media_url = ''"
                      class="w-10 h-10 rounded-full bg-red-500 text-white flex items-center justify-center hover:scale-110 shadow-lg"
                    >
                      <span class="material-icons">delete</span>
                    </button>
                  </div>
                </div>
              </template>
            </div>
          </div>

          <!-- Answer Options -->
          <div v-if="activeQuestion.type !== 'short_answer'" class="space-y-6">
            <label
              class="block text-xs font-bold text-slate-400 uppercase tracking-widest"
              >Answer Options</label
            >

            <div class="space-y-3">
              <div
                v-for="(opt, oIdx) in activeQuestion.options"
                :key="oIdx"
                class="flex items-center gap-2 sm:gap-4 group"
              >
                <button
                  @click="setCorrectOption(oIdx)"
                  :class="`w-7 h-7 sm:w-8 sm:h-8 rounded-full border-2 sm:border-3 shrink-0 flex items-center justify-center transition-all ${opt.is_correct ? 'bg-primary border-primary text-white scale-110 shadow-lg shadow-primary/30' : 'border-slate-200 dark:border-slate-800 hover:border-primary/50 text-transparent'}`"
                >
                  <span class="material-icons text-base sm:text-lg">check</span>
                </button>
                <div class="flex-1 relative">
                  <span
                    :class="`absolute left-3 sm:left-4 top-3 sm:top-3.5 text-[9px] sm:text-[10px] font-black ${opt.is_correct ? 'text-primary' : 'text-slate-300'}`"
                    >{{ String.fromCharCode(65 + oIdx) }}</span
                  >
                  <input
                    v-model="opt.option_text"
                    :class="`w-full pl-8 sm:pl-10 pr-12 sm:pr-24 py-3 sm:py-3.5 bg-white dark:bg-surface-dark border-2 sm:border-3 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold transition-all ${opt.is_correct ? 'border-primary ring-4 ring-primary/5' : 'border-slate-100 dark:border-slate-800 focus:border-primary/50'}`"
                    :placeholder="`Option ${String.fromCharCode(65 + oIdx)}...`"
                  />
                  <span
                    v-if="opt.is_correct"
                    class="absolute right-3 sm:right-4 top-3 sm:top-3.5 text-[9px] sm:text-[10px] font-black text-primary flex items-center gap-1 uppercase tracking-widest"
                  >
                    <span
                      class="material-icons text-xs sm:text-sm hidden sm:inline"
                      >check_circle</span
                    >
                    <span class="hidden xs:inline">Correct</span>
                  </span>
                </div>
                <button
                  v-if="activeQuestion.options.length > 2"
                  @click="activeQuestion.options.splice(oIdx, 1)"
                  class="p-1.5 sm:p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-xl transition-all opacity-100 xs:opacity-0 group-hover:opacity-100"
                >
                  <span class="material-icons text-lg sm:material-icons"
                    >delete_outline</span
                  >
                </button>
              </div>
            </div>

            <button
              @click="
                activeQuestion.options.push({
                  option_text: '',
                  is_correct: false,
                })
              "
              class="ml-9 sm:ml-12 text-xs sm:text-sm font-bold text-primary hover:text-primary-hover flex items-center gap-2 group"
            >
              <span
                class="material-icons text-base sm:text-lg group-hover:scale-125 transition-transform"
                >add</span
              >
              Add Option
            </button>
          </div>
        </div>

        <div class="h-24 shrink-0"></div>
      </section>

      <!-- Quiz Settings Panel (Integrated) -->
      <section
        v-else-if="activeQuestionIdx === -1"
        class="flex-1 bg-white dark:bg-surface-dark overflow-y-auto relative flex flex-col"
      >
        <!-- Panel Header -->
        <div
          class="px-4 sm:px-8 py-4 sm:py-5 border-b border-border-light dark:border-border-dark flex items-center gap-4 sticky top-0 bg-white/95 dark:bg-surface-dark/95 backdrop-blur z-10"
        >
          <div
            class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-primary/10 text-primary flex items-center justify-center"
          >
            <span class="material-icons text-xl sm:text-2xl">settings</span>
          </div>
          <div>
            <h2
              class="text-sm font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5"
            >
              Quiz Configuration
            </h2>
            <h1
              class="text-base sm:text-xl font-black text-slate-800 dark:text-white tracking-tighter uppercase"
            >
              Informasi & Pengaturan
            </h1>
          </div>
        </div>

        <div
          class="p-4 sm:p-8 max-w-4xl mx-auto w-full space-y-8 sm:space-y-10"
        >
          <!-- Judul -->
          <div class="space-y-2.5">
            <label
              class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest"
              >Judul Quiz Utama</label
            >
            <div class="relative group">
              <span
                class="material-icons absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg group-focus-within:text-primary transition-colors"
                >edit_note</span
              >
              <input
                v-model="quiz.title"
                type="text"
                class="w-full pl-12 pr-4 py-4 sm:py-5 bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent focus:border-primary/20 rounded-[20px] sm:rounded-[24px] text-sm sm:text-lg font-bold text-slate-800 dark:text-white placeholder:text-slate-300 transition-all outline-none shadow-sm"
                placeholder="Masukkan judul quiz di sini..."
              />
            </div>
          </div>

          <!-- Configuration Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
            <!-- Left: Settings & Detail -->
            <div
              class="space-y-6 bg-slate-50/50 dark:bg-slate-800/20 p-5 sm:p-8 rounded-[32px] border border-slate-100 dark:border-white/5 flex flex-col"
            >
              <div class="flex items-center gap-2 mb-2">
                <span class="material-icons text-primary text-sm">tune</span>
                <span
                  class="text-[10px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest"
                  >Detail Utama</span
                >
              </div>

              <!-- Kategori -->
              <div class="space-y-2 relative">
                <label
                  class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest"
                  >Mata Pelajaran</label
                >
                <button
                  type="button"
                  @click.stop="
                    isCatDropdownOpen = !isCatDropdownOpen;
                    isTypeDropdownOpen = false;
                    isClassDropdownOpen = false;
                  "
                  class="w-full flex items-center justify-between px-5 py-4 bg-white dark:bg-slate-800 border-2 border-transparent hover:border-primary/30 rounded-2xl text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-slate-200 shadow-sm transition-all group/cat"
                  :class="{
                    'border-primary shadow-lg shadow-primary/5':
                      isCatDropdownOpen,
                  }"
                >
                  <div class="flex items-center gap-3">
                    <div
                      class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center"
                    >
                      <span class="material-icons text-sm">label</span>
                    </div>
                    <span class="truncate">{{
                      subjects.find((s) => s.id === quiz.subject_id)?.name ||
                      "Pilih Mata Pelajaran"
                    }}</span>
                  </div>
                  <span
                    class="material-icons text-slate-400 text-sm transition-transform"
                    :class="{ 'rotate-180': isCatDropdownOpen }"
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
                    v-if="isCatDropdownOpen"
                    class="absolute top-full left-0 w-full mt-2 bg-white/90 dark:bg-surface-dark/95 backdrop-blur-xl border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl py-2 z-[110] max-h-[220px] overflow-y-auto custom-scrollbar"
                  >
                    <div
                      v-for="sub in subjects"
                      :key="sub.id"
                      @click="
                        quiz.subject_id = sub.id;
                        isCatDropdownOpen = false;
                      "
                      :class="`px-5 py-3.5 text-[10px] font-black uppercase tracking-widest hover:bg-primary/5 hover:text-primary transition-all flex items-center gap-3 cursor-pointer border-b border-slate-50 dark:border-white/5 last:border-0 ${quiz.subject_id === sub.id ? 'text-primary bg-primary/5' : 'text-slate-500 hover:pl-6'}`"
                    >
                      <div
                        :class="`w-8 h-8 rounded-lg flex items-center justify-center ${quiz.subject_id === sub.id ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800'}`"
                      >
                        <span class="material-icons text-sm">label</span>
                      </div>
                      {{ sub.name }}
                    </div>
                  </div>
                </Transition>
              </div>

              <div class="flex-grow"></div>

              <div
                class="p-4 bg-primary/5 rounded-2xl border border-primary/10"
              >
                <div class="flex items-start gap-3">
                  <span class="material-icons text-primary text-lg">info</span>
                  <p
                    class="text-[10px] font-medium text-slate-500 leading-relaxed"
                  >
                    Pengaturan **Target Kelas** dan **Batas Waktu** dapat
                    diakses langsung melalui header (di samping tombol Publish).
                  </p>
                </div>
              </div>
            </div>

            <!-- Right: Description -->
            <div
              class="space-y-3 flex flex-col h-full bg-slate-50/50 dark:bg-slate-800/20 p-5 sm:p-8 rounded-[32px] border border-slate-100 dark:border-white/5"
            >
              <div class="flex items-center gap-2 mb-2">
                <span class="material-icons text-primary text-sm"
                  >description</span
                >
                <span
                  class="text-[10px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest"
                  >Deskripsi & Instruksi</span
                >
              </div>
              <textarea
                v-model="quiz.description"
                class="w-full px-6 py-5 bg-white dark:bg-slate-700 border-2 border-transparent focus:border-primary/20 rounded-2xl text-xs sm:text-base font-medium text-slate-600 dark:text-slate-300 placeholder:text-slate-300 transition-all outline-none resize-none flex-grow min-h-[300px] shadow-sm"
                placeholder="Tuliskan instruksi pengerjaan untuk siswa Anda..."
              ></textarea>
            </div>
          </div>

          <!-- Bottom Action -->
          <div class="pt-6">
            <button
              @click="addNewQuestion"
              class="w-full py-5 bg-primary/10 hover:bg-primary/20 text-primary font-black text-[11px] uppercase tracking-[0.2em] rounded-2xl transition-all flex items-center justify-center gap-3 active:scale-[0.98]"
            >
              <span class="material-icons text-sm">add_circle</span>
              Lanjut Tambahkan Soal Pertama
            </button>
          </div>
        </div>

        <div class="h-24 shrink-0"></div>
      </section>

      <!-- Empty State -->
      <section
        v-else
        class="flex-1 flex flex-col items-center justify-center text-slate-300 dark:text-slate-700"
      >
        <div
          class="w-24 h-24 rounded-full bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center mb-6"
        >
          <span class="material-icons text-6xl">edit_note</span>
        </div>
        <h3 class="text-xl font-bold dark:text-slate-600">
          No question selected
        </h3>
        <p class="text-sm mt-1">
          Select a question or create a new one to start editing.
        </p>
        <button
          @click="addNewQuestion"
          class="mt-8 px-6 py-2.5 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/30"
        >
          Create First Question
        </button>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import api from "../../configs/api.js";
import { useNotificationStore } from "../../stores/notification";

const router = useRouter();
const route = useRoute();
const notification = useNotificationStore();

const quiz = ref({ title: "", questions: [] });
const activeQuestionIdx = ref(-1);
const saving = ref(false);
const isQuestionListOpen = ref(false);
const isClassDropdownOpen = ref(false);
const isCatDropdownOpen = ref(false);
const isTypeDropdownOpen = ref(false);
const classrooms = ref([]);
const subjects = ref([]);

const activeQuestion = computed(
  () => quiz.value.questions[activeQuestionIdx.value],
);
const isPureMultipleChoice = computed(() => {
  if (!quiz.value.questions || quiz.value.questions.length === 0) return false;
  return quiz.value.questions.every((q) => q.type === "multiple_choice");
});
const hasShortAnswer = computed(() => {
  if (!quiz.value.questions || quiz.value.questions.length === 0) return false;
  return quiz.value.questions.some((q) => q.type === "short_answer");
});
const selectedClassroomName = computed(() => {
  if (!quiz.value.classroom_id) return "Public";
  const cls = classrooms.value.find((c) => c.id === quiz.value.classroom_id);
  return cls ? cls.name : "Public";
});

const durationMin = ref(0);
const durationSec = ref(0);
const ytStartMin = ref(0);
const ytStartSec = ref(0);
const ytEndMin = ref(0);
const ytEndSec = ref(0);
const fileInput = ref(null);
const uploading = ref(false);

const triggerUpload = () => fileInput.value?.click();

const getFullUrl = (url) => {
  if (!url) return "";
  if (url.startsWith("http")) return url;
  return `http://localhost:8000${url}`;
};

const mediaAccept = computed(() => {
  if (activeQuestion.value?.media_type === "image") return "image/*";
  if (activeQuestion.value?.media_type === "video") return "video/*";
  return "*";
});

const mediaIcon = computed(() => {
  if (activeQuestion.value?.media_type === "image") return "image";
  if (activeQuestion.value?.media_type === "video") return "videocam";
  if (activeQuestion.value?.media_type === "youtube") return "play_circle";
  return "add_photo_alternate";
});

const formatQuestionType = (type) => {
  const map = {
    multiple_choice: "Multiple Choice",
    true_false: "True / False",
    short_answer: "Short Answer",
  };
  return map[type] || type;
};

const getQuestionTypeIcon = (type) => {
  const map = {
    multiple_choice: "list",
    true_false: "check_circle",
    short_answer: "short_text",
  };
  return map[type] || "help";
};

const handleFileUpload = async (e) => {
  const file = e.target.files[0];
  if (!file) return;

  uploading.value = true;
  const formData = new FormData();
  formData.append("file", file);
  formData.append("type", activeQuestion.value.media_type);

  try {
    const token = localStorage.getItem("access_token");
    const res = await axios.post(
      "http://localhost:8000/api/guru/media/upload",
      formData,
      {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
        },
      },
    );
    activeQuestion.value.media_url = res.data.url;
  } catch (err) {
    console.error("Upload failed", err);
    notification.add({
      title: "Gagal!",
      message: "Gagal mengupload file media.",
      type: "error",
    });
  } finally {
    uploading.value = false;
  }
};

const updateDuration = () => {
  quiz.value.duration_seconds =
    parseInt(durationMin.value || 0) * 60 + parseInt(durationSec.value || 0);
};

const updateYTTimer = () => {
  if (!activeQuestion.value) return;
  activeQuestion.value.media_start =
    parseInt(ytStartMin.value || 0) * 60 + parseInt(ytStartSec.value || 0);
  activeQuestion.value.media_end =
    parseInt(ytEndMin.value || 0) * 60 + parseInt(ytEndSec.value || 0);
};

const syncYTLocalRefs = () => {
  if (!activeQuestion.value) return;
  const start = activeQuestion.value.media_start || 0;
  ytStartMin.value = Math.floor(start / 60);
  ytStartSec.value = start % 60;

  const end = activeQuestion.value.media_end || 0;
  ytEndMin.value = Math.floor(end / 60);
  ytEndSec.value = end % 60;
};


const addNewQuestion = () => {
  if (!quiz.value.questions) quiz.value.questions = [];
  quiz.value.questions.push({
    question_text: "",
    type: "multiple_choice",
    points: 5,
    media_type: "none",
    media_url: "",
    media_start: null,
    media_end: null,
    options: [
      { option_text: "", is_correct: true },
      { option_text: "", is_correct: false },
    ],
  });
  activeQuestionIdx.value = quiz.value.questions.length - 1;
  syncYTLocalRefs();
};

const redistributePoints = () => {
  if (!quiz.value.questions || quiz.value.questions.length === 0) return;

  const mcQuestions = quiz.value.questions.filter(
    (q) => q.type === "multiple_choice",
  );
  if (mcQuestions.length === 0) return;

  const count = mcQuestions.length;
  const basePoints = Math.floor(100 / count);
  const remainder = 100 % count;

  mcQuestions.forEach((q, idx) => {
    // Distribute remainder to the last MC questions
    q.points = idx >= count - remainder ? basePoints + 1 : basePoints;
  });
};

const deleteQuestion = (idx) => {
  quiz.value.questions.splice(idx, 1);
  if (activeQuestionIdx.value >= quiz.value.questions.length) {
    activeQuestionIdx.value = Math.max(0, quiz.value.questions.length - 1);
  }
  redistributePoints();
};

const setCorrectOption = (oIdx) => {
  activeQuestion.value.options.forEach((opt, i) => {
    opt.is_correct = i === oIdx;
  });
};

const saveQuiz = async () => {
  updateDuration();
  saving.value = true;

  // Clean payload
  const cleanQuiz = {
    ...quiz.value,
    duration_seconds:
      parseInt(durationMin.value || 0) * 60 + parseInt(durationSec.value || 0),
    questions: quiz.value.questions.map((q) => ({
      ...q,
      media_type: q.media_type || "none",
      options: q.type === "short_answer" ? [] : q.options,
    })),
  };

  try {
    const isEditing = !!quiz.value.id;
    let response;

    if (isEditing) {
      response = await api.put(`/guru/quizzes/${quiz.value.id}`, cleanQuiz);
    } else {
      response = await api.post('/guru/quizzes', cleanQuiz);
    }

    notification.add({
      title: "Berhasil!",
      message: isEditing 
        ? `Quiz "${quiz.value.title}" berhasil disimpan.` 
        : `Quiz "${quiz.value.title}" berhasil dibuat.`,
      type: "success",
    });

    // Navigate to Quiz Bank after saving
    router.push('/guru/quizzes');
  } catch (err) {
    console.error("Save failed", err);
    notification.add({
      title: "Gagal!",
      message: "Pastikan semua field soal terisi dengan benar.",
      type: "error",
    });
  } finally {
    saving.value = false;
  }
};

onMounted(async () => {
  try {
    const isEditing = !!route.params.id;

    if (isEditing) {
      // Fetch quiz, classrooms, and subjects in parallel
      const [qRes, clsRes, subRes] = await Promise.all([
        api.get(`/guru/quizzes/${route.params.id}`),
        api.get('/public/classrooms'), // Database-wide classrooms
        api.get('/guru/subjects') // Fetch all subjects
      ]);

      quiz.value = qRes.data;
      classrooms.value = clsRes.data;
      subjects.value = subRes.data;

      // Set local duration refs
      const totalSec = quiz.value.duration_seconds || quiz.value.duration_minutes * 60 || 0;
      durationMin.value = Math.floor(totalSec / 60);
      durationSec.value = totalSec % 60;
      quiz.value.duration_seconds = totalSec;
    } else {
      // Create mode: Fetch data but don't load a quiz
      const [clsRes, subRes] = await Promise.all([
        api.get('/public/classrooms'),
        api.get('/guru/subjects')
      ]);
      
      classrooms.value = clsRes.data;
      subjects.value = subRes.data;
      
      // Initialize fresh quiz
      quiz.value = {
        title: '',
        description: '',
        subject_id: subRes.data[0]?.id || null,
        category_id: null,
        classroom_id: null,
        duration_seconds: 1800,
        is_active: true,
        questions: []
      };
      durationMin.value = 30;
      durationSec.value = 0;
    }

    activeQuestionIdx.value = -1; // Show settings first
    syncYTLocalRefs();
  } catch (err) {
    console.error("Fetch failed", err);
  }

  window.addEventListener("click", () => {
    isClassDropdownOpen.value = false;
    isCatDropdownOpen.value = false;
    isTypeDropdownOpen.value = false;
  });
});

watch(activeQuestionIdx, () => {
  syncYTLocalRefs();
});

// Watch for changes in question count or types to redistribute points
watch(
  () => quiz.value.questions,
  () => {
    redistributePoints();
  },
  { deep: true },
);
</script>

<style scoped>
/* Hide number input spinners */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
input[type="number"] {
  -moz-appearance: textfield;
  appearance: textfield;
}

.scrollbar-thin::-webkit-scrollbar {
  width: 4px;
}
.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.dark .scrollbar-thin::-webkit-scrollbar-thumb {
  background: #1e293b;
}
.border-3 {
  border-width: 3px;
}
</style>
