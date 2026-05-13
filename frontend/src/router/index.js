import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  { path: '/',         name: 'home',     component: () => import('@/views/HomeView.vue') },
  { path: '/rooms',    name: 'rooms',    component: () => import('@/views/RoomsView.vue') },
  { path: '/rooms/:id',name: 'room-detail', component: () => import('@/views/RoomDetailView.vue') },
  { path: '/booking',  name: 'booking',  component: () => import('@/views/BookingView.vue'),     meta: { requiresAuth: true } },
  { path: '/booking/confirm/:id', name: 'booking-confirm', component: () => import('@/views/BookingConfirmView.vue'), meta: { requiresAuth: true } },
  { path: '/login',    name: 'login',    component: () => import('@/views/auth/LoginView.vue'),  meta: { guestOnly: true } },
  { path: '/register', name: 'register', component: () => import('@/views/auth/RegisterView.vue'), meta: { guestOnly: true } },
  { path: '/forgot-password', name: 'forgot-password', component: () => import('@/views/auth/ForgotPasswordView.vue'), meta: { guestOnly: true } },
  { path: '/reset-password',  name: 'reset-password',  component: () => import('@/views/auth/ResetPasswordView.vue'),  meta: { guestOnly: true } },
  { path: '/dashboard',name: 'dashboard',component: () => import('@/views/dashboard/ProfileView.vue'), meta: { requiresAuth: true } },
  { path: '/dashboard/reservations', name: 'my-reservations', component: () => import('@/views/dashboard/MyReservationsView.vue'), meta: { requiresAuth: true } },
  { path: '/admin',    name: 'admin-dashboard', component: () => import('@/views/admin/AdminDashboardView.vue'), meta: { requiresAuth: true } },
  { path: '/admin/reservations', name: 'admin-reservations', component: () => import('@/views/admin/AdminReservationsView.vue'), meta: { requiresAuth: true } },
  { path: '/admin/rooms',        name: 'admin-rooms',        component: () => import('@/views/admin/AdminRoomsView.vue'),        meta: { requiresAuth: true } },
  { path: '/admin/users',        name: 'admin-users',        component: () => import('@/views/admin/AdminUsersView.vue'),        meta: { requiresAuth: true } },
  {
  path: '/verify-email',
  name: 'verify-email-prompt',
  component: () => import('../views/auth/VerifyEmailPromptView.vue'),
  meta: { requiresAuth: true }
  },
  {
  path: '/email-verified',
  name: 'email-verified',
  component: () => import('../views/auth/EmailVerifiedView.vue'),
  },
  {
  path: '/booking/result',
  name: 'booking-result',
  component: () => import('../views/BookingResultView.vue'),
  },
  {
  path: '/booking/confirm-arrival/:id',
  name: 'booking-confirm-arrival',
  component: () => import('../views/BookingResultView.vue'),
  },
  {
  path: '/auth/callback',
  name: 'auth-callback',
  component: () => import('../views/auth/AuthCallbackView.vue'),
  },
  {
  path: '/complete-profile',
  name: 'complete-profile',
  component: () => import('../views/auth/CompleteProfileView.vue'),
  meta: { requiresAuth: true }
},
{
  path: '/manager',
  name: 'manager-dashboard',
  component: () => import('../views/admin/ManagerDashboardView.vue'),
  meta: { requiresAuth: true }
},
{
  path: '/reception',
  name: 'reception-dashboard',
  component: () => import('../views/admin/ReceptionDashboardView.vue'),
  meta: { requiresAuth: true }
},
{
  path: '/admin/users/create',
  name: 'admin-create-user',
  component: () => import('../views/admin/AdminCreateUserView.vue'),
  meta: { requiresAuth: true }
},
{
  path: '/services',
  name: 'services',
  component: () => import('../views/ServicesView.vue'),
},
{
  path: '/about',
  name: 'about',
  component: () => import('../views/AboutView.vue'),
},
{
  path: '/admin/services',
  name: 'admin-services',
  component: () => import('../views/admin/AdminServicesView.vue'),
  meta: { requiresAuth: true }
},
{
  path: '/admin/promotions',
  name: 'admin-promotions',
  component: () => import('../views/admin/AdminPromotionsView.vue'),
  meta: { requiresAuth: true }
},
{
  path: '/admin/announcements',
  name: 'admin-announcements',
  component: () => import('../views/admin/AdminAnnouncementsView.vue'),
  meta: { requiresAuth: true }
},
  {
    path: '/admin/analytics',
    name: 'admin-analytics',
    component: () => import('../views/admin/AdminAnalyticsView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/profile',
    name: 'admin-profile',
    component: () => import('../views/admin/AdminProfileView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/check-in/:reference',
    name: 'check-in',
    component: () => import('../views/CheckInView.vue'),
},
{
    path: '/account-deleted',
    name: 'account-deleted',
    component: () => import('../views/auth/AccountDeletedView.vue'),
}
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
})

// ⚠️ Le store doit être importé DANS le guard, pas en dehors
router.beforeEach(async (to, from, next) => {
  const { useAuthStore } = await import('@/stores/auth')
  const auth = useAuthStore()

  if (!auth.user && auth.token) {
    await auth.fetchUser().catch(() => {})
  }

  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    return next({ name: 'login', query: { redirect: to.fullPath } })
  }

  if (to.meta.guestOnly && auth.isLoggedIn) {
    return next({ name: 'home' })
  }

  // ─── Redirection automatique selon le rôle ───────────────
  if (to.name === 'admin-dashboard' && auth.isLoggedIn) {
    const role = auth.user?.roles?.[0]?.name
    if (role === 'manager')      return next({ name: 'manager-dashboard' })
    if (role === 'receptionist') return next({ name: 'reception-dashboard' })
    if (role === 'client')       return next({ name: 'home' })
    // admin → continue vers /admin
  }

  // ─── Protection des routes par rôle ──────────────────────
  if (to.name === 'manager-dashboard') {
    const role = auth.user?.roles?.[0]?.name
    if (!['admin', 'manager'].includes(role)) return next({ name: 'home' })
  }

  if (to.name === 'reception-dashboard') {
    const role = auth.user?.roles?.[0]?.name
    if (!['admin', 'manager', 'receptionist'].includes(role)) return next({ name: 'home' })
  }

  if (to.path.startsWith('/admin') && auth.isLoggedIn) {
  const role = auth.user?.roles?.[0]?.name
  // Seuls les clients ne peuvent pas accéder aux pages admin
  if (role === 'client') return next({ name: 'home' })
  // admin, manager, receptionist → autorisés sur /admin/*
}

  next()
})

export default router
