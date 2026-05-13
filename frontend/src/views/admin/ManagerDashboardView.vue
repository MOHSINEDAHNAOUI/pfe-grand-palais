<template>
  <AdminLayout page-title="Tableau de bord" @logout="logout">

    <div class="max-w-[1600px] mx-auto animate-fade-in">
      
      <!-- Premium Hero Section -->
      <div class="mb-10 bg-primary p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-96 h-96 bg-accent/10 rounded-full blur-[100px] group-hover:scale-110 transition-transform duration-1000"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
          <div>
            <p class="text-accent font-bold tracking-[0.4em] uppercase text-[10px] mb-4">Espace de Pilotage Stratégique</p>
            <h2 class="text-5xl font-serif text-white mb-2">Bienvenue, <span class="italic text-accent">{{ authStore.user?.name }}</span></h2>
            <p class="text-slate-400 text-sm font-light tracking-widest uppercase">Directeur d'Hôtel • {{ today }}</p>
          </div>
          <div class="flex gap-4">
            <router-link to="/admin/analytics" class="px-8 py-4 bg-white/5 border border-white/10 text-white rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-white/10 transition-all">
              Rapports Détaillés
            </router-link>
            <router-link to="/admin/reservations" class="px-8 py-4 bg-accent text-white rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-accent/80 transition-all shadow-xl shadow-accent/20">
              Gérer Planning
            </router-link>
          </div>
        </div>
      </div>

      <!-- Strategic KPI Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div v-for="kpi in kpis" :key="kpi.label"
             class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-primary/5 group hover:scale-[1.02] transition-all duration-500 relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-slate-50 rounded-bl-[4rem] -z-10 group-hover:bg-accent/5 transition-colors"></div>
          
          <div class="flex items-start justify-between">
            <div>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">{{ kpi.label }}</p>
              <h3 class="text-3xl font-serif text-primary mb-1">{{ kpi.value }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all group-hover:rotate-12"
                 :style="{ backgroundColor: kpi.color + '15', color: kpi.color }">
              <i :class="[kpi.icon, 'text-2xl']"></i>
            </div>
          </div>
          
          <div class="mt-6 h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-1000"
                 :style="{ width: '100%', backgroundColor: kpi.color }"></div>
          </div>
        </div>
      </div>

      <!-- Financials & Inventory Grid -->
      <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-10">
        
        <!-- Revenue Evolution (Vertical Bar Chart Modernized) -->
        <div class="xl:col-span-2 bg-white p-10 rounded-[3.5rem] border border-slate-100 shadow-2xl shadow-primary/5">
          <div class="flex items-center justify-between mb-10">
            <div class="flex items-center gap-4">
              <span class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-primary"><i class="las la-chart-bar text-2xl"></i></span>
              <div>
                <h2 class="text-2xl font-serif text-primary">Performances Financières</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ formatCurrency(stats?.monthly_revenue ?? 0) }} • Recettes ce mois</p>
              </div>
            </div>
          </div>

          <div v-if="loadingChart" class="py-20 flex justify-center">
            <div class="w-10 h-10 border-3 border-accent/20 border-t-accent rounded-full animate-spin"></div>
          </div>
          <div v-else class="space-y-6">
            <div v-for="item in chartData" :key="item.label" class="group">
              <div class="flex items-center justify-between mb-2 px-2">
                <span class="text-[10px] font-bold text-primary uppercase tracking-widest">{{ item.label }}</span>
                <span class="text-xs font-serif text-accent">{{ formatCurrency(item.revenue) }}</span>
              </div>
              <div class="h-8 bg-slate-50 rounded-xl overflow-hidden relative group-hover:shadow-md transition-all">
                <div class="h-full bg-gradient-to-r from-primary to-accent transition-all duration-1000 ease-out flex items-center justify-end pr-4"
                     :style="{ width: getBarWidth(item.revenue) }">
                   <span v-if="item.revenue > 0" class="text-[8px] font-black text-white/40 tracking-[0.3em]">PROJECTION</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Inventory Status (Occupancy) -->
        <div class="bg-white p-10 rounded-[3.5rem] border border-slate-100 shadow-2xl shadow-primary/5 flex flex-col items-center">
          <h2 class="text-2xl font-serif text-primary mb-10 text-center">Taux d'Occupation</h2>
          
          <div class="relative w-56 h-56 flex items-center justify-center mb-10 group">
            <svg class="w-full h-full -rotate-90">
              <circle cx="112" cy="112" r="100" fill="none" stroke="#f1f5f9" stroke-width="12"/>
              <circle cx="112" cy="112" r="100" fill="none" stroke="#b45309" stroke-width="12"
                      stroke-dasharray="628"
                      :stroke-dashoffset="628 - (628 * (stats?.occupancy_rate ?? 0) / 100)"
                      stroke-linecap="round"
                      class="transition-all duration-1000 ease-out" />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center group-hover:scale-110 transition-transform">
              <p class="text-5xl font-serif text-primary">{{ stats?.occupancy_rate ?? 0 }}%</p>
              <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Capacité Remplie</p>
            </div>
          </div>

          <div class="w-full space-y-6">
            <div v-for="s in roomStats" :key="s.label" class="bg-slate-50 p-6 rounded-3xl">
              <div class="flex justify-between items-end mb-3">
                <div>
                  <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ s.label }}</p>
                  <p class="text-xl font-serif text-primary">{{ s.count }} Chambres</p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-lg" :style="{ backgroundColor: s.color }">
                   <i :class="s.label === 'Disponibles' ? 'las la-check-circle' : 'las la-user-tag'" class="text-xl"></i>
                </div>
              </div>
              <div class="h-1.5 bg-white rounded-full overflow-hidden">
                <div class="h-full transition-all duration-1000" :style="{ width: s.percent + '%', backgroundColor: s.color }"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Real-time Activity Table -->
      <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-2xl shadow-primary/5 overflow-hidden mb-12">
        <div class="p-10 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
          <div class="flex items-center gap-4">
            <span class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-primary shadow-sm">
              <i class="las la-history text-2xl"></i>
            </span>
            <div>
              <h2 class="text-2xl font-serif text-primary">Flux Opérationnel</h2>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Dernières activités & mouvements du jour</p>
            </div>
          </div>
          <router-link to="/admin/reservations" class="px-6 py-3 bg-white border border-slate-100 rounded-xl text-[10px] font-bold text-primary hover:bg-primary hover:text-white transition-all uppercase tracking-widest">
            Historique Complet
          </router-link>
        </div>

        <div v-if="!todayReservations.length" class="py-24 text-center opacity-30">
          <i class="las la-calendar-day text-6xl mb-4 block"></i>
          <p class="text-[10px] font-bold uppercase tracking-[0.3em]">Aucune activité enregistrée ce jour</p>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-slate-50/30">
                <th class="py-6 px-10 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Client Privilégié</th>
                <th class="py-6 px-10 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Hébergement</th>
                <th class="py-6 px-10 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Statut</th>
                <th class="py-6 px-10 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Actions Stratégiques</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              <tr v-for="r in todayReservations" :key="r.id" class="group hover:bg-slate-50/50 transition-all">
                <td class="py-6 px-10">
                  <div class="flex items-center gap-4">
                    <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(r.user?.name || 'U')}&size=80&background=0f172a&color=fff&bold=true`"
                         class="w-12 h-12 rounded-xl object-cover border border-slate-100 shadow-sm" />
                    <div>
                      <p class="text-sm font-bold text-primary">{{ r.user?.name }}</p>
                      <p class="text-[10px] font-bold text-accent uppercase tracking-widest">{{ r.reference }}</p>
                    </div>
                  </div>
                </td>
                <td class="py-6 px-10">
                  <p class="text-xs font-medium text-slate-600">Unité N° {{ r.room?.number }}</p>
                  <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ r.room?.room_type?.name }}</p>
                </td>
                <td class="py-6 px-10">
                  <span class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-[0.2em]" :style="statusStyle(r.status)">
                    {{ statusLabel(r.status) }}
                  </span>
                </td>
                <td class="py-6 px-10 text-right">
                  <div class="flex justify-end gap-2">
                    <button v-if="r.status === 'confirmed'" @click="checkIn(r)"
                            class="px-5 py-2.5 bg-emerald-500 text-white rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20">
                      Check-in ✓
                    </button>
                    <button v-if="r.status === 'checked_in'" @click="checkOut(r)"
                            class="px-5 py-2.5 bg-blue-500 text-white rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg shadow-blue-500/20">
                      Check-out →
                    </button>
                    <router-link :to="`/admin/reservations?search=${r.reference}`" 
                                 class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-primary hover:text-white transition-all">
                      <i class="las la-eye text-xl"></i>
                    </router-link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminLayout from '@/components/admin/AdminLayout.vue'
import api from '@/api/axios'

const router    = useRouter()
const authStore = useAuthStore()

const stats             = ref(null)
const chartData         = ref([])
const todayReservations = ref([])
const loadingChart      = ref(true)

const today = new Date().toLocaleDateString('fr-FR', {
  weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
})

const kpis = computed(() => [
  { icon: 'las la-wallet',         label: 'Revenus ce mois',      value: formatCurrency(stats.value?.monthly_revenue ?? 0), color: '#b45309' },
  { icon: 'las la-calendar-check', label: 'Réservations actives', value: stats.value?.active_reservations ?? 0,             color: '#3b82f6' },
  { icon: 'las la-door-open',      label: 'Chambres disponibles',value: stats.value?.available_rooms ?? 0,                  color: '#10b981' },
  { icon: 'las la-users',          label: 'Clients total',         value: stats.value?.total_clients ?? 0,                   color: '#6366f1' },
])

const roomStats = computed(() => {
  const total     = stats.value?.total_rooms ?? 1
  const available = stats.value?.available_rooms ?? 0
  const occupied  = total - available
  return [
    { label: 'Disponibles', count: available, color: '#10b981', percent: ((available / total) * 100).toFixed(0) },
    { label: 'Occupées',    count: occupied,  color: '#b45309', percent: ((occupied  / total) * 100).toFixed(0) },
  ]
})

const maxRevenue  = computed(() => Math.max(...chartData.value.map(d => d.revenue), 1))
const getBarWidth = (revenue) => `${(revenue / maxRevenue.value * 100).toFixed(0)}%`

const formatCurrency = (val) =>
  new Intl.NumberFormat('fr-MA', { style: 'currency', currency: 'MAD', maximumFractionDigits: 0 }).format(val ?? 0)

const statusLabel = (s) => ({ pending: 'En attente', confirmed: 'Confirmée', checked_in: 'Présent', checked_out: 'Parti', cancelled: 'Annulée' }[s] ?? s)
const statusStyle = (s) => ({
  pending: 'background: #fef3c7; color: #b45309;', confirmed: 'background: #dbeafe; color: #1d4ed8;',
  checked_in: 'background: #dcfce7; color: #15803d;', checked_out: 'background: #f3e8ff; color: #7e22ce;',
  cancelled: 'background: #fee2e2; color: #b91c1c;',
}[s] ?? '')

const checkIn  = async (r) => { await api.post(`/admin/reservations/${r.id}/check-in`);  r.status = 'checked_in' }
const checkOut = async (r) => { await api.post(`/admin/reservations/${r.id}/check-out`); r.status = 'checked_out' }
const logout   = async () => { await authStore.logout(); router.push('/login') }

onMounted(async () => {
  const [statsRes, chartRes, todayRes] = await Promise.allSettled([
    api.get('/admin/dashboard'),
    api.get('/admin/dashboard/revenue-chart', { params: { period: 'monthly' } }),
    api.get('/admin/dashboard/today'),
  ])
  if (statsRes.status === 'fulfilled') stats.value             = statsRes.value.data
  if (chartRes.status === 'fulfilled') chartData.value         = chartRes.value.data.slice(-6)
  if (todayRes.status === 'fulfilled') todayReservations.value = todayRes.value.data
  loadingChart.value = false
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
