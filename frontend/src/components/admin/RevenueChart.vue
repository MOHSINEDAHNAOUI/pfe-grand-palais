<!-- frontend/src/components/admin/RevenueChart.vue -->
<template>
  <div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-bold text-gray-800">Revenus</h2>
        <p class="text-sm text-gray-400 mt-0.5">Évolution sur la période sélectionnée</p>
      </div>
      <div class="flex gap-2">
        <button
          v-for="p in periods"
          :key="p.value"
          @click="changePeriod(p.value)"
          :class="[
            'text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors',
            activePeriod === p.value
              ? 'bg-primary-600 text-white'
              : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
          ]"
        >
          {{ p.label }}
        </button>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-3 gap-4 mb-6">
      <div class="bg-primary-50 rounded-xl p-4 text-center">
        <p class="text-xs text-primary-600 font-semibold uppercase tracking-wide mb-1">Total</p>
        <p class="text-xl font-bold text-primary-700">{{ formatCurrency(kpis.total) }}</p>
      </div>
      <div class="bg-green-50 rounded-xl p-4 text-center">
        <p class="text-xs text-green-600 font-semibold uppercase tracking-wide mb-1">Moyenne</p>
        <p class="text-xl font-bold text-green-700">{{ formatCurrency(kpis.average) }}</p>
      </div>
      <div class="bg-orange-50 rounded-xl p-4 text-center">
        <p class="text-xs text-orange-600 font-semibold uppercase tracking-wide mb-1">Meilleur mois</p>
        <p class="text-xl font-bold text-orange-700">{{ formatCurrency(kpis.best) }}</p>
      </div>
    </div>

    <!-- Chart -->
    <div v-if="loading" class="flex justify-center items-center h-48">
      <LoadingSpinner />
    </div>

    <div v-else-if="!chartData" class="flex justify-center items-center h-48 text-gray-400">
      Aucune donnée disponible
    </div>

    <div v-else class="relative h-64">
      <Line :data="chartData" :options="chartOptions" />
    </div>

    <!-- Legend Table -->
    <div v-if="chartData && !loading" class="mt-6 border-t pt-4">
      <div class="grid grid-cols-3 gap-2 text-xs text-gray-500 font-medium mb-2 px-1">
        <span>Période</span>
        <span class="text-center">Revenus</span>
        <span class="text-right">Réservations</span>
      </div>
      <div
        v-for="(item, i) in rawData.slice(-5)"
        :key="i"
        class="grid grid-cols-3 gap-2 text-xs py-1.5 px-1 rounded-lg hover:bg-gray-50 transition"
      >
        <span class="text-gray-600 font-medium">{{ item.label }}</span>
        <span class="text-center font-semibold text-primary-600">{{ formatCurrency(item.revenue) }}</span>
        <span class="text-right text-gray-500">{{ item.reservations }} rés.</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler,
} from 'chart.js'
import { Line } from 'vue-chartjs'
import api from '@/api/axios'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

ChartJS.register(
  CategoryScale, LinearScale, PointElement,
  LineElement, Title, Tooltip, Legend, Filler
)

// ─── Props ────────────────────────────────────────────────────
const props = defineProps({
  /** Injecter les données depuis le parent (optionnel) */
  externalData: {
    type:    Array,
    default: null,
  },
})

// ─── State ────────────────────────────────────────────────────
const loading      = ref(false)
const activePeriod = ref('monthly')
const rawData      = ref([])

const periods = [
  { value: 'weekly',   label: '7j'   },
  { value: 'monthly',  label: '12 mois' },
  { value: 'quarterly',label: 'Trimestres' },
]

// ─── KPIs ─────────────────────────────────────────────────────
const kpis = computed(() => {
  if (!rawData.value.length) return { total: 0, average: 0, best: 0 }
  const revenues = rawData.value.map(d => d.revenue)
  return {
    total:   revenues.reduce((s, v) => s + v, 0),
    average: revenues.reduce((s, v) => s + v, 0) / revenues.length,
    best:    Math.max(...revenues),
  }
})

// ─── Chart Data ───────────────────────────────────────────────
const chartData = computed(() => {
  if (!rawData.value.length) return null
  return {
    labels:   rawData.value.map(d => d.label),
    datasets: [
      {
        label:           'Revenus ( MAD)',
        data:            rawData.value.map(d => d.revenue),
        borderColor:     '#0284c7',
        backgroundColor: 'rgba(2, 132, 199, 0.08)',
        borderWidth:     2.5,
        pointBackgroundColor: '#0284c7',
        pointRadius:     4,
        pointHoverRadius: 6,
        tension:         0.4,
        fill:            true,
      },
      {
        label:           'Réservations',
        data:            rawData.value.map(d => d.reservations),
        borderColor:     '#f59e0b',
        backgroundColor: 'transparent',
        borderWidth:     2,
        pointBackgroundColor: '#f59e0b',
        pointRadius:     3,
        pointHoverRadius: 5,
        tension:         0.4,
        fill:            false,
        yAxisID:         'y1',
      },
    ],
  }
})

const chartOptions = {
  responsive:          true,
  maintainAspectRatio: false,
  interaction: { mode: 'index', intersect: false },
  plugins: {
    legend: {
      position: 'top',
      labels:   { usePointStyle: true, padding: 16, font: { size: 12 } },
    },
    tooltip: {
      callbacks: {
        label: (ctx) => {
          if (ctx.dataset.yAxisID === 'y1') {
            return `  Réservations : ${ctx.parsed.y}`
          }
          return `  Revenus : ${new Intl.NumberFormat('fr-FR').format(ctx.parsed.y)} MAD`
        },
      },
    },
  },
  scales: {
    x: {
      grid: { display: false },
      ticks: { font: { size: 11 }, color: '#9ca3af' },
    },
    y: {
      position: 'left',
      grid:     { color: 'rgba(0,0,0,0.05)' },
      ticks: {
        font:     { size: 11 },
        color:    '#9ca3af',
        callback: (v) => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v) + ' MAD',
      },
    },
    y1: {
      position: 'right',
      grid:     { display: false },
      ticks: {
        font:  { size: 11 },
        color: '#f59e0b',
        stepSize: 1,
      },
    },
  },
}

// ─── Methods ──────────────────────────────────────────────────
const formatCurrency = (val) =>
  new Intl.NumberFormat('fr-MA', { style: 'currency', currency: 'MAD', maximumFractionDigits: 0 }).format(val ?? 0)

const changePeriod = async (period) => {
  activePeriod.value = period
  await loadData()
}

const loadData = async () => {
  // Si les données sont injectées depuis le parent, les utiliser directement
  if (props.externalData) {
    rawData.value = props.externalData
    return
  }

  loading.value = true
  try {
    const { data } = await api.get('/admin/dashboard/revenue-chart', {
      params: { period: activePeriod.value },
    })
    rawData.value = data
  } catch (e) {
    console.error('RevenueChart: erreur chargement', e)
    rawData.value = []
  } finally {
    loading.value = false
  }
}

// ─── Watchers ─────────────────────────────────────────────────
watch(() => props.externalData, (val) => {
  if (val) rawData.value = val
}, { immediate: true })

// ─── Lifecycle ────────────────────────────────────────────────
onMounted(() => {
  if (!props.externalData) loadData()
})
</script>
