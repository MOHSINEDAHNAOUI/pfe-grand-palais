<template>
  <div class="bg-slate-50 min-h-screen pt-20">

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center h-[60vh]">
      <div class="flex flex-col items-center gap-6">
        <div class="w-12 h-12 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
        <p class="text-xs font-bold tracking-[0.3em] text-slate-400 uppercase">Chargement de l'exception...</p>
      </div>
    </div>

    <div v-else-if="room" class="max-w-7xl mx-auto px-6 py-12">

      <!-- Breadcrumb -->
      <nav class="flex items-center gap-3 text-[10px] font-bold tracking-widest uppercase mb-10 text-slate-400">
        <router-link to="/" class="hover:text-accent transition-colors">Accueil</router-link>
        <span class="opacity-30">/</span>
        <router-link to="/rooms" class="hover:text-accent transition-colors">Chambres</router-link>
        <span class="opacity-30">/</span>
        <span class="text-primary">Chambre {{ room.number }}</span>
      </nav>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">

        <!-- LEFT: Gallery & Details -->
        <div class="lg:col-span-8">
          
          <!-- Main Display -->
          <div class="relative group rounded-[2.5rem] overflow-hidden bg-slate-200 aspect-[16/10] mb-6 shadow-2xl">
            <transition name="fade" mode="out-in">
              <img :key="activeImage" :src="activeImage" :alt="'Chambre ' + room.number"
                   class="w-full h-full object-cover" />
            </transition>
            
            <!-- Floating Navigation -->
            <div class="absolute inset-x-8 top-1/2 -translate-y-1/2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity duration-500">
              <button @click="prevImage" class="w-12 h-12 rounded-full bg-white/95 backdrop-blur-md flex items-center justify-center text-primary shadow-xl hover:scale-110 active:scale-95 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <button @click="nextImage" class="w-12 h-12 rounded-full bg-white/95 backdrop-blur-md flex items-center justify-center text-primary shadow-xl hover:scale-110 active:scale-95 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>

            <!-- Badges -->
            <div class="absolute top-8 left-8 flex gap-3">
              <span class="px-4 py-1.5 bg-accent text-white text-[10px] font-bold tracking-widest uppercase rounded-full shadow-lg">
                {{ room.room_type?.name }}
              </span>
              <span v-if="room.view" class="px-4 py-1.5 bg-white/90 backdrop-blur-md text-primary text-[10px] font-bold tracking-widest uppercase rounded-full shadow-lg">
                Vue {{ room.view }}
              </span>
            </div>

            <!-- Image Counter -->
            <div class="absolute bottom-8 right-8 px-4 py-2 bg-black/40 backdrop-blur-md text-white/90 text-[10px] font-bold tracking-[0.2em] rounded-full">
              {{ activeIndex + 1 }} / {{ allImages.length || 1 }}
            </div>
          </div>

          <!-- Thumbnails Grid -->
          <div v-if="allImages.length > 1" class="flex gap-4 mb-16 overflow-x-auto pb-4 scrollbar-hide">
            <button v-for="(img, i) in allImages" :key="i"
                    @click="activeIndex = i"
                    class="relative shrink-0 w-24 h-20 rounded-2xl overflow-hidden transition-all duration-300"
                    :class="i === activeIndex ? 'ring-2 ring-accent scale-105' : 'opacity-60 grayscale hover:grayscale-0 hover:opacity-100'">
              <img :src="img" class="w-full h-full object-cover" />
            </button>
          </div>

          <!-- Room Info Header -->
          <div class="mb-16">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-10">
              <div>
                <p class="text-accent font-bold tracking-[0.3em] uppercase text-xs mb-4">Une immersion dans le luxe</p>
                <h1 class="text-5xl md:text-6xl font-serif text-primary leading-tight">Chambre {{ room.number }}</h1>
              </div>
              <div class="flex flex-wrap gap-8">
                <div class="text-center">
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Surface</p>
                  <p class="text-xl font-serif text-primary">{{ room.surface }} m²</p>
                </div>
                <div class="text-center">
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Capacité</p>
                  <p class="text-xl font-serif text-primary">{{ room.room_type?.max_capacity }} pers.</p>
                </div>
                <div class="text-center">
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Étage</p>
                  <p class="text-xl font-serif text-primary">{{ room.floor }}</p>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
              <div class="space-y-6">
                <h3 class="text-2xl font-serif text-primary">L'Atmosphère</h3>
                <p class="text-lg text-slate-600 font-light leading-relaxed italic">
                  {{ room.room_type?.description }}
                </p>
                <div class="flex items-center gap-6 py-6 border-y border-slate-100 italic text-slate-400 text-sm">
                   <span class="flex items-center gap-2">
                     <span class="w-1.5 h-1.5 rounded-full" :class="room.smoking ? 'bg-amber-400' : 'bg-emerald-400'"></span>
                     {{ room.smoking ? 'Fumeur autorisé' : 'Espace non-fumeur' }}
                   </span>
                   <span class="flex items-center gap-2">
                     <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                     Conciergerie 24/7
                   </span>
                </div>
              </div>

              <!-- Rating / Summary box -->
              <div v-if="room.average_rating" class="p-8 rounded-[2.5rem] bg-white border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Note des voyageurs</p>
                  <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-serif text-primary">{{ room.average_rating }}</span>
                    <span class="text-slate-300 text-xl">/ 5</span>
                  </div>
                </div>
                <div class="text-right">
                   <div class="flex justify-end gap-1 mb-2">
                     <span v-for="n in 5" :key="n" class="text-sm" :class="n <= Math.round(room.average_rating) ? 'text-accent' : 'text-slate-200'">★</span>
                   </div>
                   <p class="text-[10px] font-bold text-accent uppercase tracking-widest">Basé sur {{ approvedReviews.length }} avis</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Équipements Moderne -->
          <div class="mb-20">
            <h2 class="text-3xl font-serif text-primary mb-10">L'Excellence du Confort</h2>
            
            <div v-if="!room.amenities?.length" class="p-8 rounded-[2rem] bg-slate-100 text-center text-slate-400 text-sm italic">
               Détails des équipements en cours de mise à jour.
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-10">
               <div v-for="(group, cat) in amenityGroups" :key="cat" class="card-elegant p-8 border-none bg-white/50">
                 <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 rounded-2xl bg-white shadow-sm flex items-center justify-center text-xl text-accent">
                      <i :class="getCatIcon(cat)"></i>
                    </div>
                    <h4 class="text-[10px] font-bold text-slate-400 tracking-[0.2em] uppercase">{{ catLabel(cat) }}</h4>
                 </div>
                 <div class="grid grid-cols-1 gap-y-3">
                   <div v-for="a in group" :key="a.id" class="flex items-center gap-3 text-sm text-slate-600">
                     <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                     {{ a.name }}
                   </div>
                 </div>
               </div>
            </div>
          </div>

          <!-- Avis Premium -->
          <div class="mb-20">
            <div class="flex items-center justify-between mb-10">
               <h2 class="text-3xl font-serif text-primary">Leurs Moments</h2>
               <button v-if="approvedReviews.length > 3" class="text-[10px] font-bold text-accent tracking-[0.2em] uppercase hover:underline">Voir tout</button>
            </div>

            <div v-if="!approvedReviews.length" class="p-12 text-center rounded-[3rem] border-2 border-dashed border-slate-200">
               <p class="text-slate-400 text-sm font-light italic">Soyez le premier à partager votre expérience au Grand Palais.</p>
            </div>
            <div v-else class="space-y-6">
               <div v-for="review in approvedReviews.slice(0, 3)" :key="review.id" 
                    class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all hover:shadow-md">
                 <div class="flex items-center gap-4 mb-6">
                   <img :src="`https://ui-avatars.com/api/?name=${review.user?.name}&size=44&background=0f172a&color=ffffff`"
                        class="w-12 h-12 rounded-2xl object-cover" />
                   <div class="flex-1">
                     <p class="font-bold text-primary text-sm">{{ review.user?.name }}</p>
                     <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ formatDate(review.created_at) }}</p>
                   </div>
                   <div class="flex gap-1">
                     <span v-for="n in 5" :key="n" class="text-xs" :class="n <= review.rating ? 'text-accent' : 'text-slate-100'">★</span>
                   </div>
                 </div>
                 <p class="text-slate-600 text-sm leading-relaxed font-light italic border-l-2 border-accent/20 pl-6">
                   "{{ review.comment }}"
                 </p>
               </div>
            </div>
          </div>
        </div>

        <!-- RIGHT: Professional Booking Widget -->
        <div class="lg:col-span-4">
          <div class="sticky top-24">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-100 relative overflow-hidden group">
              <!-- Background effect -->
              <div class="absolute -top-12 -right-12 w-32 h-32 bg-accent/10 rounded-full blur-3xl transition-all group-hover:bg-accent/20"></div>
              
              <div class="relative z-10 text-center mb-8 border-b border-slate-100 pb-8">
                <p class="text-[10px] font-bold text-accent tracking-[0.3em] uppercase mb-3">Tarif Privilège</p>
                <div class="flex items-baseline justify-center gap-2">
                  <span class="text-5xl font-serif text-primary">{{ formattedPrice }} MAD</span>
                  <span class="text-slate-400 text-xs font-light">/ nuit</span>
                </div>
              </div>

              <div class="space-y-6 mb-8">
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Arrivée</label>
                    <div class="relative">
                      <input v-model="bookForm.check_in" type="date" :min="today"
                             class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm text-primary focus:outline-none focus:border-accent transition-all" />
                    </div>
                  </div>
                  <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Départ</label>
                    <div class="relative">
                      <input v-model="bookForm.check_out" type="date" :min="bookForm.check_in || today"
                             class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm text-primary focus:outline-none focus:border-accent transition-all" />
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Adultes</label>
                    <input v-model.number="bookForm.adults" type="number" min="1" :max="room.room_type?.max_capacity"
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm text-primary focus:outline-none focus:border-accent transition-all" />
                  </div>
                  <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Enfants</label>
                    <input v-model.number="bookForm.children" type="number" min="0" max="4"
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm text-primary focus:outline-none focus:border-accent transition-all" />
                  </div>
                </div>
              </div>

              <!-- Summary Recap if dates are valid -->
              <div v-if="nights > 0" class="mb-8 p-6 rounded-2xl bg-slate-50 border border-slate-100 animate-fade-in">
                <div class="flex justify-between text-xs text-slate-500 mb-2">
                  <span>{{ formattedPrice }} MAD x {{ nights }} nuits</span>
                  <span class="font-bold text-primary">{{ totalPrice }} MAD</span>
                </div>
                <div class="flex justify-between text-sm font-bold text-accent pt-4 mt-2 border-t border-slate-200">
                  <span>Total estimé</span>
                  <span>{{ totalPrice }} MAD</span>
                </div>
              </div>

              <button @click="book" :disabled="!canBook"
                      class="btn-gold-premium w-full py-5 rounded-2xl font-bold flex items-center justify-center gap-3 transition-all active:scale-95 disabled:opacity-30 disabled:grayscale disabled:cursor-not-allowed shadow-lg shadow-accent/20">
                <span>{{ canBook ? 'RÉSERVER CETTE CHAMBRE' : 'CHOISISSEZ VOS DATES' }}</span>
                <svg v-if="canBook" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
              </button>

              <!-- Safety notes -->
              <div class="mt-8 space-y-3">
                <div class="flex items-center gap-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                  <span class="w-1 h-1 rounded-full bg-emerald-400"></span> Annulation flexible
                </div>
                <div class="flex items-center gap-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                  <span class="w-1 h-1 rounded-full bg-accent"></span> Meilleur prix garanti
                </div>
              </div>
            </div>

            <!-- Extra Assistance -->
            <div class="mt-6 p-8 rounded-[2.5rem] bg-white border border-slate-100 shadow-sm flex items-center gap-6">
               <div class="w-12 h-12 rounded-2xl bg-accent-light/30 flex items-center justify-center text-accent text-2xl">
                 <i class="las la-gem"></i>
               </div>
               <div>
                 <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Besoin d'aide ?</p>
                 <p class="text-sm font-bold text-primary">Contacter la Conciergerie</p>
               </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 404 Room -->
    <div v-else class="flex flex-col items-center justify-center min-h-[70vh] px-6 text-center">
      <div class="w-24 h-24 rounded-full bg-slate-100 flex items-center justify-center mb-10 text-slate-300 text-5xl">
        <i class="las la-search-location"></i>
      </div>
      <h2 class="text-4xl md:text-5xl font-serif text-primary mb-6">Trésor introuvable</h2>
      <p class="max-w-md text-slate-500 font-light leading-relaxed mb-10">
        Cette chambre semble s'être volatilisée dans les couloirs du temps. 
        Découvrez nos autres joyaux de l'hôtellerie française.
      </p>
      <router-link to="/rooms">
        <button class="btn-premium rounded-full px-10">Explorer la collection</button>
      </router-link>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useBookingStore } from '@/stores/booking'
