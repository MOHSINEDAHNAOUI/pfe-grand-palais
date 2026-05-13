<template>
  <div class="bg-slate-50 min-h-screen">

    <!-- Welcome notification -->
    <Transition name="fade-slide">
      <div v-if="showWelcome"
           class="fixed top-24 right-6 z-[10000] w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 p-5">
        <div class="flex gap-4">
          <div class="w-10 h-10 rounded-full bg-accent/10 text-accent flex items-center justify-center shrink-0">
             <i class="las la-hand-peace text-xl"></i>
          </div>
          <div class="flex-1">
            <h4 class="text-sm font-bold text-slate-900 mb-1">Bienvenue, {{ authStore.user?.name }} !</h4>
            <p class="text-xs text-slate-500 leading-relaxed mb-3">
              Complétez votre profil pour bénéficier de nos offres exclusives.
            </p>
            <router-link to="/dashboard" class="text-xs font-bold text-accent hover:underline">
              Paramètres du profil →
            </router-link>
          </div>
          <button @click="showWelcome = false" class="text-slate-400 hover:text-slate-600">
            <i class="las la-times"></i>
          </button>
        </div>
      </div>
    </Transition>

    <!-- HERO SECTION -->
    <section class="relative h-screen min-h-[700px] flex items-center justify-center overflow-hidden">
      <div class="absolute inset-0 z-0">
        <img
          src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1920&q=80"
          class="w-full h-full object-cover scale-105 animate-slow-zoom"
          alt="Luxury Hotel"
        />
        <div class="absolute inset-0 bg-gradient-to-b from-primary/60 via-primary/30 to-primary/80"></div>
      </div>

      <div class="relative z-10 max-w-5xl mx-auto px-6 text-center text-white animate-slide-up">
        <p class="inline-block px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-[10px] font-bold tracking-[0.3em] uppercase mb-8">
          ✦ Collection Signature ✦
        </p>
        <h1 class="text-6xl md:text-8xl lg:text-9xl mb-8 leading-[0.9] font-serif">
          L'essence du <span class="italic text-accent-light">Luxe</span>
        </h1>
        <p class="max-w-xl mx-auto text-lg md:text-xl font-light text-white/80 leading-relaxed mb-12">
          Découvrez une expérience hôtelière d'exception au cœur de Marrakech, 
          mêlant hospitalité marocaine et raffinement contemporain.
        </p>
        <div class="flex flex-col sm:flex-row gap-5 justify-center mt-8">
          <router-link to="/rooms">
            <button class="btn-gold-premium shadow-xl shadow-accent/20">Explorer nos suites</button>
          </router-link>
          <router-link to="/about">
            <button class="btn-outline-premium !border-white/40 !text-white hover:!bg-white hover:!text-primary">
              Notre histoire
            </button>
          </router-link>
        </div>
      </div>

      <!-- Scroll Indicator -->
      <div class="absolute bottom-12 left-1/2 -translate-x-1/2 flex flex-col items-center gap-3 animate-bounce opacity-60">
        <span class="text-[10px] font-bold tracking-widest uppercase text-white">Découvrir</span>
        <div class="w-px h-16 bg-gradient-to-b from-accent to-transparent"></div>
      </div>
    </section>

    <!-- FLOATING SEARCH BAR -->
    <section class="relative z-20 -mt-16 px-6">
      <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-3xl shadow-2xl shadow-primary/5 p-4 md:p-6 lg:p-8 border border-slate-100">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="space-y-2">
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Arrivée</label>
              <div class="relative">
                <input v-model="searchForm.check_in" type="date" :min="today" class="input-clean !pl-10" />
              </div>
            </div>
            <div class="space-y-2">
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Départ</label>
              <div class="relative">
                <input v-model="searchForm.check_out" type="date" :min="searchForm.check_in || today" class="input-clean !pl-10" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Adultes</label>
                <select v-model="searchForm.adults" class="input-clean">
                  <option v-for="n in 6" :key="n" :value="n">{{ n }}</option>
                </select>
              </div>
              <div class="space-y-2">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Enfants</label>
                <select v-model="searchForm.children" class="input-clean">
                  <option v-for="n in [0,1,2,3]" :key="n" :value="n">{{ n }}</option>
                </select>
              </div>
            </div>
            <div class="flex items-end">
              <button @click="search" class="btn-gold-premium w-full !rounded-2xl shadow-lg shadow-accent/20">
                Vérifier Disponibilité
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ROOM TYPES GRID -->
    <section class="py-32 px-6">
      <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8">
          <div class="max-w-xl">
            <p class="text-accent font-bold tracking-[0.2em] uppercase text-xs mb-4">Hébergements</p>
            <h2 class="text-5xl md:text-6xl font-serif text-slate-900 leading-tight">
              Des espaces pensés pour votre <span class="italic text-accent">confort</span>
            </h2>
          </div>
          <router-link to="/rooms" class="group flex items-center gap-3 font-bold text-sm tracking-widest uppercase text-slate-900 hover:text-accent transition-all">
            Voir tout le catalogue
            <div class="w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center group-hover:border-accent group-hover:bg-accent group-hover:text-white transition-all">
              →
            </div>
          </router-link>
        </div>

        <div v-if="loadingRooms" class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="i in 3" :key="i" class="h-[500px] rounded-3xl bg-slate-200 animate-pulse"></div>
        </div>

        <!-- Infinite Carousel -->
        <div v-else class="relative w-full overflow-hidden mt-8">
          <div class="carousel-track flex gap-8 py-10">
            <!-- First Set -->
            <div v-for="type in roomTypes" :key="'first-' + type.id"
                 class="carousel-item flex-none w-[420px] group relative h-[580px] rounded-[2.5rem] overflow-hidden cursor-pointer shadow-2xl shadow-primary/5 border border-slate-100/50 transition-all duration-700 hover:-translate-y-2"
                 @click="goToRooms(type)">
              <img :src="getRoomTypeImage(type.slug)" 
                   class="w-full h-full object-cover transition-transform duration-[1.5s] group-hover:scale-110" />
              <div class="absolute inset-0 bg-gradient-to-t from-primary/95 via-primary/30 to-transparent"></div>
              
              <div class="absolute bottom-10 left-10 right-10">
                <div class="flex flex-col gap-2">
                  <p class="text-[10px] font-bold text-accent tracking-[0.4em] uppercase mb-1">Standard Distinction</p>
                  <h3 class="text-3xl font-serif text-white mb-4">{{ type.name }}</h3>
                  <div class="flex items-center gap-4 mb-6">
                    <span class="px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-[9px] font-bold text-white uppercase tracking-widest border border-white/20">
                      À partir de {{ type.base_price }} MAD
                    </span>
                    <span class="text-[9px] text-white/50 font-bold uppercase tracking-widest">Capacité: {{ type.max_capacity }} pers.</span>
                  </div>
                  <button class="btn-premium !bg-white !text-primary !rounded-xl !py-4 !px-8 !text-[11px] !tracking-widest uppercase opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-700 shadow-2xl shadow-white/10">
                    Découvrir la résidence
                  </button>
                </div>
              </div>
            </div>
            <!-- Second Set (Duplicate for Infinite Loop) -->
            <div v-for="type in roomTypes" :key="'second-' + type.id"
                 class="carousel-item flex-none w-[420px] group relative h-[580px] rounded-[2.5rem] overflow-hidden cursor-pointer shadow-2xl shadow-primary/5 border border-slate-100/50 transition-all duration-700 hover:-translate-y-2"
                 @click="goToRooms(type)">
              <img :src="getRoomTypeImage(type.slug)" 
                   class="w-full h-full object-cover transition-transform duration-[1.5s] group-hover:scale-110" />
              <div class="absolute inset-0 bg-gradient-to-t from-primary/95 via-primary/30 to-transparent"></div>
              
              <div class="absolute bottom-10 left-10 right-10">
                <div class="flex flex-col gap-2">
                   <p class="text-[10px] font-bold text-accent tracking-[0.4em] uppercase mb-1">Standard Distinction</p>
                  <h3 class="text-3xl font-serif text-white mb-4">{{ type.name }}</h3>
                  <div class="flex items-center gap-4 mb-6">
                    <span class="px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-[9px] font-bold text-white uppercase tracking-widest border border-white/20">
                      À partir de {{ type.base_price }} MAD
                    </span>
                    <span class="text-[9px] text-white/50 font-bold uppercase tracking-widest">Capacité: {{ type.max_capacity }} pers.</span>
                  </div>
                  <button class="btn-premium !bg-white !text-primary !rounded-xl !py-4 !px-8 !text-[11px] !tracking-widest uppercase opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-700 shadow-2xl shadow-white/10">
                    Découvrir la résidence
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- PHILOSOPHY / SERVICES -->
    <section class="py-32 bg-white overflow-hidden border-t border-slate-100">
      <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
          <div class="relative">
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-accent/5 rounded-full blur-3xl"></div>
            <p class="text-accent font-bold tracking-[0.2em] uppercase text-xs mb-6">Notre Excellence</p>
            <h2 class="text-5xl md:text-7xl font-serif text-primary leading-tight mb-8">
              Plus qu'un hôtel, <br><span class="italic text-accent">un sanctuaire.</span>
            </h2>
            <p class="text-lg text-slate-500 font-light leading-relaxed mb-12 max-w-lg">
              Depuis plus de deux décennies, nous cultivons l'art de recevoir avec une attention 
              particulière portée aux moindres détails. Nos concierges sont à votre service 
              pour faire de chaque vœux une réalité.
            </p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
              <div v-for="feat in features" :key="feat.title" class="group">
                <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-3xl mb-5 group-hover:bg-accent group-hover:border-accent group-hover:text-white transition-all duration-500">
                  <i :class="feat.iconClass"></i>
                </div>
                <h4 class="text-primary font-bold text-lg mb-2">{{ feat.title }}</h4>
                <p class="text-slate-400 text-sm leading-relaxed">{{ feat.desc }}</p>
              </div>
            </div>
          </div>

          <div class="relative group">
            <div class="absolute -inset-4 bg-accent/10 rounded-[3rem] blur-2xl group-hover:bg-accent/20 transition-all"></div>
            <div class="relative h-[650px] rounded-[3rem] overflow-hidden shadow-2xl">
              <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80" 
                   class="w-full h-full object-cover grayscale-[0.2] transition-transform duration-1000 group-hover:scale-105" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- STATS -->
    <section class="py-24 border-b border-slate-100 bg-white">
      <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-12 text-center">
          <div v-for="stat in stats" :key="stat.label">
            <p class="text-5xl md:text-6xl font-serif text-primary mb-3">{{ stat.value }}</p>
            <p class="text-[10px] font-bold tracking-[0.3em] uppercase text-slate-400">{{ stat.label }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 3D PALACE DISCOVERY -->
    <section class="py-32 bg-white overflow-hidden">
      <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col lg:flex-row items-center gap-20">
          <div class="lg:w-1/3 animate-slide-up">
            <p class="text-xs font-bold tracking-[0.4em] text-accent uppercase mb-6">Immersion Totale</p>
            <h2 class="text-5xl font-serif text-primary mb-8 leading-tight">
              Explorez le <br/><span class="italic">Palais en 3D</span>
            </h2>
            <div class="w-12 h-1 bg-accent mb-10"></div>
            <p class="text-slate-500 font-light leading-relaxed mb-10">
              Vivez une expérience interactive unique. Naviguez à travers notre complexe, découvrez l'emplacement de nos suites et visualisez la vue imprenable sur l'Atlas et nos jardins luxuriants.
            </p>
            <div class="space-y-6 mb-12">
              <div class="flex items-center gap-5 group">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-accent transition-all group-hover:bg-accent group-hover:text-white">
                  <i class="las la-cube text-2xl"></i>
                </div>
                <div>
                  <h4 class="text-sm font-bold text-primary uppercase tracking-widest">Maquette Interactive</h4>
                  <p class="text-xs text-slate-400">Navigation fluide et temps réel</p>
                </div>
              </div>
              <div class="flex items-center gap-5 group">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-accent transition-all group-hover:bg-accent group-hover:text-white">
                  <i class="las la-door-open text-2xl"></i>
                </div>
                <div>
                  <h4 class="text-sm font-bold text-primary uppercase tracking-widest">Disponibilité Directe</h4>
                  <p class="text-xs text-slate-400">Réservez depuis la vue 3D</p>
                </div>
              </div>
            </div>
            <router-link to="/rooms">
              <button class="btn-premium !rounded-2xl !px-10 !py-4 shadow-xl shadow-primary/5">Consulter les tarifs</button>
            </router-link>
          </div>
          <div class="lg:w-2/3 w-full relative">
            <div class="absolute -inset-10 bg-accent/5 rounded-full blur-3xl"></div>
            <HotelModel 
              :rooms="rooms" 
              @select-room="router.push({ name: 'room-detail', params: { id: $event.id } })" />
          </div>
        </div>
      </div>
    </section>

    <!-- TESTIMONIALS / REVIEWS -->
    <section v-if="reviews.length > 0" class="py-32 bg-slate-50 overflow-hidden">
      <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-20">
          <p class="text-accent font-bold tracking-[0.2em] uppercase text-xs mb-4">Témoignages</p>
          <h2 class="text-5xl font-serif text-primary">Ce que nos résidents <span class="italic text-accent">racontent</span></h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
          <div v-for="rev in reviews" :key="rev.id" 
               class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-primary/5 border border-slate-100 flex flex-col justify-between transition-all hover:-translate-y-2">
            <div>
              <div class="flex items-center gap-1 mb-6">
                <span v-for="star in 5" :key="star" 
                      class="text-sm"
                      :class="star <= rev.rating ? 'text-accent' : 'text-slate-200'">
                  <i class="las la-star"></i>
                </span>
              </div>
              <p class="text-slate-600 font-light italic leading-relaxed mb-8">
                "{{ rev.comment }}"
              </p>
            </div>
            
            <div class="flex items-center gap-4 pt-8 border-t border-slate-50">
              <img :src="getAvatar(rev.user)" 
                   class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm" />
              <div>
                <h5 class="text-sm font-bold text-primary">{{ rev.user?.name }}</h5>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ rev.room?.room_type?.name }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-slate-100 pt-32 pb-16">
      <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-20 mb-20">
          <div class="lg:col-span-2">
            <h3 class="text-4xl font-serif text-primary mb-8">Grand Palais</h3>
            <p class="text-slate-500 font-light leading-relaxed max-w-sm mb-12">
              L'exceptionnel devient standard. Membre distingué de la collection 
              Hôtels & Récits célèbres, nous vous offrons le meilleur de Marrakech.
            </p>
            <div class="flex gap-4">
              <a href="#" class="w-10 h-10 rounded-full border border-slate-100 flex items-center justify-center hover:bg-accent hover:text-white transition-all text-slate-400">
                <i class="lab la-facebook-f"></i>
              </a>
              <a href="#" class="w-10 h-10 rounded-full border border-slate-100 flex items-center justify-center hover:bg-accent hover:text-white transition-all text-slate-400">
                <i class="lab la-instagram"></i>
              </a>
              <a href="#" class="w-10 h-10 rounded-full border border-slate-100 flex items-center justify-center hover:bg-accent hover:text-white transition-all text-slate-400">
                <i class="lab la-twitter"></i>
              </a>
            </div>
          </div>
          <div>
            <h4 class="text-xs font-bold tracking-widest uppercase text-accent mb-8">Navigation</h4>
            <ul class="space-y-4">
              <li v-for="link in footerLinks" :key="link.to">
                <router-link :to="link.to" class="text-sm text-slate-500 hover:text-accent transition-all">
                  {{ link.label }}
                </router-link>
              </li>
            </ul>
          </div>
          <div>
            <h4 class="text-xs font-bold tracking-widest uppercase text-accent mb-8">Contact</h4>
            <ul class="space-y-4 text-sm text-slate-500 font-light">
              <li class="flex items-center gap-3 italic">
                <i class="las la-map-marker text-accent"></i> Avenue Mohammed VI, Hivernage, Marrakech
              </li>
              <li class="flex items-center gap-3">
                <i class="las la-phone text-accent"></i> +212 5 24 00 00 00
              </li>
              <li class="flex items-center gap-3">
                <i class="las la-envelope text-accent"></i> contact@grandpalais.ma
              </li>
            </ul>
          </div>
        </div>
        <div class="pt-8 border-t border-slate-50 flex flex-col md:flex-row justify-between gap-6">
          <p class="text-[10px] text-slate-300 uppercase tracking-widest">© 2026 Grand Palais Hôtel. Tous droits réservés.</p>
          <div class="flex gap-8">
            <a href="#" class="text-[10px] text-slate-300 hover:text-accent transition-all uppercase tracking-widest">Confidentialité</a>
            <a href="#" class="text-[10px] text-slate-300 hover:text-accent transition-all uppercase tracking-widest">Mentions Légales</a>
          </div>
        </div>
      </div>
    </footer>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useBookingStore } from '@/stores/booking'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'
