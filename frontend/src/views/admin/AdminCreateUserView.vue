<template>
  <AdminLayout page-title="Ouverture de Dossier" @logout="logout">

    <div class="max-w-6xl mx-auto animate-fade-in">

      <!-- Premium Header -->
      <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-8">
        <div>
          <div class="flex items-center gap-6 mb-4">
            <router-link to="/admin/users" class="w-14 h-14 rounded-full bg-white border border-slate-100 flex items-center justify-center text-primary hover:bg-accent hover:text-white transition-all duration-500 shadow-xl shadow-primary/5 group">
              <i class="las la-arrow-left text-2xl transition-transform group-hover:-translate-x-1"></i>
            </router-link>
            <p class="text-accent font-bold tracking-[0.4em] uppercase text-[10px]">Département RH & Conciergerie</p>
          </div>
          <h2 class="text-5xl font-serif text-primary leading-tight">Ouverture de <span class="italic text-accent">Dossier</span></h2>
          <p class="text-slate-400 font-light mt-4 text-sm leading-relaxed max-w-xl">
            Enregistrez un nouveau membre du personnel ou accueillez un nouveau résident privilégié au sein du Grand Palais.
          </p>
        </div>
      </div>

      <!-- Alerts -->
      <Transition name="fade">
        <div v-if="success" class="mb-8 p-6 bg-emerald-50 rounded-[2rem] border border-emerald-100 flex items-center gap-4 text-emerald-700 font-bold text-[11px] uppercase tracking-widest shadow-xl shadow-emerald-500/5">
          <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0"><i class="las la-check text-xl"></i></div>
          {{ success }}
        </div>
      </Transition>
      <Transition name="fade">
        <div v-if="error" class="mb-8 p-6 bg-rose-50 rounded-[2rem] border border-rose-100 flex items-center gap-4 text-rose-700 font-bold text-[11px] uppercase tracking-widest shadow-xl shadow-rose-500/5">
          <div class="w-10 h-10 rounded-full bg-rose-500 text-white flex items-center justify-center shrink-0"><i class="las la-times text-xl"></i></div>
          {{ error }}
        </div>
      </Transition>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 pb-12">
        
        <!-- Form Section -->
        <div class="lg:col-span-2 space-y-10">
          
          <!-- Infos Base -->
          <div class="bg-white p-8 md:p-12 rounded-[3.5rem] border border-slate-100 shadow-2xl shadow-primary/5 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-[8rem] -z-10 group-hover:scale-110 transition-transform duration-1000"></div>
            
            <h3 class="text-2xl font-serif text-primary mb-10 flex items-center gap-4">
              <span class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-accent"><i class="las la-id-card text-2xl"></i></span>
              Identité du Profil
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
              <div class="space-y-3">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4">Nom complet</label>
                <div class="relative">
                  <span class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"><i class="las la-user text-xl"></i></span>
                  <input v-model="form.name" type="text" placeholder="Ex: Jean Dupont"
                         class="w-full pl-14 pr-6 py-5 bg-white border border-slate-100 rounded-[2rem] text-sm font-medium text-primary focus:border-accent/30 focus:ring-0 transition-all outline-none shadow-sm" />
                </div>
              </div>
              <div class="space-y-3">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4">Adresse Email</label>
                <div class="relative">
                  <span class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"><i class="las la-envelope text-xl"></i></span>
                  <input v-model="form.email" type="email" placeholder="contact@exemple.com"
                         class="w-full pl-14 pr-6 py-5 bg-white border border-slate-100 rounded-[2rem] text-sm font-medium text-primary focus:border-accent/30 focus:ring-0 transition-all outline-none shadow-sm" />
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div class="space-y-3">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4">Mot de passe provisoire</label>
                <div class="relative">
                  <span class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"><i class="las la-lock text-xl"></i></span>
                  <input v-model="form.password" type="password" placeholder="••••••••"
                         class="w-full pl-14 pr-6 py-5 bg-white border border-slate-100 rounded-[2rem] text-sm font-medium text-primary focus:border-accent/30 focus:ring-0 transition-all outline-none shadow-sm" />
                </div>
              </div>
              <div class="space-y-3">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4">Téléphone de contact</label>
                <div class="relative">
                  <span class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"><i class="las la-phone text-xl"></i></span>
                  <input v-model="form.phone" type="tel" placeholder="+212 ..."
                         class="w-full pl-14 pr-6 py-5 bg-white border border-slate-100 rounded-[2rem] text-sm font-medium text-primary focus:border-accent/30 focus:ring-0 transition-all outline-none shadow-sm" />
                </div>
              </div>
            </div>
          </div>

          <!-- Permissions (Visible only for Staff) -->
          <Transition name="slide-up">
            <div v-if="form.role && form.role !== 'client'" class="bg-white p-8 md:p-12 rounded-[3.5rem] border border-slate-100 shadow-2xl shadow-primary/5 relative overflow-hidden">
              <div class="absolute inset-0 bg-gradient-to-br from-slate-50 to-white -z-10"></div>
              
              <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                <h3 class="text-2xl font-serif text-primary flex items-center gap-4">
                  <span class="w-12 h-12 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-center text-accent"><i class="las la-shield-alt text-2xl"></i></span>
                  Matrice des Habilitations
                </h3>
                <div class="flex gap-3">
                  <button @click="selectAll" class="px-5 py-3 rounded-full bg-slate-100 text-[9px] font-bold uppercase tracking-[0.2em] text-primary hover:bg-primary hover:text-white transition-all">Tout cocher</button>
                  <button @click="deselectAll" class="px-5 py-3 rounded-full bg-white border border-slate-100 text-[9px] font-bold uppercase tracking-[0.2em] text-slate-400 hover:text-primary transition-all">Tout décocher</button>
                </div>
              </div>

              <div v-if="loadingPermissions" class="py-16 text-center text-accent flex flex-col items-center justify-center">
                 <div class="w-12 h-12 border-4 border-accent/20 border-t-accent rounded-full animate-spin mb-4"></div>
                 <p class="text-[10px] font-bold uppercase tracking-[0.3em]">Analyse des droits d'accès...</p>
              </div>

              <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                 <label v-for="perm in allPermissions" :key="perm" 
                        class="flex items-center gap-4 p-5 rounded-[1.5rem] border transition-all cursor-pointer group"
                        :class="form.permissions.includes(perm) ? 'bg-primary border-primary text-white shadow-xl shadow-primary/20' : 'bg-white border-slate-100 text-primary hover:border-accent/30 hover:bg-slate-50/50'">
                   <input type="checkbox" :value="perm" v-model="form.permissions" class="hidden" />
                   <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all shrink-0"
                        :class="form.permissions.includes(perm) ? 'border-transparent bg-white/20' : 'border-slate-200 group-hover:border-accent/50'">
                     <i v-if="form.permissions.includes(perm)" class="las la-check text-white text-xs"></i>
                   </div>
                   <span class="text-[10px] font-bold uppercase tracking-widest line-clamp-1" :class="form.permissions.includes(perm) ? 'text-white' : 'text-slate-500'">{{ permLabel(perm) }}</span>
                 </label>
              </div>
            </div>
          </Transition>
        </div>

        <!-- Sidebar / Actions -->
        <div class="space-y-8">
          <div class="bg-primary p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-48 h-48 bg-accent/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
            
            <h3 class="text-xl font-serif text-white mb-8 flex items-center gap-4 relative z-10">
              <i class="las la-crown text-accent text-3xl"></i>
              Statut du Profil
            </h3>
            
            <div class="space-y-4 relative z-10">
              <button v-for="role in roles" :key="role.value" @click="selectRole(role.value)"
                      class="w-full p-6 rounded-[2rem] border-2 text-left transition-all duration-500 overflow-hidden relative group/btn"
                      :class="form.role === role.value ? 'border-accent bg-accent text-white shadow-2xl shadow-accent/20' : 'border-white/10 bg-white/5 text-slate-300 hover:border-white/30 hover:bg-white/10'">
                <div class="relative z-10">
                  <p class="text-xs font-bold uppercase tracking-widest text-white mb-1 flex items-center justify-between">
                    {{ role.label }}
                    <i v-if="form.role === role.value" class="las la-check-circle text-xl text-white"></i>
                  </p>
                  <p class="text-[10px] opacity-80 leading-relaxed font-light">{{ role.desc }}</p>
                </div>
              </button>
            </div>
          </div>

          <button @click="createUser" :disabled="saving || !form.name || !form.email || !form.password || !form.role"
                  class="w-full py-8 bg-white text-primary rounded-[2.5rem] font-bold text-xs uppercase tracking-[0.2em] hover:bg-accent hover:text-white hover:border-accent border-2 border-slate-100 transition-all duration-700 shadow-2xl shadow-primary/5 disabled:opacity-50 active:scale-95 flex items-center justify-center gap-4 group cursor-pointer">
            <span v-if="saving" class="w-5 h-5 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
            <i v-else class="las la-save text-2xl transition-transform group-hover:scale-110"></i>
            {{ saving ? 'Génération...' : 'Valider l\'inscription' }}
          </button>
        </div>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminLayout from '@/components/admin/AdminLayout.vue'
