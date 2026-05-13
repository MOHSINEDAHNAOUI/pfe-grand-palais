<template>
  <AdminLayout page-title="Analyses & Rapports" @logout="logout">
    
    <!-- Top Summary Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12 animate-slide-up">
      <div v-for="kpi in kpis" :key="kpi.label" 
           class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-primary/5 group hover:shadow-2xl hover:-translate-y-1 transition-all duration-700 overflow-hidden relative">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-slate-50 rounded-full transition-transform duration-1000 group-hover:scale-150 opacity-50"></div>
        <div class="relative z-10">
          <div class="flex items-center gap-4 mb-4">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center bg-slate-50 group-hover:bg-primary group-hover:text-white transition-colors duration-500">
               <i :class="kpi.icon" class="text-xl"></i>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">{{ kpi.label }}</p>
          </div>
          <div class="flex items-baseline gap-2">
            <h4 class="text-3xl font-serif text-primary">{{ kpi.value }}</h4>
            <span v-if="kpi.trend" :class="kpi.trend > 0 ? 'text-emerald-500' : 'text-rose-500'" class="text-[10px] font-bold">
              {{ kpi.trend > 0 ? '+' : '' }}{{ kpi.trend }}%
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12">
      <!-- Occupancy Trends (Line Chart) -->
      <div class="bg-white p-10 rounded-[4rem] border border-slate-100 shadow-2xl shadow-primary/5 animate-slide-up" style="animation-delay: 0.1s">
        <div class="flex items-center justify-between mb-10">
           <h3 class="text-xl font-serif text-primary">Taux d'Occupation (30j)</h3>
           <i class="las la-chart-line text-2xl text-accent/20"></i>
        </div>
        <div class="h-80">
          <Line v-if="loaded" :data="occupancyChartData" :options="chartOptions" />
          <div v-else class="h-full flex items-center justify-center"><div class="w-8 h-8 border-4 border-accent border-t-transparent rounded-full animate-spin"></div></div>
        </div>
      </div>

      <!-- Revenue by Room Type (Doughnut) -->
      <div class="bg-white p-10 rounded-[4rem] border border-slate-100 shadow-2xl shadow-primary/5 animate-slide-up" style="animation-delay: 0.2s">
        <div class="flex items-center justify-between mb-10">
           <h3 class="text-xl font-serif text-primary">Revenus par Type de Chambre</h3>
           <i class="las la-pie-chart text-2xl text-accent/20"></i>
        </div>
        <div class="h-80 flex items-center justify-center">
          <Doughnut v-if="loaded" :data="roomTypeChartData" :options="pieOptions" />
          <div v-else class="h-full flex items-center justify-center"><div class="w-8 h-8 border-4 border-accent border-t-transparent rounded-full animate-spin"></div></div>
        </div>
      </div>

      <!-- Revenue by Service (Bar Chart) -->
      <div class="bg-white p-10 rounded-[4rem] border border-slate-100 shadow-2xl shadow-primary/5 animate-slide-up" style="animation-delay: 0.3s">
        <div class="flex items-center justify-between mb-10">
           <h3 class="text-xl font-serif text-primary">Top Services (Revenus)</h3>
           <i class="las la-concierge-bell text-2xl text-accent/20"></i>
        </div>
        <div class="h-80">
          <Bar v-if="loaded" :data="serviceChartData" :options="barOptions" />
          <div v-else class="h-full flex items-center justify-center"><div class="w-8 h-8 border-4 border-accent border-t-transparent rounded-full animate-spin"></div></div>
        </div>
      </div>

      <!-- Customer Origin (Bar Chart) -->
      <div class="bg-white p-10 rounded-[4rem] border border-slate-100 shadow-2xl shadow-primary/5 animate-slide-up" style="animation-delay: 0.4s">
        <div class="flex items-center justify-between mb-10">
           <h3 class="text-xl font-serif text-primary">Origine des Résidents</h3>
           <i class="las la-map-marker text-2xl text-accent/20"></i>
        </div>
        <div class="h-80">
          <Bar v-if="loaded" :data="cityChartData" :options="barOptions" />
          <div v-else class="h-full flex items-center justify-center"><div class="w-8 h-8 border-4 border-accent border-t-transparent rounded-full animate-spin"></div></div>
        </div>
      </div>
    </div>

    <!-- Export Section -->
    <div class="bg-primary p-12 rounded-[4rem] shadow-2xl shadow-primary/20 relative overflow-hidden animate-slide-up" style="animation-delay: 0.5s">
       <div class="absolute top-0 right-0 w-96 h-96 bg-accent/10 rounded-full blur-3xl"></div>
       <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-10">
          <div>
            <h3 class="text-4xl font-serif text-white mb-4">Édition de Rapports</h3>
            <p class="text-slate-400 text-sm max-w-md font-light leading-relaxed">
              Générez des documents officiels au format PDF pour votre comptabilité ou vos réunions de direction.
            </p>
          </div>
          <div class="flex flex-wrap gap-4">
             <button @click="exportReport('monthly')" class="px-10 py-5 bg-white text-primary rounded-2xl text-[10px] font-bold uppercase tracking-[0.2em] hover:bg-accent hover:text-white transition-all duration-700 shadow-xl flex items-center gap-3 active:scale-95">
                <i class="las la-file-pdf text-xl"></i> Rapport Mensuel (Simple)
             </button>
             <button @click="exportReport('accounting')" class="px-10 py-5 bg-white/5 border border-white/10 text-white rounded-2xl text-[10px] font-bold uppercase tracking-[0.2em] hover:bg-white hover:text-primary transition-all duration-700 flex items-center gap-3 active:scale-95">
                <i class="las la-wallet text-xl text-accent"></i> Rapport Comptable (Détaillé)
             </button>
          </div>
       </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminLayout from '@/components/admin/AdminLayout.vue'
