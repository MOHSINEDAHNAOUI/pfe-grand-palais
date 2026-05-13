<template>
  <div class="min-h-screen bg-[#FAF8F5] px-6 py-6 flex items-center justify-center">
    <div class="max-w-4xl w-full mx-auto">
      
      <!-- Loading State -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-20 gap-6">
        <div class="w-12 h-12 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">Orchestration de votre séjour...</p>
      </div>

      <div v-else-if="reservation" class="animate-slide-up">
        <div class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 overflow-hidden relative">
          
          <div class="h-1 bg-gradient-to-r from-accent/40 via-accent to-accent/40"></div>

          <div class="grid grid-cols-1 lg:grid-cols-12">
            
            <!-- Left Side: Status & Actions -->
            <div class="lg:col-span-7 p-8 md:p-10 border-r border-slate-50 flex flex-col justify-between">
              <div>
                <div class="relative inline-block mb-6">
                  <div class="w-16 h-16 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500 relative z-10">
                    <i class="las la-check-circle text-3xl"></i>
                  </div>
                </div>

                <p class="text-[9px] font-bold text-accent tracking-[0.4em] uppercase mb-2">Confirmation</p>
                <h1 class="text-3xl font-serif text-primary mb-5 leading-tight">
                  Votre séjour d'exception <br/> <span class="italic text-slate-400">est réservé</span>
                  <span v-if="isGift" class="ml-2 inline-block animate-bounce">🎁</span>
                </h1>

                <div class="mb-6 p-4 rounded-xl bg-slate-50/50 border border-slate-100 flex items-center gap-4">
                  <i class="las la-envelope-open-text text-xl text-accent"></i>
                  <div class="text-left">
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Notification envoyée</p>
                    <p class="text-xs text-primary font-medium">{{ reservation.user?.email }}</p>
                  </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-8">
                  <router-link to="/dashboard/reservations" class="w-full">
                    <button class="btn-premium w-full !py-3.5 !rounded-xl !text-[9px] shadow-lg shadow-primary/5">Mes Séjours</button>
                  </router-link>
                  <router-link to="/" class="w-full">
                    <button class="btn-outline-premium w-full !py-3.5 !rounded-xl !text-[9px]">Accueil</button>
                  </router-link>
                </div>
              </div>

              <!-- Assistance integrated -->
              <div class="pt-6 border-t border-slate-50">
                <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest mb-2">Conciergerie 24/7</p>
                <div class="flex items-center gap-4 text-[10px] text-primary">
                  <span class="flex items-center gap-1.5"><i class="las la-phone text-accent"></i> +33 1 00 00 00 00</span>
                  <span class="flex items-center gap-1.5"><i class="las la-envelope text-accent"></i> concierge@grandpalais.fr</span>
                </div>
              </div>
            </div>

            <!-- Right Side: The Receipt -->
            <div class="lg:col-span-5 bg-slate-50/20 p-8 md:p-10 flex flex-col justify-between">
              <div>
                <div class="mb-6 pb-4 border-b border-slate-100">
                  <div class="flex items-center justify-between mb-1">
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.2em]">Réf. Dossier</p>
                    <div class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 text-[8px] font-bold uppercase rounded border border-emerald-500/20">Confirmé</div>
                  </div>
                  <p class="text-lg font-mono font-bold text-primary tracking-tighter">{{ reservation.reference }}</p>
                </div>

                <div class="space-y-4 mb-8">
                  <div class="space-y-0.5">
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Hébergement</p>
                    <p class="text-xs font-bold text-primary">N° {{ reservation.room?.number }} — {{ reservation.room?.room_type?.name }}</p>
                  </div>
                  <div class="space-y-0.5">
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Dates de séjour</p>
                    <p class="text-xs font-bold text-primary">{{ formatDate(reservation.check_in) }} — {{ formatDate(reservation.check_out) }}</p>
                  </div>
                  <div class="flex justify-between items-center bg-white/50 p-3 rounded-lg border border-slate-100">
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Règlement</p>
                    <div class="flex items-center gap-1.5 text-primary font-bold text-[10px]">
                      <i :class="reservation.payment_method === 'card' ? 'las la-credit-card' : 'las la-gem'"></i>
                      <span>{{ reservation.payment_method === 'card' ? 'Carte Privilège' : 'Au Comptoir' }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <div>
                <div class="pt-5 border-t border-slate-200">
                  <p class="text-[8px] font-bold text-accent uppercase tracking-[0.3em] mb-1.5">Montant Total</p>
                  <div class="flex items-baseline gap-2">
                    <p class="text-3xl font-serif text-primary">{{ parseFloat(reservation.total_price || 0).toFixed(0) }} MAD</p>
                    <p class="text-[8px] text-slate-400 italic">Taxes incluses</p>
                  </div>
                </div>

                <div class="mt-6 flex justify-center">
                   <div class="bg-white p-2 rounded-lg shadow-sm border border-slate-100">
                     <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=${reservation.reference}&color=0f172a`" 
                          class="w-16 h-16 opacity-40 hover:opacity-100 transition-opacity" 
                          alt="Booking QR" />
                   </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-20 bg-white rounded-[3rem] shadow-xl border border-slate-100">
        <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-6 text-2xl text-slate-200">
           <i class="las la-search-location"></i>
        </div>
        <h2 class="text-2xl font-serif text-primary mb-4">Réservation introuvable</h2>
        <p class="text-slate-500 text-sm mb-10 px-10">Désolé, nous ne parvenons pas à retrouver votre dossier.</p>
        <router-link to="/rooms">
          <button class="btn-premium px-10">Explorer nos Chambres</button>
        </router-link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios'
import confetti from 'canvas-confetti'

const route       = useRoute()
const reservation = ref(null)
const loading     = ref(true)

const formatDate = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { day:'numeric', month:'short', year:'numeric' }) : '—'

const isGift = computed(() => {
  if (!reservation.value) return false
  return reservation.value.discount_amount > 0 && 
         Math.abs(reservation.value.discount_amount - (reservation.value.room_price * 0.5)) < 1
})

const triggerConfetti = () => {
  const duration = 4 * 1000
  const animationEnd = Date.now() + duration
  const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 }

  const randomInRange = (min, max) => Math.random() * (max - min) + min

  const interval = setInterval(function() {
    const timeLeft = animationEnd - Date.now()

    if (timeLeft <= 0) {
      return clearInterval(interval)
    }

    const particleCount = 50 * (timeLeft / duration)
    confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } })
    confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } })
  }, 250)
}

onMounted(async () => {
  try {
    // Artificial small delay for premium feel of orchestration
    await new Promise(resolve => setTimeout(resolve, 800))
    const { data } = await api.get(`/reservations/${route.params.id}`)
    reservation.value = data
    
    if (isGift.value) {
      triggerConfetti()
    }
  } catch (e) {
    console.error('Erreur:', e)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.animate-slide-up {
  animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