import HotelModel from '@/components/rooms/HotelModel.vue'

const router       = useRouter()
const bookingStore = useBookingStore()
const authStore    = useAuthStore()
const roomTypes    = ref([])
const rooms        = ref([])
const loadingRooms = ref(true)
const reviews      = ref([])
const today        = new Date().toISOString().split('T')[0]
const showWelcome  = ref(window.location.search.includes('welcome=google'))

const searchForm = ref({
  check_in:  '',
  check_out: '',
  adults:    2,
  children:  0,
})

const features = [
  {
    iconClass: 'las la-utensils',
    title: 'Gastronomie',
    desc:  'Découvrez une cuisine triplement étoilée.',
    image: 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=400&q=80',
  },
  {
    iconClass: 'las la-spa',
    title: 'Sanctuaire Spa',
    desc:  'Évadez-vous dans notre havre de paix urbain.',
    image: 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=400&q=80',
  },
  {
    iconClass: 'las la-concierge-bell',
    title: 'Conciergerie',
    desc:  'Un service sur mesure pour chaque désir.',
    image: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&q=80',
  },
  {
    iconClass: 'las la-swimming-pool',
    title: 'Piscine Infinity',
    desc:  'Détente avec vue sur les jardins de la Ménara.',
    image: 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=400&q=80',
  },
]

