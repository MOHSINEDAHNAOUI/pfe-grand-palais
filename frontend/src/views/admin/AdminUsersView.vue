<template>
  <AdminLayout page-title="Utilisateurs" @logout="logout">

    <!-- Simple Filters & Actions -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mb-8 flex flex-wrap items-center gap-4 animate-fade-in">
      <div class="flex-1 min-w-[300px] relative">
        <i class="las la-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
        <input v-model="search" type="text" placeholder="Rechercher par nom, email..." 
               class="w-full pl-14 pr-6 py-4 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/10 outline-none transition-all" />
      </div>
      
      <select v-model="filterRole" 
              class="px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm outline-none cursor-pointer focus:ring-2 focus:ring-primary/10">
        <option value="">Tous les rôles</option>
        <option value="admin">Administrateur</option>
        <option value="manager">Manager</option>
        <option value="receptionist">Réceptionniste</option>
        <option value="client">Client</option>
      </select>

      <select v-model="filterStatus" 
              class="px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm outline-none cursor-pointer focus:ring-2 focus:ring-primary/10">
        <option value="">Tous les statuts</option>
        <option value="active">Actif</option>
        <option value="inactive">Suspendu</option>
      </select>
      
      <button @click="$router.push('/admin/users/create')" class="px-6 py-4 bg-primary text-white rounded-2xl hover:bg-accent transition-all text-xs font-bold flex items-center gap-2 uppercase tracking-widest">
        <i class="las la-plus text-lg"></i> Nouvel Utilisateur
      </button>
    </div>

    <!-- Main Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden animate-fade-in" style="animation-delay: 0.1s">
      <div v-if="loading" class="py-32 text-center text-slate-400">
        <div class="w-10 h-10 border-4 border-accent border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-xs font-bold uppercase tracking-widest">Chargement des données...</p>
      </div>

      <div v-else-if="!filteredUsers.length" class="py-32 text-center text-slate-300">
        <i class="las la-users text-5xl mb-4 opacity-20"></i>
        <p class="italic">Aucun utilisateur trouvé</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50 border-b border-slate-50">
              <th class="py-5 px-8">Utilisateur</th>
              <th class="py-5 px-8">Rôle</th>
              <th class="py-5 px-8">Contact</th>
              <th class="py-5 px-8">Inscription</th>
              <th class="py-5 px-8">Statut</th>
              <th class="py-5 px-8 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-slate-50/50 transition-colors">
              <td class="py-5 px-8">
                <div class="flex items-center gap-4">
                  <img :src="user.avatar ? `http://localhost:8000/storage/${user.avatar}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&size=128&background=f8fafc&color=0f172a&bold=true&font-size=0.33`" 
                       class="w-10 h-10 rounded-xl object-cover border border-slate-100" />
                  <div>
                    <p class="font-bold text-primary text-sm">{{ user.name }}</p>
                    <p class="text-[10px] text-slate-400">{{ user.email }}</p>
                  </div>
                </div>
              </td>
              <td class="py-5 px-8">
                <span :class="roleClass(user.roles?.[0]?.name)" class="text-[9px] font-bold uppercase tracking-widest px-3 py-1 rounded-full border">
                  {{ user.roles?.[0]?.name ?? 'client' }}
                </span>
              </td>
              <td class="py-5 px-8">
                <p class="font-bold text-primary text-sm">{{ user.phone ?? '—' }}</p>
                <p class="text-[10px] text-slate-400">{{ user.city ?? '—' }}</p>
              </td>
              <td class="py-5 px-8 text-[11px] font-bold text-slate-500">
                {{ formatDate(user.created_at) }}
              </td>
              <td class="py-5 px-8">
                <span class="text-[9px] font-bold uppercase tracking-widest px-3 py-1 rounded-full border"
                      :class="user.is_active ? 'border-emerald-100 text-emerald-600 bg-emerald-50' : 'border-rose-100 text-rose-600 bg-rose-50'">
                  {{ user.is_active ? 'Actif' : 'Suspendu' }}
                </span>
              </td>
              <td class="py-5 px-8 text-right">
                <div class="flex justify-end gap-2">
                  <button @click="toggleStatus(user)"
                          class="p-2 rounded-xl transition-all"
                          :class="user.is_active ? 'bg-rose-50 text-rose-500 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-500 hover:bg-emerald-100'"
                          :title="user.is_active ? 'Suspendre' : 'Réactiver'">
                    <i :class="user.is_active ? 'las la-user-lock' : 'las la-user-check'"></i>
                  </button>
                  <button @click="viewUser(user)" class="p-2 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200" title="Détails">
                    <i class="las la-eye"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Simple Pagination -->
      <div v-if="meta?.last_page > 1" class="p-6 bg-slate-50/50 flex items-center justify-between border-t border-slate-50">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
          Page {{ meta.current_page }} sur {{ meta.last_page }} <span class="mx-2">|</span> {{ meta.total }} Résidents
        </p>
        <div class="flex gap-2">
          <button @click="currentPage--" :disabled="currentPage === 1" class="px-4 py-2 bg-white border border-slate-100 rounded-xl text-xs disabled:opacity-50 hover:bg-slate-50">Précédent</button>
          <button @click="currentPage++" :disabled="currentPage === meta.last_page" class="px-4 py-2 bg-white border border-slate-100 rounded-xl text-xs disabled:opacity-50 hover:bg-slate-50">Suivant</button>
        </div>
      </div>
    </div>

    <!-- Profile Detail Modal -->
    <Transition name="modal">
      <div v-if="selectedUser"
           class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 backdrop-blur-xl bg-primary/40"
           @click.self="selectedUser = null">
        <div class="bg-slate-50 max-w-4xl w-full rounded-[3rem] shadow-2xl overflow-hidden relative border border-slate-200/50 animate-slide-up flex flex-col max-h-[95vh]">
          
          <!-- Header Bar -->
          <div class="flex items-center justify-between px-8 py-6 bg-white border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-4">
              <span class="w-10 h-10 rounded-xl bg-primary/5 flex items-center justify-center text-primary">
                <i class="las la-id-card text-xl"></i>
              </span>
              <div>
                <h3 class="text-xl font-serif text-primary leading-none">Dossier Résident</h3>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">ID #{{ String(selectedUser.id).padStart(4, '0') }}</p>
              </div>
            </div>
            <button @click="selectedUser = null"
                    class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300">
              <i class="las la-times text-2xl"></i>
            </button>
          </div>

          <!-- Scrollable Content -->
          <div class="overflow-y-auto p-6 sm:p-10 custom-scrollbar flex-1">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
              
              <!-- Left Column: Identity -->
              <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-xl shadow-primary/5 text-center relative overflow-hidden group">
                  <div class="absolute inset-0 bg-gradient-to-b from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                  
                  <div class="relative inline-block mb-6">
                    <img :src="selectedUser.avatar
                           ? `http://localhost:8000/storage/${selectedUser.avatar}`
                           : `https://ui-avatars.com/api/?name=${encodeURIComponent(selectedUser.name)}&size=256&background=f8fafc&color=0f172a&bold=true&font-size=0.33`"
                         class="w-32 h-32 rounded-full object-cover shadow-2xl ring-8 ring-slate-50 mx-auto group-hover:scale-105 transition-transform duration-500" />
                    <div class="absolute bottom-1 right-1 w-6 h-6 rounded-full border-4 border-white shadow-lg"
                         :class="selectedUser.is_active ? 'bg-emerald-500' : 'bg-rose-500'"></div>
                  </div>
                  
                  <h4 class="text-2xl font-serif text-primary mb-2">{{ selectedUser.name }}</h4>
                  <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-full text-[9px] font-bold uppercase tracking-widest mb-6"
                        :class="roleClass(selectedUser.roles?.[0]?.name)">
                    {{ selectedUser.roles?.[0]?.name ?? 'client' }}
                  </span>
                  
                  <div class="space-y-4 text-left">
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                      <i class="las la-envelope text-xl text-accent"></i>
                      <div class="overflow-hidden">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Email</p>
                        <p class="text-sm font-medium text-primary truncate">{{ selectedUser.email }}</p>
                      </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                      <i class="las la-phone text-xl text-accent"></i>
                      <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Téléphone</p>
                        <p class="text-sm font-medium text-primary">{{ selectedUser.phone ?? '—' }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right Column: Details & Actions -->
              <div class="lg:col-span-2 space-y-8">
                
                <!-- Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                  <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-primary/5 text-primary flex items-center justify-center text-2xl">
                      <i class="las la-map-marker"></i>
                    </div>
                    <div>
                      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Résidence</p>
                      <p class="text-base font-bold text-primary">{{ selectedUser.city ?? 'Non renseignée' }}</p>
                    </div>
                  </div>
                  <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-primary/5 text-primary flex items-center justify-center text-2xl">
                      <i class="las la-calendar-check"></i>
                    </div>
                    <div>
                      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Membre Depuis</p>
                      <p class="text-base font-bold text-primary">{{ formatDate(selectedUser.created_at) }}</p>
                    </div>
                  </div>
                </div>

                <!-- Admin Controls -->
                <div class="bg-primary rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
                  <div class="absolute -right-20 -top-20 w-64 h-64 bg-accent/10 rounded-full blur-3xl"></div>
                  
                  <div class="relative z-10 flex flex-col sm:flex-row items-center gap-6">
                    <div class="flex-1 w-full">
                      <p class="text-[10px] font-bold text-accent uppercase tracking-[0.3em] mb-4">Habilitation du Profil</p>
                      <div class="flex gap-3">
                        <div class="relative flex-1">
                          <select v-model="newRole"
                                  class="w-full appearance-none pl-6 pr-12 py-4 bg-white/10 border border-white/20 rounded-2xl text-sm font-bold text-white outline-none focus:border-accent transition-all cursor-pointer">
                            <option value="admin" class="text-primary">Administrateur</option>
                            <option value="manager" class="text-primary">Manager du Domaine</option>
                            <option value="receptionist" class="text-primary">Service Réception</option>
                            <option value="client" class="text-primary">Client Privilégié</option>
                          </select>
                          <i class="las la-angle-down absolute right-4 top-1/2 -translate-y-1/2 text-white pointer-events-none"></i>
                        </div>
                        <button @click="updateRole"
                                class="px-6 py-4 bg-accent text-white rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-white hover:text-primary transition-all shadow-xl active:scale-95">
                          Valider
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="mt-8 pt-8 border-t border-white/10 relative z-10 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div>
                      <p class="text-white font-bold text-sm mb-1">Statut d'accès au système</p>
                      <p class="text-[10px] text-slate-300">Verrouillez ou déverrouillez le compte immédiatement.</p>
                    </div>
                    <button @click="toggleStatus(selectedUser)"
                            class="px-8 py-4 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all active:scale-95 flex items-center gap-3 shrink-0"
                            :class="selectedUser.is_active 
                                    ? 'bg-rose-500/20 text-rose-300 hover:bg-rose-500 hover:text-white' 
                                    : 'bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500 hover:text-white'">
                      <i :class="selectedUser.is_active ? 'las la-lock' : 'las la-unlock'" class="text-xl"></i>
                      {{ selectedUser.is_active ? 'Suspendre l\'accès' : 'Rétabir l\'accès' }}
                    </button>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>

  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminLayout from '@/components/admin/AdminLayout.vue'
import api from '@/api/axios'

const router    = useRouter()
const authStore = useAuthStore()

const users        = ref([])
const loading      = ref(false)
const meta         = ref({ current_page: 1, last_page: 1, total: 0 })
const currentPage  = ref(1)
const search       = ref('')
const filterRole   = ref('')
const filterStatus = ref('')
const selectedUser = ref(null)
const newRole      = ref('client')

const headers = ['Utilisateur', 'Privilège', 'Contact', 'Inscription', 'Activité', 'Statut', 'Actions']

const hiddenEmails = ['quiwamiilyas314@gmail.com', 'quiwamiilyas82@gmail.com'];

const filteredUsers = computed(() => {
  return users.value.filter(u => {
    if (hiddenEmails.includes(u.email)) return false;

    if (search.value) {
      const q = search.value.toLowerCase()
      if (!u.name.toLowerCase().includes(q) && !u.email.toLowerCase().includes(q)) return false
    }
    if (filterRole.value && u.roles?.[0]?.name !== filterRole.value) return false
    if (filterStatus.value === 'active' && !u.is_active) return false
    if (filterStatus.value === 'inactive' && u.is_active) return false
    return true
  })
})

const userStats = computed(() => {
  const visibleUsers = users.value.filter(u => !hiddenEmails.includes(u.email));
  return [
  { label: 'Total',          value: visibleUsers.length,                                                         color: '#0f172a', iconClass: 'las la-users' },
  { label: 'Privilégiés',    value: visibleUsers.filter(u => u.roles?.[0]?.name === 'client').length,            color: '#b45309', iconClass: 'las la-crown' },
  { label: 'Personnel',      value: visibleUsers.filter(u => ['admin','manager','receptionist'].includes(u.roles?.[0]?.name)).length, color: '#3B82F6', iconClass: 'las la-user-tie' },
  { label: 'En ligne',       value: visibleUsers.filter(u => u.is_active).length,                                color: '#10B981', iconClass: 'las la-globe' },
]})

const loadUsers = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/users', {
      params: { page: currentPage.value }
    })
    users.value = data.data ?? data
    meta.value = data.meta ?? {
      current_page: data.current_page ?? 1,
      last_page:    data.last_page    ?? 1,
      total:        data.total        ?? users.value.length,
    }
  } catch (e) {
    console.error("Erreur users", e)
  } finally {
    loading.value = false
  }
}

