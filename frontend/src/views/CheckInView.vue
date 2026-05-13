<template>
  <div class="min-h-screen bg-[#FAF8F5] px-6 py-12 flex items-center justify-center">
    <div class="max-w-xl w-full mx-auto">
      
      <!-- Loading -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-20 gap-6">
        <div class="w-10 h-10 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">Signature de votre arrivée...</p>
      </div>

      <!-- Content -->
      <div v-else-if="reservation" class="animate-slide-up">
        <div class="bg-white rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden relative">
          
          <div class="h-1.5 bg-gradient-to-r from-accent/40 via-accent to-accent/40"></div>

          <div class="p-10 text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-8 border border-slate-100 relative group">
               <img src="@/assets/logo-premium.png" class="w-10 opacity-80 group-hover:opacity-100 transition-opacity" alt="GP Logo">
            </div>

            <p class="text-[10px] font-bold text-accent tracking-[0.4em] uppercase mb-4">Maison de Prestige</p>
            <h1 class="text-4xl font-serif text-primary mb-2">Bienvenue au Palace</h1>
            <p class="text-slate-400 text-sm font-light italic mb-10">Une parenthèse enchantée commence maintenant</p>

            <div class="bg-slate-50/50 rounded-2xl p-8 mb-10 border border-slate-100 text-left">
               <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Résident</p>
                  <p class="text-sm font-bold text-primary">{{ reservation.user?.name }}</p>
               </div>

               <div class="grid grid-cols-2 gap-8">
                  <div class="space-y-1">
                     <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Logement</p>
                     <p class="text-xs font-bold text-primary">N° {{ reservation.room?.number }} — {{ reservation.room?.room_type?.name }}</p>
                  </div>
                  <div class="space-y-1 text-right">
                     <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Période</p>
                     <p class="text-xs font-bold text-primary">{{ formatDate(reservation.check_in) }} — {{ formatDate(reservation.check_out) }}</p>
                  </div>
               </div>
            </div>

            <div class="space-y-4">
               <button @click="downloadReceipt" 
                       :disabled="downloading"
                       class="btn-premium w-full !py-5 !rounded-2xl shadow-xl shadow-primary/10 flex items-center justify-center gap-3">
                  <i v-if="downloading" class="las la-spinner animate-spin"></i>
                  <i v-else class="las la-file-pdf text-xl"></i>
                  <span>Télécharger mon Reçu Officiel</span>
               </button>
               
               <router-link to="/rooms" class="block">
                  <button class="btn-outline-premium w-full !py-5 !rounded-2xl hover:bg-slate-50">Explorer les Services</button>
               </router-link>
            </div>
          </div>

          <div class="bg-slate-50/50 py-6 text-center border-t border-slate-100">
             <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] flex items-center justify-center gap-3">
                <span class="w-1 h-1 rounded-full bg-accent"></span> Réf : {{ reservation.reference }} <span class="w-1 h-1 rounded-full bg-accent"></span>
             </p>
          </div>
        </div>
      </div>

      <div v-else class="text-center p-10 bg-white rounded-[3rem] shadow-xl border border-slate-100">
         <h2 class="text-2xl font-serif text-primary mb-4">Dossier introuvable</h2>
         <p class="text-slate-400 text-sm">Nous n'avons pas pu identifier votre réservation.</p>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios'

const route = useRoute()
const reservation = ref(null)
const loading = ref(true)
const downloading = ref(false)

const formatDate = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { day:'numeric', month:'short' }) : '—'

const fetchReservation = async () => {
  try {
    const { data } = await api.get(`/reservations/reference/${route.params.reference}`)
    reservation.value = data
  } catch (err) {
    console.error('Fetch error:', err)
  } finally {
    loading.value = false
  }
}

const downloadReceipt = async () => {
  if (downloading.value) return
  downloading.value = true
  try {
    const response = await api.get(`/reservations/${reservation.value.id}/receipt`, {
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `Recu_GRANDPALAIS_${reservation.value.reference}.pdf`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  } catch (err) {
    console.error('Download error:', err)
    alert('Désolé, une erreur est survenue lors du téléchargement.')
  } finally {
    downloading.value = false
  }
}

onMounted(fetchReservation)
</script>
