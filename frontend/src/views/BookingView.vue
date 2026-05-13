<template>
  <div class="bg-slate-50 min-h-screen pt-20">

    <!-- Header / Steps -->
    <div class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-20 z-40">
      <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="flex items-center gap-6">
          <button @click="router.back()" class="w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center text-primary hover:border-accent hover:text-accent transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          </button>
          <div>
            <p class="text-[10px] font-bold text-accent tracking-[0.3em] uppercase mb-1">Réservation Concierge</p>
            <h1 class="text-2xl font-serif text-primary">Finaliser votre séjour</h1>
          </div>
        </div>

        <!-- Steps Indicators -->
        <div class="flex items-center gap-4">
          <div v-for="(step, i) in steps" :key="i" class="flex items-center gap-4">
            <div class="flex flex-col items-center gap-1.5">
              <div class="w-8 h-8 rounded-xl flex items-center justify-center text-[10px] font-bold transition-all duration-500"
                   :class="currentStep >= i ? 'bg-primary text-white shadow-lg' : 'bg-slate-100 text-slate-400'">
                {{ i + 1 }}
              </div>
              <span class="text-[9px] font-bold uppercase tracking-widest whitespace-nowrap"
                    :class="currentStep >= i ? 'text-primary' : 'text-slate-300'">
                {{ step }}
              </span>
            </div>
            <div v-if="i < steps.length - 1" class="w-8 h-px bg-slate-100 mt-[-12px]"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">

        <!-- LEFT: Active Step Content -->
        <div class="lg:col-span-8">

          <!-- STEP 1: Dates & Voyageurs -->
          <div v-if="currentStep === 0" class="animate-slide-up">
            <div class="bg-white p-10 md:p-16 rounded-[3rem] shadow-sm border border-slate-100">
              <div class="mb-12">
                <h2 class="text-4xl font-serif text-primary mb-4">Dates & Voyageurs</h2>
                <div class="w-16 h-1 bg-accent mb-12"></div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-12">
                <div class="space-y-4">
                  <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Arrivée au Palais</label>
                  <input v-model="form.check_in" type="date" :min="today" class="input-booking" />
                </div>
                <div class="space-y-4">
                  <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Départ souhaité</label>
                  <input v-model="form.check_out" type="date" :min="form.check_in || today" class="input-booking" />
                </div>
                <div class="space-y-4">
                  <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nombre d'Adultes</label>
                  <select v-model="form.adults" class="input-booking appearance-none">
                    <option v-for="n in (selectedRoom?.room_type?.max_capacity || 6)" :key="n" :value="n">{{ n }} Adulte{{ n > 1 ? 's' : '' }}</option>
                  </select>
                </div>
                <div class="space-y-4">
                  <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Enfants</label>
                  <select v-model="form.children" class="input-booking appearance-none">
                    <option v-for="n in [0,1,2,3]" :key="n" :value="n">{{ n }} Enfant{{ n > 1 ? 's' : '' }}</option>
                  </select>
                </div>
              </div>

              <div class="space-y-4 mb-16">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Notes & Demandes Spéciales</label>
                <textarea v-model="form.special_requests" rows="4" 
                          placeholder="Ex: Arrivée tardive, allergie, décoration romantique..."
                          class="input-booking resize-none"></textarea>
              </div>

              <div v-if="!isAvailable && !checkingAvailability" class="mb-10 p-6 rounded-2xl bg-rose-50 border-l-4 border-rose-500 text-rose-600 text-sm italic animate-shake">
                Cette chambre n'est pas disponible pour les dates sélectionnées. Veuillez choisir une autre période ou une autre chambre.
              </div>

              <button @click="nextStep" :disabled="!canProceedStep1" 
                      class="btn-premium w-full py-6 rounded-3xl font-bold tracking-widest flex items-center justify-center gap-4 transition-all active:scale-95 disabled:opacity-30">
                <span v-if="checkingAvailability">VÉRIFICATION...</span>
                <span v-else>CONTINUER VERS LES SERVICES</span>
                <svg v-if="!checkingAvailability" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
              </button>
            </div>
          </div>

          <!-- STEP 2: Services -->
          <div v-if="currentStep === 1" class="animate-slide-up">
            <div class="bg-white p-10 md:p-16 rounded-[3rem] shadow-sm border border-slate-100">
              <div class="mb-12">
                <h2 class="text-4xl font-serif text-primary mb-4">La Signature Grand Palais</h2>
                <p class="text-slate-500 font-light italic">Sublimez votre séjour avec nos services exclusifs</p>
                <div class="w-16 h-1 bg-accent mt-8 mb-12"></div>
              </div>

              <!-- Services Groups -->
              <div v-if="loadingServices" class="flex flex-col items-center justify-center py-20 gap-4">
                <div class="w-10 h-10 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Orchestration des services...</p>
              </div>

              <div v-else class="space-y-16 mb-16">
                <div v-for="(group, category) in groupedServices" :key="category">
                  <div class="flex items-center gap-6 mb-8">
                    <h4 class="text-[10px] font-bold text-accent tracking-[0.3em] uppercase">{{ getCategoryLabel(category) }}</h4>
                    <div class="flex-1 h-px bg-slate-100"></div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div v-for="service in group" :key="service.id" 
                         @click="toggleService(service)"
                         class="group relative card-elegant !p-0 border-none overflow-hidden cursor-pointer transition-all duration-500"
                         :class="isServiceSelected(service.id) ? 'shadow-2xl ring-2 ring-accent scale-[1.02]' : 'hover:shadow-xl opacity-80 hover:opacity-100'">
                      
                      <div class="relative h-44 overflow-hidden">
                        <img :src="getServiceImage(service)" class="w-full h-full object-cover transition-all duration-1000 group-hover:scale-110" />
                        <div class="absolute inset-0 bg-primary/20 transition-all" :class="isServiceSelected(service.id) ? 'bg-accent/20' : ''"></div>
                        <div v-if="isServiceSelected(service.id)" class="absolute top-4 right-4 w-8 h-8 bg-accent rounded-full flex items-center justify-center text-white animate-fade-in shadow-lg">
                          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                        </div>
                        <div class="absolute bottom-4 left-4">
                           <span class="px-4 py-1.5 bg-white/95 backdrop-blur-md rounded-full text-[10px] font-bold text-primary shadow-sm uppercase tracking-widest">
                             {{ parseFloat(service.price).toFixed(0) }} MAD {{ service.price_type === 'per_night' ? '/n' : '' }}
                           </span>
                        </div>
                      </div>

                      <div class="p-6">
                        <h5 class="font-serif text-xl text-primary mb-2">{{ service.name }}</h5>
                        <p class="text-xs text-slate-500 font-light leading-relaxed truncate">{{ service.description }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex flex-col md:flex-row gap-6">
                <button @click="currentStep--" class="btn-outline-premium flex-1 py-5 !rounded-[2rem] font-bold tracking-widest">RETOUR</button>
                <button @click="nextStep" class="btn-premium flex-1 py-5 !rounded-[2rem] font-bold tracking-widest flex items-center justify-center gap-4">
                  CONTINUER VERS LE PAIEMENT
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
              </div>
            </div>
          </div>

          <!-- STEP 3: Paiement -->
          <div v-if="currentStep === 2" class="animate-slide-up">
            <div class="bg-white p-10 md:p-16 rounded-[3rem] shadow-sm border border-slate-100">
              <div class="mb-12">
                <h2 class="text-4xl font-serif text-primary mb-4">Paiement Sécurisé</h2>
                <div class="w-16 h-1 bg-accent mb-12"></div>
              </div>

              <div v-if="error" class="mb-10 p-6 rounded-2xl bg-rose-50 border-l-4 border-rose-500 text-rose-600 text-sm italic animate-shake">
                {{ error }}
              </div>

              <!-- Payment Methods -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                <div v-for="method in paymentMethods" :key="method.value" 
                     @click="form.payment_method = method.value"
                     class="group relative card-elegant flex items-center gap-6 cursor-pointer transition-all duration-300"
                     :class="form.payment_method === method.value ? 'ring-2 ring-accent bg-accent/5' : 'hover:bg-slate-50 opacity-70 hover:opacity-100'">
                  <div class="w-16 h-16 rounded-3xl bg-white shadow-sm flex items-center justify-center text-3xl text-accent transition-transform group-hover:scale-110">
                    <i :class="method.iconClass"></i>
                  </div>
                  <div class="flex-1">
                    <p class="font-bold text-primary text-sm mb-1">{{ method.label }}</p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ method.desc }}</p>
                  </div>
                  <div class="w-5 h-5 rounded-full border-2 transition-all flex items-center justify-center"
                       :class="form.payment_method === method.value ? 'border-accent bg-accent' : 'border-slate-200'">
                     <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                  </div>
                </div>
              </div>

              <transition name="slide-up">
                <div v-if="form.payment_method === 'card'" class="p-10 rounded-[2.5rem] bg-white shadow-xl border border-slate-100 mb-12 relative overflow-hidden group">
                  <div class="absolute -top-12 -right-12 w-48 h-48 bg-accent/10 rounded-full blur-3xl"></div>
                  
                  <div class="relative z-10 space-y-8">
                     <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4 ml-1">Détails de votre Carte Beaumont</label>
                        <div id="card-element" class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 min-h-[50px]">
                          <!-- Stripe Card Element will be injected here -->
                        </div>
                        <div id="card-errors" role="alert" class="text-rose-500 text-[10px] mt-2 font-bold uppercase tracking-widest"></div>
                     </div>
                  </div>
                </div>
              </transition>

              <!-- Promo Code -->
              <div class="mb-16">
                 <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4 ml-1">Invitation / Code Privilège</label>
                 <div class="flex gap-4">
                    <input v-model="form.promo_code" type="text" placeholder="Votre code..." class="input-booking flex-1" />
                    <button @click="checkPromo" class="px-8 bg-slate-100 text-[10px] font-bold text-primary tracking-widest uppercase rounded-2xl transition-all hover:bg-slate-200 active:scale-95">Appliquer</button>
                 </div>
                 <p v-if="promoApplied" class="text-xs text-emerald-600 mt-4 font-bold flex items-center gap-2 animate-bounce">
                    <span v-if="isGiftApplied" class="text-xl">🎁</span>
                    <span v-else class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> 
                    {{ isGiftApplied ? 'Cadeau de Fidélité Appliqué !' : 'Privilege appliqué' }} : -{{ promoDiscount }} MAD
                 </p>
              </div>

              <div class="flex flex-col md:flex-row gap-6">
                <button @click="currentStep--" class="btn-outline-premium flex-1 py-6 !rounded-[2.5rem] font-bold tracking-widest transition-all active:scale-95">RETOUR</button>
                <button @click="submitReservation" :disabled="submitting" 
                        class="btn-premium flex-1 py-6 !rounded-[2.5rem] font-bold tracking-widest flex items-center justify-center gap-4 shadow-xl transition-all active:scale-95 disabled:grayscale">
                  {{ submitting ? 'TRAITEMENT EN COURS...' : `PAYER ${totalPrice} MAD` }}
                  <svg v-if="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT: RECAP SIDEBAR -->
        <div class="lg:col-span-4">
          <div class="sticky top-40">
            <div class="bg-white p-10 rounded-[3rem] shadow-xl relative overflow-hidden border border-slate-100 group">
              <!-- Overlay effect -->
              <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-accent/10 rounded-full blur-3xl group-hover:bg-accent/20 transition-all"></div>
              
              <div class="relative z-10">
                <div class="flex items-center gap-4 mb-10 overflow-hidden">
                   <div class="w-1.5 h-10 bg-accent rounded-full"></div>
                   <h3 class="text-2xl font-serif text-primary">Votre Séjour</h3>
                </div>

                <!-- Room Preview -->
                <div class="rounded-3xl overflow-hidden mb-8 shadow-2xl aspect-video relative group/img">
                   <img :src="roomImage" class="w-full h-full object-cover transition-transform duration-1000 group-hover/img:scale-110" />
                   <div class="absolute inset-0 bg-gradient-to-t from-primary/60 to-transparent"></div>
                   <div class="absolute bottom-6 left-6">
                      <p class="text-white font-serif text-xl mb-1">Chambre {{ selectedRoom?.number }}</p>
                      <p class="text-white/80 text-[10px] font-bold uppercase tracking-widest">{{ selectedRoom?.room_type?.name }}</p>
                   </div>
                </div>

                <div class="space-y-6">
                   <!-- Dates Recap -->
                   <div v-if="nights > 0" class="p-6 rounded-3xl bg-slate-50 border border-slate-100 space-y-4">
                      <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 uppercase tracking-widest font-bold">Arrivée</span>
                        <span class="text-primary font-bold">{{ formatDate(form.check_in) }}</span>
                      </div>
                      <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 uppercase tracking-widest font-bold">Départ</span>
                        <span class="text-primary font-bold">{{ formatDate(form.check_out) }}</span>
                      </div>
                      <div class="flex justify-between items-center text-xs pt-4 border-t border-slate-200">
                        <span class="text-slate-400 uppercase tracking-widest font-bold">Durée</span>
                        <span class="text-accent font-bold">{{ nights }} Nuit{{ nights > 1 ? 's' : '' }}</span>
                      </div>
                   </div>

                   <!-- Price Details -->
                   <div class="space-y-4 py-6 border-y border-slate-100">
                      <div class="flex justify-between text-xs font-light">
                        <span class="text-slate-500 italic">Hébergement ({{ nights }} nuits)</span>
                        <span class="text-primary font-bold">{{ roomPrice }} MAD</span>
                      </div>
                      <div v-if="selectedServices.length > 0" class="space-y-2">
                        <div v-for="s in selectedServices" :key="s.id" class="flex justify-between text-[11px] font-light">
                          <span class="text-accent italic">✦ {{ s.name }}</span>
                          <span class="text-primary font-bold">{{ parseFloat(s.price).toFixed(0) }} MAD</span>
                        </div>
                      </div>
                      <div v-if="promoDiscount > 0" class="flex justify-between text-xs text-emerald-600 font-bold">
                        <span>Code Privilège Beaumont</span>
                        <span>-{{ promoDiscount }} MAD</span>
                      </div>
                   </div>

                   <!-- Grand Total -->
                   <div class="pt-4 flex items-end justify-between">
                      <div>
                         <p class="text-[10px] text-slate-400 uppercase tracking-[0.3em] font-bold mb-1">Montant Total</p>
                         <p class="text-4xl font-serif text-accent">{{ totalPrice }} MAD</p>
                      </div>
                      <div class="text-right">
                         <p class="text-[10px] text-slate-400 font-bold italic mb-1 uppercase tracking-widest">Taxes Incluses</p>
                         <p class="text-[10px] text-accent font-bold uppercase tracking-widest">Paiement Sécurisé</p>
                      </div>
                   </div>
                </div>

                <div class="mt-12 p-6 rounded-3xl bg-accent/10 border border-accent/20 flex items-center gap-6">
                   <div class="text-3xl text-accent">
                     <i class="las la-dove"></i>
                   </div>
                   <p class="text-[10px] text-accent font-bold uppercase tracking-widest leading-relaxed">Annulation gracieuse jusqu'à 24h avant votre arrivée.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useBookingStore } from '@/stores/booking'
