<template>
  <header class="fixed top-0 left-0 right-0 z-[9000] transition-all duration-500"
          :class="[isScrolled ? 'h-16 glass shadow-sm' : 'h-20 bg-transparent']">
    <div class="max-w-7xl mx-auto px-6 h-full flex items-center justify-between">
      
      <!-- Logo -->
      <router-link to="/" class="flex items-center gap-3 transition-transform hover:scale-105">
        <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-blur-sm shadow-xl overflow-hidden border border-slate-100">
          <img src="@/assets/logo-premium.png" alt="Grand Palais Logo" class="w-full h-full object-contain" />
        </div>
        <span class="text-xl font-bold tracking-tight" 
              :class="isScrolled ? 'text-primary' : 'text-white'">
          Grand Palais
        </span>
      </router-link>

      <!-- Desktop Nav -->
      <nav class="hidden md:flex items-center gap-8">
        <router-link v-for="link in navLinks" :key="link.to" :to="link.to"
                     class="text-sm font-medium transition-all hover:text-accent"
                     :class="isScrolled ? 'text-slate-600' : 'text-white/90'">
          {{ link.label }}
        </router-link>
      </nav>

      <!-- Auth Actions -->
      <div class="flex items-center gap-6">
        <template v-if="isLoggedIn">
          <div class="relative" ref="dropdownRef">
            <button @click="dropdownOpen = !dropdownOpen" 
                    class="flex items-center gap-3 p-1 rounded-full transition-all"
                    :class="isScrolled ? 'hover:bg-slate-100' : 'hover:bg-white/10'">
              <img :src="avatarUrl" referrerpolicy="no-referrer" class="w-8 h-8 rounded-full object-cover border-2" 
                   :class="isScrolled ? 'border-primary/10' : 'border-white/20'" />
              <span class="hidden sm:block text-sm font-semibold"
                    :class="isScrolled ? 'text-primary' : 'text-white'">
                {{ firstName }}
              </span>
              <svg class="w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': dropdownOpen }"
                   :style="{ color: isScrolled ? '#64748b' : 'rgba(255,255,255,0.7)' }"
                   fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <Transition name="dropdown">
              <div v-if="dropdownOpen" 
                   class="absolute top-full right-0 mt-3 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-[10000]">
                <div class="p-5 bg-slate-50 border-bottom border-slate-100 flex items-center gap-4">
                  <img :src="avatarUrl" referrerpolicy="no-referrer" class="w-12 h-12 rounded-xl object-cover" />
                  <div class="overflow-hidden">
                    <p class="font-bold text-slate-900 truncate">{{ authStore.user?.name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ authStore.user?.email }}</p>
                  </div>
                </div>
                <div class="p-2">
                  <router-link v-for="item in dropdownItems" :key="item.to" :to="item.to"
                               @click="dropdownOpen=false"
                               class="flex items-center gap-3 p-3 rounded-xl text-sm text-slate-600 hover:bg-slate-50 hover:text-accent transition-all">
                    <span class="text-lg"><i :class="item.iconClass"></i></span> {{ item.label }}
                  </router-link>
                  <div v-if="isAdmin" class="mt-2 pt-2 border-t border-slate-50">
                    <router-link :to="adminLink" @click="dropdownOpen=false"
                                 class="flex items-center gap-3 p-3 rounded-xl text-sm text-primary font-bold hover:bg-slate-50 transition-all">
                      <span class="text-lg"><i class="las la-cog"></i></span> Administration
                    </router-link>
                  </div>
                </div>
                <div class="p-2 bg-slate-50">
                  <button @click="logout" 
                          class="w-full flex items-center gap-3 p-3 rounded-xl text-sm text-red-500 font-semibold hover:bg-red-50 transition-all">
                    <span class="text-lg"><i class="las la-sign-out-alt"></i></span> Se déconnecter
                  </button>
                </div>
              </div>
            </Transition>
          </div>
        </template>
        
        <template v-else>
          <router-link to="/login" class="text-sm font-semibold transition-all"
                       :class="isScrolled ? 'text-primary' : 'text-white'">
             Connexion
          </router-link>
          <router-link to="/register">
            <button class="btn-gold-premium !py-2 !px-6 rounded-full text-xs">S'inscrire</button>
          </router-link>
        </template>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore    = useAuthStore()
const router       = useRouter()
const route        = useRoute()
const scrolled     = ref(false)
const dropdownOpen = ref(false)
const dropdownRef  = ref(null)

const navLinks = [
  { to: '/',         label: 'Accueil' },
  { to: '/rooms',    label: 'Chambres' },
  { to: '/services', label: 'Services' },
  { to: '/about',    label: 'À propos' },
]

const dropdownItems = [
  { to: '/dashboard/reservations', label: 'Mes réservations', iconClass: 'las la-calendar-check' },
  { to: '/dashboard',             label: 'Mon profil',      iconClass: 'las la-user-circle' },
]

const heroRoutes = ['/']

const isScrolled = computed(() => {
  return scrolled.value || !heroRoutes.includes(route.path)
})

const isLoggedIn = computed(() => authStore.isLoggedIn)
const isAdmin    = computed(() =>
  authStore.user?.roles?.some(r => ['admin','manager','receptionist'].includes(r.name))
)
const adminLink = computed(() => {
  const role = authStore.user?.roles?.[0]?.name
  if (role === 'manager')      return '/manager'
  if (role === 'receptionist') return '/reception'
  return '/admin'
})
const firstName = computed(() => (authStore.user?.name || '').split(' ')[0])
const avatarUrl = computed(() => {
  if (authStore.user?.avatar) {
    if (authStore.user.avatar.startsWith('http')) {
      return authStore.user.avatar
    }
    return `http://localhost:8000/storage/${authStore.user.avatar}`
  }
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(authStore.user?.name||'U')}&background=0f172a&color=fff&size=100`
})

const handleScroll = () => { scrolled.value = window.scrollY > 44 }
const handleOut    = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target))
    dropdownOpen.value = false
}

watch(() => route.path, () => {
  scrolled.value = false
  setTimeout(() => { scrolled.value = window.scrollY > 44 }, 80)
})

const logout = async () => {
  dropdownOpen.value = false
  await authStore.logout()
  router.push('/')
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  document.addEventListener('mousedown', handleOut)
})
onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  document.removeEventListener('mousedown', handleOut)
})
</script>

<style scoped>
.dropdown-enter-active, .dropdown-leave-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.dropdown-enter-from, .dropdown-leave-to { opacity: 0; transform: translateY(-10px) scale(0.95); }
</style>