import api from '@/api/axios'

const router = useRouter()
const authStore = useAuthStore()

const saving = ref(false)
const success = ref('')
const error = ref('')
const loadingPermissions = ref(false)
const allPermissions = ref([])

const roles = computed(() => {
  const all = [
    { value: 'admin', label: 'Administrateur', desc: 'Contrôle absolu et système' },
    { value: 'manager', label: 'Manager du Domaine', desc: 'Gestion opérationnelle globale' },
    { value: 'receptionist', label: 'Service Réception', desc: 'Gestion courante des résidents' },
    { value: 'client', label: 'Client Privilégié', desc: 'Accès standard aux services' },
  ]
  
  if (authStore.user?.roles?.[0]?.name === 'manager') {
    return all.filter(r => ['receptionist', 'client'].includes(r.value))
  }
  return all
})

const form = reactive({ name: '', email: '', password: '', phone: '', role: 'client', permissions: [] })

const selectRole = async (role) => {
  form.role = role
  if (role === 'client') { form.permissions = []; return }
  loadingPermissions.value = true
  try {
    const { data } = await api.get(`/admin/roles/${role}/permissions`)
    form.permissions = [...data]
  } catch (e) { console.error(e) }
  finally { loadingPermissions.value = false }
}

const selectAll = () => { form.permissions = [...allPermissions.value] }
const deselectAll = () => { form.permissions = [] }

