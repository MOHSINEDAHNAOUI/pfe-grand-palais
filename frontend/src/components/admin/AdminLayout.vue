<template>
  <div class="min-h-screen flex font-sans" style="background: linear-gradient(135deg, #F8FAF1 0%, #F1F4F9 100%);">

    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 h-full w-72 z-50 flex flex-col bg-white border-r border-slate-100">
      
      <!-- Brand -->
      <div class="px-8 py-12">
        <h1 class="text-xl font-serif tracking-tight text-slate-900">
          Grand Palais <span class="text-slate-400 font-light">Management</span>
        </h1>
        <div class="mt-4 flex items-center gap-2">
          <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
          <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ roleLabel }}</span>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 px-4 py-8 space-y-1 overflow-y-auto no-scrollbar">
        <router-link
          v-for="item in navItems" :key="item.to" :to="item.to"
          class="flex items-center gap-4 px-4 py-3 text-[13px] transition-all duration-300 rounded-xl group"
          :class="isActive(item.to)
            ? 'bg-slate-50 text-slate-900 font-bold'
            : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50/50'"
        >
          <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-colors"
               :class="isActive(item.to) ? 'text-primary' : 'text-slate-400 group-hover:text-slate-600'">
            <i :class="[item.icon, 'text-xl']"></i>
          </div>
          <span class="tracking-tight">{{ item.label }}</span>
          <div v-if="isActive(item.to)" class="ml-auto w-1 h-4 bg-primary rounded-full"></div>
        </router-link>
      </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col" style="margin-left: 18rem;">

      <!-- Header -->
      <header class="bg-white/80 backdrop-blur-md sticky top-0 z-40 px-10 py-5 flex items-center justify-between border-b border-slate-100">
        <div>
          <h1 class="text-2xl font-serif text-primary tracking-tight">{{ pageTitle }}</h1>
          <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mt-1">{{ today }}</p>
        </div>

        <!-- Profile Dropdown -->
        <div class="relative" ref="dropdownRef">
          <div @click="isDropdownOpen = !isDropdownOpen"
            class="flex items-center gap-4 cursor-pointer p-1.5 pr-5 rounded-2xl bg-white/50 border border-slate-100 shadow-sm hover:shadow-md hover:bg-white transition-all group select-none">
            <div class="relative">
              <img :src="avatarUrl" class="w-10 h-10 rounded-xl object-cover border border-slate-100 shadow-sm group-hover:scale-105 transition-transform" />
              <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></div>
            </div>
            <div class="text-right">
              <p class="text-xs font-bold text-slate-900">{{ authStore.user?.name }}</p>
              <p class="text-[8px] uppercase font-black tracking-widest text-slate-400">{{ roleLabel }}</p>
            </div>
            <i class="las la-angle-down text-slate-300 group-hover:text-primary transition-all ml-2"
               :class="isDropdownOpen ? 'rotate-180 !text-primary' : ''"></i>
          </div>

          <!-- Dropdown Menu -->
          <Transition name="dropdown">
            <div v-if="isDropdownOpen"
              class="absolute right-0 top-[calc(100%+12px)] w-64 bg-white rounded-[1.5rem] shadow-2xl border border-slate-100 overflow-hidden z-50 py-2">
              
              <!-- User recap -->
              <div class="px-5 py-4 border-b border-slate-50">
                <div class="flex items-center gap-3">
                  <img :src="avatarUrl" class="w-10 h-10 rounded-xl object-cover border border-slate-100" />
                  <div class="overflow-hidden">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ authStore.user?.name }}</p>
                    <p class="text-[9px] text-slate-400 truncate">{{ authStore.user?.email }}</p>
                  </div>
                </div>
              </div>

              <div class="p-2">
                <!-- Profile link -->
                <router-link to="/admin/profile" @click="isDropdownOpen = false"
                  class="flex items-center gap-3 px-4 py-3.5 rounded-xl hover:bg-slate-50 transition-all group/item">
                  <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 group-hover/item:bg-primary group-hover/item:text-white transition-all">
                    <i class="las la-user text-lg"></i>
                  </div>
                  <div>
                    <p class="text-xs font-bold text-slate-800 group-hover/item:text-primary transition-colors">Mon Profil</p>
                    <p class="text-[9px] text-slate-400">Modifier mes informations</p>
                  </div>
                </router-link>

                <div class="h-px bg-slate-50 my-1 mx-2"></div>

                <!-- Logout -->
                <button @click="handleLogout"
                  class="flex items-center gap-3 px-4 py-3.5 rounded-xl hover:bg-rose-50 transition-all w-full text-left group/item">
                  <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 group-hover/item:bg-rose-500 group-hover/item:text-white transition-all">
                    <i class="las la-sign-out-alt text-lg"></i>
                  </div>
                  <div>
                    <p class="text-xs font-bold text-slate-800 group-hover/item:text-rose-600 transition-colors">Déconnexion</p>
                    <p class="text-[9px] text-slate-400">Quitter la session</p>
                  </div>
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-10 max-w-[1400px] mx-auto w-full">
        <slot />
      </main>
    </div>

    <!-- Welcome Toast -->
    <Transition name="welcome-large">
      <div v-if="showWelcome" class="fixed bottom-10 right-10 z-[200] bg-white rounded-[2rem] shadow-2xl border border-slate-100 p-8 max-w-sm">
        <div class="flex items-center gap-4 mb-4">
          <img :src="avatarUrl" class="w-14 h-14 rounded-2xl object-cover border-4 border-white shadow-lg" />
          <div>
            <p class="text-[10px] font-black text-accent uppercase tracking-widest mb-1">Bienvenue</p>
            <h3 class="text-xl font-serif text-primary leading-tight">{{ authStore.user?.name }}</h3>
          </div>
        </div>
        <div class="h-1 w-full bg-slate-50 rounded-full overflow-hidden mt-4">
          <div class="h-full bg-primary rounded-full animate-progress"></div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