import api from '@/api/axios'
import getStripe from '@/api/stripe'
import confetti from 'canvas-confetti'

const router       = useRouter()
const authStore    = useAuthStore()
const bookingStore = useBookingStore()

const currentStep     = ref(0)
const steps           = ['DATES & VOYAGEURS', 'SIGNATURE SERVICES', 'PAIEMENT']
const services        = ref([])
const loadingServices = ref(false)
const submitting      = ref(false)
const error           = ref('')
const promoApplied    = ref(false)
const promoDiscount   = ref(0)
const isGiftApplied   = ref(false)
const isAvailable     = ref(true)
const checkingAvailability = ref(false)
const today           = new Date().toISOString().split('T')[0]

const selectedRoom = computed(() => bookingStore.selectedRoom)

const form = reactive({
  check_in:         bookingStore.searchParams?.check_in  || '',
  check_out:        bookingStore.searchParams?.check_out || '',
  adults:           parseInt(bookingStore.searchParams?.adults) || 2,
  children:         parseInt(bookingStore.searchParams?.children) || 0,
  special_requests: '',
  promo_code:       '',
  payment_method:   'card',
  services:         [],
})

const initStripe = async () => {
  if (currentStep.value === 2 && form.payment_method === 'card') {
    setTimeout(async () => {
      if (!stripe) {
        stripe = await getStripe()
        elements = stripe.elements()
      }
      if (!cardElement) {
        cardElement = elements.create('card', {
          style: {
            base: {
              fontSize: '16px',
              color: '#0f172a',
              fontFamily: 'Inter, sans-serif',
              '::placeholder': { color: '#94a3b8' },
            },
          },
        })
        cardElement.on('change', (event) => {
          const displayError = document.getElementById('card-errors')
          if (displayError) displayError.textContent = event.error ? event.error.message : ''
        })
      }
      const el = document.getElementById('card-element')
      if (el) {
        cardElement.mount('#card-element')
        console.log('Stripe Element mounted successfully')
      } else {
        console.error('Stripe container #card-element not found')
      }
    }, 200) // Slightly longer delay to ensure DOM is ready
  }
}