const stats = [
  { value: '25+',   label: "Années d'excellence" },
  { value: '80',   label: 'Chambres & Suites' },
  { value: '★★★★★', label: 'Etoiles de Luxe' },
  { value: '98%',   label: 'Clients Satisfaits' },
]

const footerLinks = [
  { to: '/',      label: 'Accueil' },
  { to: '/rooms', label: 'Nos Chambres' },
  { to: '/login', label: 'Espace Client' },
  { to: '/dashboard/reservations', label: 'Réservations' },
]

const getRoomTypeImage = (slug) => {
  const images = {
    simple:    'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80',
    double:    'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800&q=80',
    suite:     'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800&q=80',
    familiale: 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=800&q=80',
    prestige:  'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&q=80',
  }
  return images[slug] || 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800&q=80'
}

const getAvatar = (user) => {
  if (user?.avatar) {
    if (user.avatar.startsWith('http')) return user.avatar
    return `http://localhost:8000/storage/${user.avatar}`
  }
  const name = encodeURIComponent(user?.name || 'U')
  return `https://ui-avatars.com/api/?name=${name}&background=C9A96E&color=fff&size=100`
}

const search = () => {
  Object.assign(bookingStore.searchParams, searchForm.value)
  router.push({ path: '/rooms', query: searchForm.value })
}