const viewUser = async (user) => {
  try {
    const { data } = await api.get(`/admin/users/${user.id}`)
    selectedUser.value = data
    newRole.value = data.roles?.[0]?.name ?? 'client'
  } catch {
    selectedUser.value = { ...user }
    newRole.value = user.roles?.[0]?.name ?? 'client'
  }
}

const toggleStatus = async (user) => {
  try {
    await api.patch(`/admin/users/${user.id}/toggle-status`)
    user.is_active = !user.is_active
    if (selectedUser.value?.id === user.id) {
      selectedUser.value.is_active = user.is_active
    }
  } catch (e) {
    console.error(e)
  }
}

const updateRole = async () => {
  try {
    await api.put(`/admin/users/${selectedUser.value.id}`, { role: newRole.value })
    selectedUser.value = null
    await loadUsers()
  } catch (e) {
    console.error(e)
  }
}

const formatDate = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : '—'

const roleClass = (r) => ({
  admin:        'bg-primary text-accent border-accent/20 shadow-xl shadow-primary/10',
  manager:      'bg-slate-900 text-white border-slate-700 shadow-xl',
  receptionist: 'bg-slate-50 text-primary border-slate-200 shadow-sm',
  client:       'bg-white text-slate-500 border-slate-100 shadow-sm hover:border-accent/30',
}[r] ?? 'bg-white text-slate-500 border-slate-100 shadow-sm')

const logout = async () => { await authStore.logout(); router.push('/login') }

watch(currentPage, loadUsers)
onMounted(loadUsers)
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
.modal-enter-from, .modal-leave-to { opacity: 0; transform: scale(0.9) translateY(40px); filter: blur(10px); }

select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23b45309' stroke-width='3'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5' /%3E%3C/svg%3E");
  background-position: right 1.5rem center;
  background-repeat: no-repeat;
  background-size: 0.8rem;
}

.custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #b45309; }

@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slide-up {
  animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