import api from '@/api/axios'

const route        = useRoute()
const router       = useRouter()
const bookingStore = useBookingStore()

const room        = ref(null)
const loading     = ref(true)
const activeIndex = ref(0)
const today       = new Date().toISOString().split('T')[0]

const bookForm = reactive({
  check_in:  bookingStore.searchParams?.check_in  || '',
  check_out: bookingStore.searchParams?.check_out || '',
  adults:    parseInt(bookingStore.searchParams?.adults) || 2,
  children:  parseInt(bookingStore.searchParams?.children) || 0,
})

const approvedReviews = computed(() =>
  room.value?.reviews?.filter(r => r.is_approved) ?? []
)

const amenityGroups = computed(() => {
  if (!room.value?.amenities) return {}
  return room.value.amenities.reduce((acc, a) => {
    const cat = a.category || 'other'
    if (!acc[cat]) acc[cat] = []
    acc[cat].push(a)
    return acc
  }, {})
})

const allImages = computed(() => {
  const images = room.value?.images ?? []
  if (!images.length) return []
  return images.map(img =>
    img.url ?? (img.path ? `http://localhost:8000/storage/${img.path}` : null)
  ).filter(Boolean)
})

const activeImage = computed(() => {
  const currentImages = allImages.value
  if (currentImages && currentImages.length > 0) {
    return currentImages[activeIndex.value] || currentImages[0]
  }
  
  const slug = room.value?.room_type?.slug || 'suite'
  const slugImages = {
    simple:    'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=900&q=80',
    double:    'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=900&q=80',
    suite:     'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=900&q=80',
    familiale: 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=900&q=80',
    prestige:  'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=900&q=80',
  }
  return slugImages[slug] || slugImages.suite
})