const goToRooms = (type) => {
  router.push({ path: '/rooms', query: { room_type_id: type.id } })
}

const loadRooms = async () => {
  try {
    const { data } = await api.get('/rooms', { 
      params: { 
        check_in: searchForm.value.check_in,
        check_out: searchForm.value.check_out
      } 
    })
    rooms.value = data.data ?? data
  } catch (e) {
    console.error('Erreur chargement rooms:', e)
  }
}

import { watch } from 'vue'
watch(() => searchForm.value, () => {
  if (searchForm.value.check_in && searchForm.value.check_out) {
    loadRooms()
  }
}, { deep: true })

onMounted(async () => {
  try {
    const [roomsTypesRes, reviewsRes] = await Promise.all([
      api.get('/rooms/types'),
      api.get('/reviews')
    ])
    roomTypes.value = roomsTypesRes.data
    reviews.value = reviewsRes.data
    await loadRooms()
  } catch (e) {
    console.error('Erreur chargement données:', e)
  } finally {
    loadingRooms.value = false
  }
})
</script>

<style scoped>
@keyframes slow-zoom {
  from { transform: scale(1.05); }
  to { transform: scale(1); }
}
.animate-slow-zoom {
  animation: slow-zoom 20s linear infinite alternate;
}

.fade-slide-enter-active, .fade-slide-leave-active { transition: all 0.4s ease; }
.fade-slide-enter-from, .fade-slide-leave-to { opacity: 0; transform: translateX(20px); }

@keyframes scroll {
  0% { transform: translateX(0); }
  100% { transform: translateX(calc(-50% - 1rem)); }
}

.carousel-track {
  display: flex;
  width: max-content;
  animation: scroll 40s linear infinite;
}

.carousel-track:hover {
  animation-play-state: paused;
}

/* Adjust card contrast for premium feel */
.carousel-item img {
  filter: saturate(0.9) contrast(1.1);
}
.carousel-item:hover img {
  filter: saturate(1) contrast(1.1);
}
</style>
