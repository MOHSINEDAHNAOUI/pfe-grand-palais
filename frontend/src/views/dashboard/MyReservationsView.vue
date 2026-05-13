<template>
  <div class="max-w-6xl mx-auto px-6 py-16">
    
    <!-- HEADER -->
    <div class="mb-12 animate-fade-in">
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
        <div>
          <p class="text-[10px] font-bold text-accent tracking-[0.4em] uppercase mb-4">Accès Résident</p>
          <h1 class="text-5xl font-serif text-primary">Mes Réservations</h1>
        </div>
        
        <!-- Filters (Segmented Control style) -->
        <div class="flex p-1.5 bg-slate-100 rounded-2xl overflow-x-auto no-scrollbar">
          <button v-for="f in statusFilters" :key="f.value"
                  @click="activeFilter = f.value"
                  class="text-[10px] font-bold tracking-widest uppercase px-6 py-3 rounded-xl transition-all whitespace-nowrap"
                  :class="activeFilter === f.value 
                    ? 'bg-white text-primary shadow-sm' 
                    : 'text-slate-400 hover:text-primary'">
            {{ f.label }}
          </button>
        </div>
      </div>
      <div class="h-px bg-slate-200 mt-10"></div>
    </div>

    <!-- LOADING STATE -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-32 gap-6">
      <div class="w-12 h-12 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
      <p class="text-[10px] font-bold text-slate-400 tracking-[0.3em] uppercase">Synchronisation avec la conciergerie...</p>
    </div>

    <!-- EMPTY STATE -->
    <div v-else-if="!filteredReservations.length" class="animate-slide-up">
      <div class="bg-white rounded-[3rem] p-16 md:p-32 text-center border border-slate-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -top-24 -left-24 w-64 h-64 bg-accent/5 rounded-full blur-3xl group-hover:bg-accent/10 transition-all"></div>
        
        <div class="relative z-10 flex flex-col items-center">
          <div class="text-6xl mb-10 transform transition-transform group-hover:scale-110 duration-700">⚜️</div>
          <h2 class="text-4xl font-serif text-primary mb-6">Aucun séjour planifié</h2>
          <p class="text-slate-500 font-light max-w-md mx-auto leading-relaxed mb-12">
            Le Grand Palais n'attend que votre visite. Explorez nos suites d'exception et réservez votre prochain sanctuaire.
          </p>
          <router-link to="/rooms">
            <button class="btn-premium rounded-full px-12 py-5">Découvrir les Collections</button>
          </router-link>
        </div>
      </div>
    </div>

    <!-- RESERVATION LIST -->
    <div v-else class="grid grid-cols-1 gap-8 animate-slide-up">
      <div v-for="r in filteredReservations" :key="r.id"
           class="group bg-white rounded-[2.5rem] overflow-hidden border border-slate-100 shadow-sm hover:shadow-2xl transition-all duration-700 flex flex-col md:flex-row">
        
        <!-- Room Preview -->
        <div class="md:w-80 h-64 md:h-auto overflow-hidden relative">
          <img :src="getRoomImage(r.room?.room_type?.slug)"
               class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
          <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
          
          <div class="absolute top-6 left-6">
             <span class="px-4 py-1.5 bg-white/95 backdrop-blur-md rounded-full text-[9px] font-bold text-primary shadow-sm uppercase tracking-widest border border-slate-100">
               N° {{ r.room?.number }}
             </span>
          </div>
        </div>

        <!-- Reservation Intel -->
        <div class="flex-1 p-10 flex flex-col justify-between">
          <div>
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-6">
              <div>
                <h3 class="text-2xl font-serif text-primary mb-2 group-hover:text-accent transition-colors">{{ r.room?.room_type?.name }}</h3>
                <div class="flex items-center gap-3">
                   <span class="text-[10px] font-bold text-accent tracking-widest uppercase">{{ r.reference }}</span>
                   <div class="w-1 h-1 rounded-full bg-slate-300"></div>
                   <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ formatDate(r.created_at) }}</span>
                </div>
              </div>
              
              <div class="self-start px-5 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest flex items-center gap-2"
                   :style="statusStyle(r.status)">
                 <div class="w-1.5 h-1.5 rounded-full" :style="{ background: 'currentColor' }"></div>
                 {{ statusLabel(r.status) }}
              </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-10 pt-8 border-t border-slate-50">
               <div>
                  <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mb-1">Arrivée</p>
                  <p class="text-sm font-bold text-primary">{{ formatDate(r.check_in) }}</p>
               </div>
               <div>
                  <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mb-1">Départ</p>
                  <p class="text-sm font-bold text-primary">{{ formatDate(r.check_out) }}</p>
               </div>
               <div>
                  <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mb-1">Voyageurs</p>
                  <p class="text-sm font-bold text-primary">{{ r.adults }} Adulte{{ r.adults > 1 ? 's' : '' }}</p>
               </div>
               <div>
                  <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mb-1">Total</p>
                  <p class="text-sm font-bold text-accent">{{ r.total_price?.toFixed(0) }} MAD</p>
               </div>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-4">
             <router-link :to="{ name: 'booking-confirm', params: { id: r.id } }">
               <button class="px-8 py-3 bg-slate-50 text-[10px] font-bold text-primary tracking-widest uppercase rounded-xl hover:bg-slate-100 transition-all active:scale-95">Détails du Séjour</button>
             </router-link>
             
             <button v-if="r.status === 'checked_out' && !r.review"
                     @click="openReview(r)"
                     class="px-8 py-3 bg-accent text-white text-[10px] font-bold tracking-widest uppercase rounded-xl hover:bg-primary transition-all active:scale-95 shadow-lg shadow-accent/10">
                Laisser un avis
             </button>

             <button v-if="['pending', 'confirmed'].includes(r.status)"
                     @click="cancelReservation(r)"
                     class="px-8 py-3 text-[10px] font-bold text-rose-500 tracking-widest uppercase rounded-xl hover:bg-rose-50 transition-all active:scale-95 border border-transparent hover:border-rose-100">
                Annuler la Réservation
             </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Review Modal -->
    <ReviewModal v-model="showReviewModal" :reservation="selectedForReview" @success="handleReviewSuccess" />

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/api/axios'
import ReviewModal from '@/components/common/ReviewModal.vue'