import { watch } from 'vue'
watch(() => form.payment_method, (newVal) => {
  if (newVal === 'card') initStripe()
})

const checkAvailability = async () => {
  if (!form.check_in || !form.check_out || !selectedRoom.value) return
  
  checkingAvailability.value = true
  error.value = ''
  
  try {
    const { data } = await api.get('/rooms/search', {
      params: {
        check_in:  form.check_in,
        check_out: form.check_out,
        adults:    form.adults,
        children:  form.children
      }
    })
    
    // Check if current room is in the available list
    const available = data.data.some(r => Number(r.id) === Number(selectedRoom.value.id))
    isAvailable.value = available
    
    if (!available) {
      error.value = "Cette chambre n'est pas disponible pour ces dates"
    }
  } catch (e) {
    console.error('Error checking availability:', e)
  } finally {
    checkingAvailability.value = false
  }
}

watch([() => form.check_in, () => form.check_out, () => form.adults, () => form.children], () => {
  if (form.check_in && form.check_out) {
    checkAvailability()
  } else {
    isAvailable.value = true
  }
})

let stripe = null
let elements = null
let cardElement = null

const paymentMethods = [
  { value: 'card',       iconClass: 'las la-credit-card', label: 'Carte Beaumont VIP',    desc: 'Visa, Mastercard, Amex' },
  { value: 'on_arrival', iconClass: 'las la-gem',         label: 'Paiement au Palais',     desc: 'À régler lors du check-in' },
]

