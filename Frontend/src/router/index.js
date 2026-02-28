import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'register',
      component: RegisterView
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView
    },
    // Admin Routes
    {
      path: '/admin',
      component: () => import('../layouts/AdminLayout.vue'),
      children: [
        {
          path: 'dashboard',
          component: () => import('../views/admin/Dashboard.vue')
        },
        {
          path: 'classrooms',
          component: () => import('../views/admin/ClassroomManagement.vue')
        },
        {
          path: 'classrooms/add',
          component: () => import('../views/admin/AddClassroom.vue')
        },
        {
          path: 'classrooms/edit/:id',
          component: () => import('../views/admin/AddClassroom.vue')
        },
        {
          path: 'classrooms/:id',
          name: 'admin.classrooms.detail',
          component: () => import('../views/admin/ClassroomDetail.vue')
        },
        {
          path: 'teachers',
          component: () => import('../views/admin/Teachers.vue')
        },
        {
          path: 'teachers/add',
          component: () => import('../views/admin/AddTeacher.vue')
        },
        {
          path: 'teachers/edit/:id',
          component: () => import('../views/admin/AddTeacher.vue')
        },
        {
          path: 'students',
          component: () => import('../views/admin/Students.vue')
        },
        {
          path: 'students/add',
          component: () => import('../views/admin/AddStudent.vue')
        },
        {
          path: 'students/edit/:id',
          component: () => import('../views/admin/AddStudent.vue')
        },
        {
          path: 'settings',
          component: () => import('../views/admin/Settings.vue')
        }
      ]
    },
    // Guru Routes
    {
      path: '/guru',
      component: () => import('../layouts/GuruLayout.vue'),
      children: [
        {
          path: 'dashboard',
          component: () => import('../views/guru/Dashboard.vue')
        },
        {
          path: 'quizzes',
          component: () => import('../views/guru/Quizzes.vue')
        },
        {
          path: 'quizzes/create',
          component: () => import('../views/guru/QuizEditor.vue')
        },
        {
          path: 'quizzes/:id/edit',
          component: () => import('../views/guru/QuizEditor.vue')
        },

        {
          path: 'jadwal',
          component: () => import('../views/guru/Jadwal.vue')
        },
        {
          path: 'jadwal/add',
          component: () => import('../views/guru/AddJadwal.vue')
        },
        {
          path: 'jadwal/edit/:id',
          component: () => import('../views/guru/AddJadwal.vue')
        },
        {
          path: 'absen',
          component: () => import('../views/guru/Absen.vue')
        },
        {
          path: 'nilai',
          component: () => import('../views/guru/Nilai.vue')
        },
        {
          path: 'activity-logs',
          component: () => import('../views/guru/ActivityLogs.vue')
        }
      ]
    },
    // Siswa Routes
    {
      path: '/siswa',
      component: () => import('../layouts/SiswaLayout.vue'),
      children: [
        {
          path: 'quizzes',
          component: () => import('../views/siswa/Quizzes.vue')
        },
        {
          path: 'attempts',
          component: () => import('../views/siswa/Attempts.vue')
        },
        {
          path: 'attempts/:id',
          component: () => import('../views/siswa/AttemptDetail.vue')
        },
        {
          path: 'quiz/:id',
          component: () => import('../views/siswa/TakeQuiz.vue')
        },
        {
          path: 'profile',
          component: () => import('../views/siswa/ProfileView.vue')
        }
      ]
    }
  ]
})

export default router