@keyframes progress {
  from { width: 0%; }
  to   { width: 100%; }
}
.animate-progress { animation: progress 5s linear forwards; }

/* Welcome toast */
.welcome-large-enter-active { transition: all 1.2s cubic-bezier(0.16, 1, 0.3, 1); }
.welcome-large-leave-active { transition: all 0.5s ease; }
.welcome-large-enter-from { opacity: 0; transform: translateY(40px) scale(0.9); }
.welcome-large-leave-to   { opacity: 0; transform: translateY(20px); }

/* Dropdown animation */
.dropdown-enter-active { transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
.dropdown-leave-active { transition: all 0.15s ease; }
.dropdown-enter-from   { opacity: 0; transform: translateY(-8px) scale(0.97); }
.dropdown-leave-to     { opacity: 0; transform: translateY(-4px) scale(0.98); }
</style>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'

const showWelcome = ref(false)

defineProps({
  pageTitle: { type: String, default: 'Dashboard' },
})

const authStore = useAuthStore()
const route     = useRoute()
const router    = useRouter()

const userRole = computed(() => authStore.user?.roles?.[0]?.name || 'client')

const today = computed(() =>
  new Intl.DateTimeFormat('fr-FR', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
  }).format(new Date())
)

const roleLabel = computed(() => ({
  admin:        'Directeur Général',
  manager:      'Manager d\'Hôtel',
  receptionist: 'Chef de Réception',
}[userRole.value] ?? 'Administration'))

const avatarUrl = computed(() => {
  if (authStore.user?.avatar) {
    if (authStore.user.avatar.startsWith('http')) return authStore.user.avatar
    return `http://localhost:8000/storage/${authStore.user.avatar}`
  }
  const name = encodeURIComponent(authStore.user?.name || 'A')
  return `https://ui-avatars.com/api/?name=${name}&background=b45309&color=fff&size=100&bold=true`
})

// ── Dropdown ─────────────────────────────────────────────
const isDropdownOpen = ref(false)
const dropdownRef    = ref(null)

const closeOnOutsideClick = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isDropdownOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeOnOutsideClick)
  if (route.query.welcome === 'true') {
    showWelcome.value = true
    setTimeout(() => { showWelcome.value = false }, 5000)
  }
})
onUnmounted(() => document.removeEventListener('click', closeOnOutsideClick))

const handleLogout = async () => {
  isDropdownOpen.value = false
  await api.post('/auth/logout').catch(() => {})
  authStore.logout()
  router.push({ name: 'login' })
}

// ── Navigation ───────────────────────────────────────────
const navItems = computed(() => {
  const role = userRole.value
  const dashboard    = { to: role === 'manager' ? '/manager' : role === 'receptionist' ? '/reception' : '/admin', icon: 'las la-tachometer-alt',  label: 'Tableau de Bord' }
  const reservations = { to: '/admin/reservations', icon: 'las la-calendar-check',  label: 'Réservations' }
  const rooms        = { to: '/admin/rooms',         icon: 'las la-door-open',       label: 'Gestion des Chambres' }
  const users        = { to: '/admin/users',         icon: 'las la-users',           label: 'Base Clients' }
  const services     = { to: '/admin/services',      icon: 'las la-concierge-bell',  label: 'Conciergerie & Services' }
  const promotions   = { to: '/admin/promotions',    icon: 'las la-percentage',      label: 'Promotions & Conventions' }
  const announcements = { to: '/admin/announcements', icon: 'las la-bullhorn',        label: 'Annonces & Événements' }
  const analytics    = { to: '/admin/analytics',     icon: 'las la-chart-pie',       label: 'Analyses & Rapports' }

  if (role === 'admin' || role === 'manager') return [dashboard, reservations, rooms, users, services, promotions, announcements, analytics]
  if (role === 'receptionist')                return [dashboard, reservations, rooms]
  return [dashboard]
})

const isActive = (path) => {
  if (path === '/admin' || path === '/manager' || path === '/reception') return route.path === path
  return route.path.startsWith(path)
}
</script>