const groupedServices = computed(() => {
  if (!Array.isArray(services.value)) return {}
  return services.value.reduce((acc, s) => {
    const cat = s.category || 'other'
    if (!acc[cat]) acc[cat] = []
    acc[cat].push(s)
    return acc
  }, {})
})

const selectedServices = computed(() => {
  if (!Array.isArray(services.value)) return []
  return services.value.filter(s => form.services.some(fs => fs.id === s.id))
})

const nights = computed(() => {
  if (!form.check_in || !form.check_out) return 0
  const d1 = new Date(form.check_in)
  const d2 = new Date(form.check_out)
  const diff = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24))
  return diff > 0 ? diff : 0
})

const roomPrice = computed(() => {
  if (!selectedRoom.value) return '0'
  
  // Try to get the most accurate price
  const basePrice = parseFloat(
    selectedRoom.value.period_price ?? 
    selectedRoom.value.price_override ?? 
    selectedRoom.value.room_type?.base_price ?? 
    0
  )
  
  // If we already have a period_price (total for the stay), use it directly
  if (selectedRoom.value.period_price) {
    return basePrice.toFixed(0)
  }
  
  // Otherwise multiply by nights
  return (basePrice * (nights.value || 1)).toFixed(0)
})

const servicesTotal = computed(() => {
  const total = selectedServices.value.reduce((sum, s) => sum + parseFloat(s.price || 0), 0)
  return total.toFixed(0)
})