const reservations = ref([])
const loading      = ref(true)
const activeFilter = ref('all')

const showReviewModal    = ref(false)
const selectedForReview  = ref(null)

const statusFilters = [
  { value: 'all',        label: 'Historique Complet' },
  { value: 'pending',    label: 'En Attente' },
  { value: 'confirmed',  label: 'Confirmées' },
  { value: 'checked_in', label: 'Séjour en cours' },
  { value: 'checked_out',label: 'Terminées' },
  { value: 'cancelled',  label: 'Annulées' },
]

const filteredReservations = computed(() => {
  if (activeFilter.value === 'all') return reservations.value
  return reservations.value.filter(r => r.status === activeFilter.value)
})

const getRoomImage = (slug) => {
  const images = {
    simple:    'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=500&q=80',
    double:    'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=500&q=80',
    suite:     'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=500&q=80',
    familiale: 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=500&q=80',
  }
  return images[slug] || images.suite
}

const formatDate  = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { day:'numeric', month:'short', year:'numeric' }) : '—'
const statusLabel = (s) => ({ pending: 'Validation Concierge', confirmed: 'Confirmée', checked_in: 'Résident Actuel', checked_out: 'Ancien Résident', cancelled: 'Résiliation', no_show: 'No-show' }[s] ?? s)
const statusStyle = (s) => ({
  pending:     'background: #FFFBEB; color: #D97706;',
  confirmed:   'background: #EFF6FF; color: #2563EB;',
  checked_in:  'background: #F0FDF4; color: #16A34A;',
  checked_out: 'background: #F5F3FF; color: #7C3AED;',
  cancelled:   'background: #FEF2F2; color: #DC2626;',
  no_show:     'background: #F9FAFB; color: #6B7280;',
}[s] ?? '')

const cancelReservation = async (r) => {
  if (!confirm('Souhaitez-vous annuler l\'orchestration de votre séjour ? Cette action est irréversible.')) return
  try {
    await api.post(`/reservations/${r.id}/cancel`)
    r.status = 'cancelled'
  } catch (err) {
    alert('Une erreur est survenue lors de l\'annulation.')
  }
}

const openReview = (r) => {
  selectedForReview.value = r
  showReviewModal.value = true
}

const handleReviewSuccess = () => {
  // Optionnel: recharger les réservations pour mettre à jour le bouton (si r.review est utilisé)
  const idx = reservations.value.findIndex(res => res.id === selectedForReview.value.id)
  if (idx !== -1) reservations.value[idx].review = true
}

onMounted(async () => {
  try {
    const { data } = await api.get('/reservations')
    reservations.value = data.data ?? data
  } catch (err) {
    console.error('Fetch reservations error', err)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

.animate-fade-in {
  animation: fadeIn 0.8s ease-out forwards;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-slide-up {
  animation: slideUp 0.8s ease-out forwards;
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