const formattedPrice = computed(() => {
  const p = parseFloat(room.value?.price_override ?? room.value?.room_type?.base_price ?? 0)
  return p.toFixed(0)
})

const nights = computed(() => {
  if (!bookForm.check_in || !bookForm.check_out) return 0
  const d1 = new Date(bookForm.check_in)
  const d2 = new Date(bookForm.check_out)
  const diff = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24))
  return diff > 0 ? diff : 0
})

const totalPrice = computed(() => {
  if (!room.value || nights.value === 0) return '0'
  const basePrice = room.value?.price_override ?? room.value?.room_type?.base_price ?? 0
  const price = parseFloat(basePrice) || 0
  return (price * nights.value).toFixed(0)
})

const canBook = computed(() =>
  bookForm.check_in && bookForm.check_out && nights.value > 0 && bookForm.adults >= 1
)

const prevImage = () => {
  activeIndex.value = (activeIndex.value - 1 + allImages.value.length) % allImages.value.length
}
const nextImage = () => {
  activeIndex.value = (activeIndex.value + 1) % allImages.value.length
}

const getCatIcon = (cat) => ({
  technology: 'las la-laptop', 
  comfort:    'las la-couch', 
  bathroom:   'las la-bath', 
  other:      'las la-box'
}[cat] ?? 'las la-star')

const catLabel = (cat) => ({
  technology: 'Technologie',
  comfort:    'Confort & Séjour',
  bathroom:   'Salle de Bain',
  other:      'Autres Avantages',
}[cat] ?? cat)

const formatDate = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { day:'numeric', month:'short', year:'numeric' }) : '—'

const book = () => {
  bookingStore.selectRoom({
    ...room.value,
    period_price: parseFloat(totalPrice.value),
    nights:       nights.value,
  })
  Object.assign(bookingStore.searchParams ?? {}, {
    check_in:  bookForm.check_in,
    check_out: bookForm.check_out,
    adults:    bookForm.adults,
    children:  bookForm.children,
  })
  router.push({ name: 'booking' })
}

onMounted(async () => {
  try {
    const { data } = await api.get(`/rooms/${route.params.id}`)
    if (!data || !data.id) {
      room.value = null
    } else {
      room.value = data
      const reviews = data.reviews?.filter(r => r.is_approved) ?? []
      room.value.average_rating = reviews.length > 0
        ? (reviews.reduce((sum, r) => sum + r.rating, 0) / reviews.length).toFixed(1)
        : null
    }
  } catch (e) {
    console.error('Erreur chargement chambre:', e)
    room.value = null
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