const totalPrice = computed(() => {
  const room = parseFloat(roomPrice.value) || 0
  const services = parseFloat(servicesTotal.value) || 0
  const promo = parseFloat(promoDiscount.value) || 0
  
  const total = room + services - promo
  return Math.max(0, total).toFixed(0)
})

const roomImage = computed(() => {
  if (selectedRoom.value?.images?.length) {
    const img = selectedRoom.value.images[0]
    return img.url ?? (img.path ? `http://localhost:8000/storage/${img.path}` : null)
  }
  const images = {
    simple:    'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=900&q=80',
    double:    'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=900&q=80',
    suite:     'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=900&q=80',
    familiale: 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=900&q=80',
  }
  return images[selectedRoom.value?.room_type?.slug] || images.suite
})

const canProceedStep1 = computed(() =>
  form.check_in && form.check_out && nights.value > 0 && form.adults >= 1 && isAvailable.value && !checkingAvailability.value
)

const isServiceSelected = (id) => form.services.some(s => s.id === id)

const toggleService = (service) => {
  const idx = form.services.findIndex(s => s.id === service.id)
  if (idx === -1) {
    form.services.push({ id: service.id, quantity: 1, price: service.price })
  } else {
    form.services.splice(idx, 1)
  }
}

const getCategoryLabel = (cat) => ({
  restaurant: 'Gastronomie Beaumont',
  transport:  'Mobilité & Prestige',
  wellness:   'Bien-être Holistique',
  room:       'Confort Privé',
  sport:      'Sport & Performance',
  other:      'Services Exclusifs',
}[cat] ?? cat)

