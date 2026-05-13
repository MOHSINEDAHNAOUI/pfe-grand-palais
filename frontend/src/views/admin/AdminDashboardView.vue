<template>
  <AdminLayout page-title="Tableau de Bord" @logout="logout">

    <!-- Simple Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 animate-fade-in">
      <div v-for="stat in statsCards" :key="stat.label" 
           class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-4 mb-4">
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center" :class="stat.bg">
            <i :class="[stat.icon, stat.color]" class="text-2xl"></i>
          </div>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ stat.label }}</p>
        </div>
        <p class="text-4xl font-serif text-primary">{{ stat.value }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8 animate-fade-in" style="animation-delay: 0.1s">
      <!-- Quick Controls -->
      <div class="lg:col-span-1 space-y-6">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm h-full">
          <h3 class="text-xl font-serif text-primary mb-6">Contrôle Rapide</h3>
          <div class="grid grid-cols-1 gap-4">
            <router-link to="/admin/reservations" class="flex items-center justify-between p-5 bg-slate-50 hover:bg-primary hover:text-white rounded-2xl transition-all group">
              <span class="font-bold text-xs uppercase tracking-widest">Nouvelle Réservation</span>
              <i class="las la-plus-circle text-xl"></i>
            </router-link>
            <router-link to="/admin/rooms" class="flex items-center justify-between p-5 bg-slate-50 hover:bg-primary hover:text-white rounded-2xl transition-all group">
              <span class="font-bold text-xs uppercase tracking-widest">État des Chambres</span>
              <i class="las la-door-open text-xl"></i>
            </router-link>
            <router-link to="/admin/users" class="flex items-center justify-between p-5 bg-slate-50 hover:bg-primary hover:text-white rounded-2xl transition-all group">
              <span class="font-bold text-xs uppercase tracking-widest">Gestion Clients</span>
              <i class="las la-users text-xl"></i>
            </router-link>
            <router-link to="/admin/services" class="flex items-center justify-between p-5 bg-slate-50 hover:bg-primary hover:text-white rounded-2xl transition-all group">
              <span class="font-bold text-xs uppercase tracking-widest">Services & Menus</span>
              <i class="las la-concierge-bell text-xl"></i>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Recent Activity Table -->
      <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between mb-8">
          <h3 class="text-xl font-serif text-primary">Arrivées & Départs (Aujourd'hui)</h3>
          <router-link to="/admin/reservations" class="text-accent text-[10px] font-bold uppercase tracking-widest hover:underline">
            Voir tout le planning
          </router-link>
        </div>

        <div v-if="!todayReservations.length" class="py-20 text-center text-slate-300">
           <i class="las la-calendar-check text-5xl mb-4 opacity-20"></i>
           <p class="font-medium italic">Aucun mouvement prévu aujourd'hui</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-50">
                <th class="pb-4">Client</th>
                <th class="pb-4">Chambre</th>
                <th class="pb-4">Statut</th>
                <th class="pb-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              <tr v-for="r in todayReservations" :key="r.id" class="group hover:bg-slate-50/50 transition-colors">
                <td class="py-5">
                  <p class="font-bold text-primary">{{ r.user?.name }}</p>
                  <p class="text-[10px] text-slate-400 uppercase tracking-widest">{{ r.reference }}</p>
                </td>
                <td class="py-5">
                  <span class="px-3 py-1 bg-slate-100 rounded-lg font-bold text-xs">N° {{ r.room?.number }}</span>
                </td>
                <td class="py-5">
                  <span :class="statusStyle(r.status)" class="text-[9px] font-bold uppercase tracking-widest px-3 py-1 rounded-full border">
                    {{ statusLabel(r.status) }}
                  </span>
                </td>
                <td class="py-5 text-right">
                  <div class="flex justify-end gap-2">
                    <button v-if="r.status === 'confirmed'" @click="checkIn(r)" 
                            class="p-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-all shadow-sm">
                      <i class="las la-check"></i>
                    </button>
                    <button v-if="r.status === 'checked_in'" @click="checkOut(r)" 
                            class="p-2 bg-primary text-white rounded-lg hover:bg-accent transition-all shadow-sm">
                      <i class="las la-sign-out-alt"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Occupancy Chart (Simplified) -->
    <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm animate-fade-in" style="animation-delay: 0.2s">
      <div class="flex items-center justify-between mb-10">
        <div>
          <h3 class="text-2xl font-serif text-primary">Performance du Palais</h3>
          <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Évolution des revenus sur 12 mois</p>
        </div>
        <div class="flex bg-slate-50 p-1.5 rounded-2xl border border-slate-100">
           <button v-for="p in periods" :key="p.value" @click="activePeriod = p.value"
                   class="px-6 py-2 text-[10px] font-bold uppercase tracking-widest rounded-xl transition-all"
                   :class="activePeriod === p.value ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-primary'">
             {{ p.label }}
           </button>
        </div>
      </div>

      <div class="flex items-end gap-4 h-64">
         <div v-for="item in chartData" :key="item.label" class="flex-1 flex flex-col items-center gap-4 group">
            <div class="relative w-full flex flex-col justify-end h-full">
               <div class="w-full bg-primary/5 rounded-t-xl group-hover:bg-primary/10 transition-all relative overflow-hidden" 
                    :style="{ height: (item.revenue / maxRevenue * 100) + '%' }">
                  <div class="absolute inset-x-0 bottom-0 bg-primary opacity-20 h-full transform origin-bottom transition-transform duration-1000 scale-y-0 group-hover:scale-y-100"></div>
                  <div class="absolute top-2 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-primary text-white text-[9px] px-2 py-1 rounded shadow-xl">
                    {{ formatCurrency(item.revenue) }}
                  </div>
               </div>
            </div>
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ item.label }}</span>
         </div>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminLayout from '@/components/admin/AdminLayout.vue'