import api from '@/api/axios'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'
import { Line, Bar, Doughnut } from 'vue-chartjs'

ChartJS.register(
  CategoryScale, LinearScale, PointElement, LineElement, 
  BarElement, ArcElement, Title, Tooltip, Legend, Filler
)

const router = useRouter()
const authStore = useAuthStore()
const loaded = ref(false)
const stats = ref(null)

const loadData = async () => {
  try {
    const { data } = await api.get('/admin/reports/advanced-stats')
    stats.value = data
    loaded.value = true
  } catch (e) {
    console.error(e)
  }
}

const kpis = computed(() => {
  if (!stats.value) return []
  return [
    { label: 'Revenus du Mois', value: formatCurrency(stats.value.monthly_comparison.current), icon: 'las la-wallet', trend: stats.value.monthly_comparison.growth },
    { label: 'Taux Occupation', value: stats.value.occupancy_trends[stats.value.occupancy_trends.length-1]?.rate + '%', icon: 'las la-door-open' },
    { label: 'Séjour Moyen', value: stats.value.stay_duration + ' nuits', icon: 'las la-clock' },
    { label: 'Top Ville', value: stats.value.customer_cities[0]?.city || 'N/A', icon: 'las la-map-marker' },
  ]
})

// Chart Data Configurations
const occupancyChartData = computed(() => ({
  labels: stats.value?.occupancy_trends.map(d => d.date) || [],
  datasets: [{
    label: 'Taux d\'occupation (%)',
    data: stats.value?.occupancy_trends.map(d => d.rate) || [],
    borderColor: '#b45309',
    backgroundColor: 'rgba(180, 83, 9, 0.05)',
    fill: true,
    tension: 0.4,
    pointRadius: 0,
    pointHoverRadius: 6,
    borderWidth: 3
  }]
}))

const roomTypeChartData = computed(() => ({
  labels: stats.value?.revenue_by_room_type.map(d => d.name) || [],
  datasets: [{
    data: stats.value?.revenue_by_room_type.map(d => d.total) || [],
    backgroundColor: ['#0f172a', '#b45309', '#3B82F6', '#10B981', '#F59E0B'],
    borderWidth: 0,
    hoverOffset: 20
  }]
}))

const serviceChartData = computed(() => ({
  labels: stats.value?.revenue_by_service.map(d => d.name) || [],
  datasets: [{
    label: 'Revenus (MAD)',
    data: stats.value?.revenue_by_service.map(d => d.total) || [],
    backgroundColor: '#0f172a',
    borderRadius: 12,
    barThickness: 40
  }]
}))

const cityChartData = computed(() => ({
  labels: stats.value?.customer_cities.map(d => d.city) || [],
  datasets: [{
    label: 'Nombre de Résidents',
    data: stats.value?.customer_cities.map(d => d.count) || [],
    backgroundColor: '#b45309',
    borderRadius: 12,
    barThickness: 40
  }]
}))

// Chart Options
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f172a', padding: 12, titleFont: { size: 10 }, bodyFont: { size: 12, weight: 'bold' } } },
  scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.03)' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
}

const barOptions = {
  ...chartOptions,
  scales: { ...chartOptions.scales, y: { ...chartOptions.scales.y, grid: { display: false } } }
}

const pieOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10, weight: 'bold' }, padding: 20 } } }
}

const formatCurrency = (val) => 
  new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'MAD', maximumFractionDigits: 0 }).format(val)

const exportReport = (type) => {
  const url = `http://localhost:8000/api/admin/reports/export/${type}?token=${authStore.token}`
  window.open(url, '_blank')
}

const logout = async () => { await authStore.logout(); router.push('/login') }

onMounted(loadData)
</script>

<style scoped>
@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slide-up {
  animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

canvas {
  filter: drop-shadow(0 10px 15px rgba(0,0,0,0.02));
}
</style>