const createUser = async () => {
  saving.value = true
  error.value = ''; success.value = ''
  try {
    await api.post('/admin/users', { ...form, permissions: form.role !== 'client' ? form.permissions : [] })
    success.value = `Le dossier pour ${form.name} a été créé avec succès.`
    setTimeout(() => router.push('/admin/users'), 2000)
  } catch (e) { 
    error.value = e.response?.data?.message ?? 'Erreur lors de la création du dossier.' 
  }
  finally { saving.value = false }
}

const permLabel = (perm) => ({
  view_dashboard_full: 'Accès analytiques complets',
  view_dashboard_today: 'Accès données du jour',
  view_reservations: 'Consulter les séjours',
  confirm_reservation: 'Valider les dossiers',
  checkin_reservation: 'Procédures d\'arrivée',
  checkout_reservation: 'Procédures de départ',
  cancel_reservation: 'Annulation de séjours',
  manage_reservations: 'Gestion totale séjours',
  view_rooms: 'Visualiser l\'état des chambres',
  edit_room: 'Modifier les caractéristiques',
  change_room_status: 'Piloter le statut d\'entretien',
  view_clients: 'Consulter l\'annuaire',
  manage_clients: 'Gestion totale annuaire',
  create_user: 'Créer de nouveaux accès',
  view_reports: 'Consulter les rapports financiers',
}[perm] ?? perm)

const logout = async () => { await authStore.logout(); router.push('/login') }

onMounted(async () => {
  try {
    const { data } = await api.get('/admin/permissions')
    allPermissions.value = Object.values(data).flat().map(p => p.name ?? p)
    
    if(form.role && form.role !== 'client') {
       selectRole(form.role)
    }
  } catch (e) { 
    console.error(e)
    if (e.response?.status === 403) {
      error.value = "Accès restreint : Seul l'Administrateur peut modifier la matrice des permissions."
    }
  }
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.slide-up-enter-active, .slide-up-leave-active {
  transition: all 0.5s ease-out;
}
.slide-up-enter-from, .slide-up-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>