import api from '@/api/axios'

const router = useRouter()
const authStore = useAuthStore()

const stats = ref(null)
const chartData = ref([])
const todayReservations = ref([])
const activePeriod = ref('12m')

const periods = [
  { label: '7j', value: '7j' },
  { label: '30j', value: '30j' },
  { label: '12m', value: '12m' },
]

const statsCards = computed(() => [
  { label: 'Occupation', value: stats.value?.occupancy_rate + '%' || '0%', icon: 'las la-door-open', bg: 'bg-emerald-50', color: 'text-emerald-600' },
  { label: 'Revenus (Mois)', value: formatCurrency(stats.value?.monthly_revenue || 0), icon: 'las la-wallet', bg: 'bg-accent/5', color: 'text-accent' },
  { label: 'Clients VIP', value: stats.value?.total_clients || '0', icon: 'las la-user-tie', bg: 'bg-blue-50', color: 'text-blue-600' },
  { label: 'Alertes', value: stats.value?.pending_reservations || '0', icon: 'las la-bell', bg: 'bg-rose-50', color: 'text-rose-600' },
])

const maxRevenue = computed(() => Math.max(...chartData.value.map(d => d.revenue), 1))

const formatCurrency = (val) => 
  new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'MAD', maximumFractionDigits: 0 }).format(val)

const statusLabel = (s) => ({ pending: 'En Attente', confirmed: 'Confirmé', checked_in: 'Présent', checked_out: 'Terminé', cancelled: 'Révoqué' }[s] ?? s)

const statusStyle = (s) => ({
  pending: 'border-amber-100 text-amber-600 bg-amber-50',
  confirmed: 'border-blue-100 text-blue-600 bg-blue-50',
  checked_in: 'border-emerald-100 text-emerald-600 bg-emerald-50',
  checked_out: 'border-slate-100 text-slate-600 bg-slate-50',
  cancelled: 'border-red-100 text-red-600 bg-red-50',
}[s] ?? '')

const checkIn = async (r) => { await api.post(`/admin/reservations/${r.id}/check-in`); r.status = 'checked_in' }
const checkOut = async (r) => { await api.post(`/admin/reservations/${r.id}/check-out`); r.status = 'checked_out' }

const logout = async () => { await authStore.logout(); router.push('/login') }

const loadDashboardData = async () => {
  try {
    const [statsRes, chartRes, todayRes] = await Promise.all([
      api.get('/admin/dashboard'),
      api.get('/admin/dashboard/revenue-chart', { params: { period: activePeriod.value } }),
      api.get('/admin/dashboard/today')
    ])
    stats.value = statsRes.data
    chartData.value = chartRes.data
    todayReservations.value = todayRes.data
  } catch (e) {
    console.error(e)
  }
}

watch(activePeriod, loadDashboardData)
onMounted(loadDashboardData)
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