const getServiceImage = (service) => {
  if (service.image) {
    const timestamp = service.updated_at ? new Date(service.updated_at).getTime() : new Date().getTime()
    return `http://localhost:8000/storage/${service.image}?t=${timestamp}`
  }
  const catImages = {
    restaurant: 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=500&q=80',
    transport:  'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=500&q=80',
    wellness:   'https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=500&q=80',
    room:       'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=500&q=80',
    sport:      'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=500&q=80',
    other:      'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500&q=80',
  }
  return catImages[service.category] || catImages.other
}

const formatDate = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { day:'numeric', month:'short', year:'numeric' }) : '—'

const formatCard = () => {
  cardForm.number = cardForm.number.replace(/\D/g, '').replace(/(\d{4})/g, '$1 ').trim()
}

const checkPromo = async () => {
  if (!form.promo_code) return
  try {
    const { data } = await api.post('/reservations/check-promo', { code: form.promo_code })
    promoDiscount.value = data.type === 'percentage'
      ? parseFloat((parseFloat(roomPrice.value) * data.value / 100).toFixed(2))
      : data.value
    
    promoApplied.value = true
    isGiftApplied.value = data.value === 50 && data.type === 'percentage'

    if (isGiftApplied.value) {
      triggerConfetti()
    }
  } catch {
    error.value = 'Code Privilège introuvable ou expiré'
    promoApplied.value = false
    promoDiscount.value = 0
    isGiftApplied.value = false
  }
}

const triggerConfetti = () => {
  const duration = 3 * 1000
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

const nextStep = async () => { 
  if (currentStep.value === 0) {
    await checkAvailability()
    if (!isAvailable.value) return
  }
  window.scrollTo({ top: 0, behavior: 'smooth' })
  currentStep.value++ 
  initStripe()
}

const submitReservation = async () => {
  if (!authStore.isLoggedIn) {
     router.push({ name: 'login', query: { redirect: '/booking' } })
     return
  }
  error.value      = ''
  submitting.value = true
  try {
    // 1. Create Reservation
    const { data: reservation } = await api.post('/reservations', {
      room_id:          selectedRoom.value.id,
      check_in:         form.check_in,
      check_out:        form.check_out,
      adults:           form.adults,
      children:         form.children,
      special_requests: form.special_requests,
      promo_code:       form.promo_code || undefined,
      services:         form.services.map(s => ({ id: s.id, quantity: 1, price: s.price })),
      payment_method:   form.payment_method,
    })

    // 2. Handle Stripe Payment if needed
    if (form.payment_method === 'card') {
      // Get Payment Intent
      const { data: intent } = await api.post(`/payments/${reservation.id}/intent`)
      
      // Confirm Card Payment
      const result = await stripe.confirmCardPayment(intent.client_secret, {
        payment_method: {
          card: cardElement,
          billing_details: {
            name: authStore.user?.name,
            email: authStore.user?.email,
          },
        },
      })

      if (result.error) {
        error.value = result.error.message
        submitting.value = false
        return
      }

      // Confirm Payment on Backend
      await api.post(`/payments/${reservation.id}/confirm`, {
        transaction_id: result.paymentIntent.id,
      })
    }

    router.push({ name: 'booking-confirm', params: { id: reservation.id } })
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Une erreur est survenue lors de l\'orchestration de votre séjour.'
    submitting.value = false
  }
}

onMounted(async () => {
  if (!selectedRoom.value) { router.push('/rooms'); return }
  loadingServices.value = true
  try {
    const { data } = await api.get('/services')
    services.value = data
    if (form.check_in && form.check_out) {
      await checkAvailability()
    }
  } finally {
    loadingServices.value = false
  }
})
</script>

<style scoped>
.input-booking {
  @apply w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 text-primary text-sm focus:outline-none focus:border-accent focus:bg-white transition-all shadow-sm;
}
.animate-slide-up {
  animation: slideUp 0.6s ease-out forwards;
}
.animate-shake {
  animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes shake {
  10%, 90% { transform: translate3d(-1px, 0, 0); }
  20%, 80% { transform: translate3d(2px, 0, 0); }
  30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
  40%, 60% { transform: translate3d(4px, 0, 0); }
}
</style>
