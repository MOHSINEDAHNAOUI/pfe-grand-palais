<template>
  <div class="bg-slate-50 min-h-screen pt-20">

    <!-- Header Section -->
    <section class="relative h-[400px] flex items-center justify-center overflow-hidden">
      <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=1920&q=80"
             class="w-full h-full object-cover" alt="Nos Chambres" />
        <div class="absolute inset-0 bg-primary/40 backdrop-blur-[2px]"></div>
      </div>
      <div class="relative z-10 text-center text-white px-6 animate-slide-up">
        <p class="text-xs font-bold tracking-[0.4em] uppercase mb-4 text-accent-light">✦ Grand Palais ✦</p>
        <h1 class="text-5xl md:text-7xl font-serif mb-6">Chambres & Suites</h1>
        <p class="text-sm md:text-base font-light tracking-widest uppercase opacity-80">
          {{ rooms.length }} hébergements d'exception à votre disposition
        </p>
      </div>
    </section>

    <!-- Main Content -->
    <section class="max-w-7xl mx-auto px-6 py-16">
      <div class="flex flex-col lg:flex-row gap-12">

        <!-- Sidebar Filters -->
        <aside class="w-full lg:w-72 shrink-0 space-y-8">
          
          <!-- Availability Card -->
          <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-xs font-bold tracking-widest uppercase text-slate-400 mb-6">Disponibilité</h3>
            <div class="space-y-4">
              <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">Arrivée</label>
                <input v-model="searchParams.check_in" type="date" :min="today" class="input-clean !py-2" />
              </div>
              <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">Départ</label>
                <input v-model="searchParams.check_out" type="date" :min="searchParams.check_in || today" class="input-clean !py-2" />
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">Adultes</label>
                  <select v-model="searchParams.adults" class="input-clean !py-2">
                    <option v-for="n in 6" :key="n" :value="n">{{ n }}</option>
                  </select>
                </div>
                <div>
                  <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">Enfants</label>
                  <select v-model="searchParams.children" class="input-clean !py-2">
                    <option v-for="n in [0,1,2,3]" :key="n" :value="n">{{ n }}</option>
                  </select>
                </div>
              </div>
              <button @click="searchRooms" :disabled="searching" 
                      class="btn-premium w-full !py-3 !text-xs mt-2">
                {{ searching ? 'Mise à jour...' : 'Mettre à jour' }}
              </button>
            </div>
          </div>

          <!-- Filters Card -->
          <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-8">
              <h3 class="text-xs font-bold tracking-widest uppercase text-slate-400">Filtres</h3>
              <button @click="resetFilters" class="text-[10px] font-bold text-accent uppercase hover:underline">Reset</button>
            </div>

            <!-- Room Types -->
            <div class="mb-10">
              <p class="text-[10px] font-bold text-slate-900 uppercase tracking-widest mb-4">Catégorie</p>
              <div class="space-y-3">
                <label v-for="type in roomTypes" :key="type.id" 
                       class="flex items-center group cursor-pointer">
                  <input type="checkbox" :value="type.id" 
                         :checked="filters.room_type_ids.includes(type.id)"
                         @change="toggleType(type.id)"
                         class="w-4 h-4 rounded border-slate-300 text-accent focus:ring-accent mr-3" />
                  <span class="text-sm text-slate-600 group-hover:text-primary transition-all">{{ type.name }}</span>
                  <span class="ml-auto text-[10px] font-bold text-slate-300">{{ type.base_price }} MAD/n</span>
                </label>
              </div>
            </div>

            <!-- Price Range -->
            <div class="mb-10">
              <div class="flex justify-between mb-4">
                <p class="text-[10px] font-bold text-slate-900 uppercase tracking-widest text">Prix Max</p>
                <span class="text-sm font-bold text-accent">{{ filters.max_price }} MAD</span>
              </div>
              <input v-model="filters.max_price" type="range" min="50" max="1500" step="50"
                     class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-accent" />
              <div class="flex justify-between mt-2 text-[10px] font-bold text-slate-300 uppercase">
                <span>50 MAD</span>
                <span>1500 MAD</span>
              </div>
            </div>

            <!-- Views -->
            <div>
              <p class="text-[10px] font-bold text-slate-900 uppercase tracking-widest mb-4">Panorama</p>
              <div class="space-y-3">
                <label v-for="v in views" :key="v.value" class="flex items-center group cursor-pointer">
                  <input type="checkbox" :value="v.value"
                         :checked="filters.views.includes(v.value)"
                         @change="toggleView(v.value)"
                         class="w-4 h-4 rounded border-slate-300 text-accent focus:ring-accent mr-3" />
                  <span class="text-sm text-slate-600 group-hover:text-primary transition-all flex items-center gap-2">
                    <i :class="v.iconClass" class="text-accent/60"></i>
                    {{ v.label.split(' ').slice(1).join(' ') }}
                  </span>
                </label>
              </div>
            </div>
          </div>
        </aside>

        <!-- Rooms Grid Area -->
        <div class="flex-1">
          
          <!-- Tool Bar -->
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-10 gap-4">
            <div>
              <h2 class="text-2xl font-serif text-primary">{{ filteredRooms.length }} Résultats</h2>
              <p class="text-xs text-slate-400 mt-1">Trouvez l'hébergement qui vous ressemble</p>
            </div>
            
            <div class="flex items-center gap-4">
              <!-- View Toggle -->
              <div class="flex bg-slate-100 p-1 rounded-xl">
                <button @click="viewMode = 'list'" 
                        :class="viewMode === 'list' ? 'bg-white shadow-sm text-primary' : 'text-slate-400'"
                        class="px-4 py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all">
                  Liste
                </button>
                <button @click="viewMode = '3d'" 
                        :class="viewMode === '3d' ? 'bg-white shadow-sm text-primary' : 'text-slate-400'"
                        class="px-4 py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all">
                  3D Hotel
                </button>
              </div>

              <div class="flex items-center gap-3 bg-white p-1 rounded-xl shadow-sm border border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase pl-3">Trier par:</span>
                <select v-model="sortBy" class="text-xs font-semibold bg-transparent border-none focus:ring-0 cursor-pointer py-2 pr-8">
                  <option value="price_asc">Prix Croissant</option>
                  <option value="price_desc">Prix Décroissant</option>
                  <option value="surface">Surface</option>
                </select>
              </div>
            </div>
          </div>

          <!-- 3D VIEW -->
          <div v-if="viewMode === '3d'" class="relative">
            <HotelModel 
               :rooms="filteredRooms" 
               :selectedId="selectedRoomForDetail?.id"
               @select-room="openRoomDetail" />
            
            <!-- Side Detail Panel -->
            <transition name="slide-right">
              <div v-if="selectedRoomForDetail" 
                   class="absolute top-4 right-4 bottom-4 w-80 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-6 overflow-y-auto z-20">
                <button @click="selectedRoomForDetail = null" class="absolute top-4 right-4 text-slate-400 hover:text-primary">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>

                <div class="h-40 rounded-xl overflow-hidden mb-4 shadow-lg">
                  <img :src="getRoomImage(selectedRoomForDetail)" class="w-full h-full object-cover" />
                </div>
                
                <h3 class="text-xl font-serif text-primary mb-1">Chambre {{ selectedRoomForDetail.number }}</h3>
                <p class="text-[10px] font-bold text-accent uppercase tracking-widest mb-4">{{ selectedRoomForDetail.room_type?.name }}</p>
                
                <div class="space-y-3 mb-6">
                  <div class="flex justify-between text-xs">
                    <span class="text-slate-400">Étage</span>
                    <span class="font-bold text-primary">{{ selectedRoomForDetail.floor }}</span>
                  </div>
                  <div class="flex justify-between text-xs">
                    <span class="text-slate-400">Surface</span>
                    <span class="font-bold text-primary">{{ selectedRoomForDetail.surface }} m²</span>
                  </div>
                  <div class="flex justify-between text-xs">
                    <span class="text-slate-400">Vue</span>
                    <span class="font-bold text-primary">{{ selectedRoomForDetail.view }}</span>
                  </div>
                </div>

                <div class="flex flex-wrap gap-2 mb-6">
                  <span v-for="amenity in selectedRoomForDetail.amenities?.slice(0, 3)" :key="amenity.id"
                        class="px-2 py-1 bg-slate-50 rounded-lg text-[9px] font-bold text-slate-500 uppercase">
                    {{ amenity.name }}
                  </span>
                </div>

                <div class="flex items-center justify-between mb-6">
                   <p class="text-2xl font-serif text-primary">
                    {{ selectedRoomForDetail.room_type?.base_price }} MAD<span class="text-xs font-sans text-slate-400">/n</span>
                   </p>
                </div>

                <router-link 
                  :to="{ name: 'room-detail', params: { id: selectedRoomForDetail.id } }"
                  @click="bookingStore.selectRoom(selectedRoomForDetail)"
                  class="block w-full"
                >
                  <button class="btn-premium w-full !py-3 !text-[10px] !rounded-xl !tracking-widest uppercase">
                    Réserver cet hébergement
                  </button>
                </router-link>
              </div>
            </transition>
          </div>

          <!-- LIST VIEW -->
          <template v-else>
            <!-- Content states -->
            <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div v-for="i in 4" :key="i" class="h-96 bg-white rounded-3xl animate-pulse"></div>
            </div>

            <div v-else-if="!filteredRooms.length" class="bg-white rounded-[2rem] p-20 text-center border border-dashed border-slate-200">
              <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl text-slate-300">
                <i class="las la-search"></i>
              </div>
              <h3 class="text-2xl font-serif text-primary mb-2">Aucune chambre trouvée</h3>
              <p class="text-slate-400 text-sm mb-8">Essayez de modifier vos dates ou de réinitialiser les filtres.</p>
              <button @click="resetFilters" class="btn-outline-premium !rounded-xl">Tout afficher</button>
            </div>

            <!-- Actual Grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <router-link v-for="room in filteredRooms" :key="room.id"
                   :to="{ name: 'room-detail', params: { id: room.id } }"
                   @click="bookingStore.selectRoom(room)"
                   class="group bg-white rounded-[2.5rem] overflow-hidden shadow-xl shadow-primary/5 hover:shadow-2xl hover:shadow-primary/10 transition-all duration-500 block border border-slate-50">
                
                <!-- Image Wrap -->
                <div class="relative h-64 overflow-hidden">
                  <img :src="getRoomImage(room)" alt="Room" 
                       class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                  <div class="absolute inset-0 bg-gradient-to-t from-primary/60 to-transparent opacity-60"></div>
                  
                  <!-- Room Badge -->
                  <div class="absolute top-6 left-6 flex flex-col gap-2">
                    <span class="px-3 py-1 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-[10px] font-bold text-white uppercase tracking-widest">
                      {{ room.room_type?.name }}
                    </span>
                    <span v-if="!room.smoking" class="w-fit px-3 py-1 bg-emerald-500/90 rounded-full text-[10px] font-bold text-white uppercase tracking-widest">
                      Non-fumeur
                    </span>
                  </div>

                  <!-- Price floating -->
                  <div class="absolute bottom-6 right-6 text-right">
                    <p class="text-white/70 text-[10px] font-bold uppercase tracking-widest mb-1">Total</p>
                    <p class="text-3xl font-serif text-white">
                      {{ parseFloat(room.period_price ?? room.price_override ?? room.room_type?.base_price ?? 0).toFixed(0) }} MAD
                    </p>
                  </div>
                </div>

                <!-- Content -->
                <div class="p-8">
                  <div class="flex justify-between items-start mb-6">
                    <div>
                      <h4 class="text-2xl font-serif text-primary mb-1">Chambre {{ room.number }}</h4>
                      <p class="text-xs text-slate-400 font-medium">Étage {{ room.floor }} • {{ room.surface }} m² • {{ room.view || 'Standard' }}</p>
                    </div>
                  </div>

                  <!-- Amenities -->
                  <div class="flex flex-wrap gap-2 mb-8">
                    <span v-for="amenity in room.amenities?.slice(0, 4)" :key="amenity.id"
                          class="px-3 py-1.5 bg-slate-50 rounded-xl text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                      {{ amenity.name }}
                    </span>
                    <span v-if="room.amenities?.length > 4" class="px-3 py-1.5 bg-accent/10 rounded-xl text-[10px] font-bold text-accent uppercase tracking-wider">
                      +{{ room.amenities.length - 4 }}
                    </span>
                  </div>

                  <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                    <div class="flex -space-x-3">
                      <div v-for="i in 3" :key="i" class="w-8 h-8 rounded-full border-2 border-white bg-slate-200 overflow-hidden ring-accent ring-offset-2 hover:ring-2 transition-all cursor-help">
                        <img :src="`https://i.pravatar.cc/100?u=${room.id+i}`" class="w-full h-full object-cover" />
                      </div>
                    </div>
                    <div class="btn-premium !rounded-2xl !py-2.5 !text-[10px] !px-6 shadow-lg shadow-primary/10">
                      Réserver
                    </div>
                  </div>
                </div>
              </router-link>
            </div>
          </template>

        </div>
      </div>
    </section>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useBookingStore } from '@/stores/booking'
import api from '@/api/axios'
import HotelModel from '@/components/rooms/HotelModel.vue'

const route        = useRoute()
const router       = useRouter()
const bookingStore = useBookingStore()

const rooms     = ref([])
const roomTypes = ref([])
const loading   = ref(false)
const searching = ref(false)
const sortBy    = ref('price_asc')
const today     = new Date().toISOString().split('T')[0]

const viewMode = ref('list')
const selectedRoomForDetail = ref(null)

const openRoomDetail = (room) => {
  selectedRoomForDetail.value = room
}

const searchParams = ref({
  check_in:  route.query.check_in  || bookingStore.searchParams?.check_in  || '',
  check_out: route.query.check_out || bookingStore.searchParams?.check_out || '',
  adults:    route.query.adults    || bookingStore.searchParams?.adults    || 2,
  children:  route.query.children  || bookingStore.searchParams?.children  || 0,
})

const filters = ref({
  room_type_ids: route.query.room_type_id ? [parseInt(route.query.room_type_id)] : [],
  max_price:     1500,
  views:         [],
  non_smoking:   false,
})

const views = [
  { value: 'mer',    label: '🌊 Horizon Mer',    iconClass: 'las la-water' },
  { value: 'jardin', label: '🌿 Jardins',        iconClass: 'las la-leaf' },
  { value: 'ville',  label: '🏙️ Vue Ville',      iconClass: 'las la-city' },
  { value: 'cour',   label: '🏡 Cour Intérieure', iconClass: 'las la-home' },
]

const filteredRooms = computed(() => {
  let list = [...rooms.value]

  if (filters.value.room_type_ids.length) {
    list = list.filter(r => filters.value.room_type_ids.includes(r.room_type_id))
  }
  list = list.filter(r =>
    (r.period_price || r.room_type?.base_price || 0) <= filters.value.max_price
  )
  if (filters.value.views.length) {
    list = list.filter(r => filters.value.views.includes(r.view))
  }
  if (filters.value.non_smoking) {
    list = list.filter(r => !r.smoking)
  }

  list.sort((a, b) => {
    const pa = a.period_price || a.room_type?.base_price || 0
    const pb = b.period_price || b.room_type?.base_price || 0
    if (sortBy.value === 'price_asc')  return pa - pb
    if (sortBy.value === 'price_desc') return pb - pa
    if (sortBy.value === 'surface')    return b.surface - a.surface
    return 0
  })

  return list
})

const toggleType = (id) => {
  const idx = filters.value.room_type_ids.indexOf(id)
  if (idx === -1) filters.value.room_type_ids.push(id)
  else filters.value.room_type_ids.splice(idx, 1)
}

const toggleView = (v) => {
  const idx = filters.value.views.indexOf(v)
  if (idx === -1) filters.value.views.push(v)
  else filters.value.views.splice(idx, 1)
}

const getRoomImage = (room) => {
  const img = room.images?.find(i => i.is_primary) || room.images?.[0]
  if (img?.path) return `http://localhost:8000/storage/${img.path}`
  const slugImages = {
    simple:              'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=500&q=80',
    double:              'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=500&q=80',
    suite:               'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=500&q=80',
    'suite-junior':      'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=500&q=80',
    'suite-presidentielle': 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=500&q=80',
    familiale:           'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=500&q=80',
    prestige:            'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=500&q=80',
  }
  return slugImages[room.room_type?.slug] || slugImages.suite
}

const searchRooms = async () => {
  searching.value = true
  try {
    const { data } = await api.get('/rooms/search', { params: searchParams.value })
    rooms.value = data.data ?? data
    Object.assign(bookingStore.searchParams ?? {}, searchParams.value)
  } catch {
    const { data } = await api.get('/rooms')
    rooms.value = data.data ?? []
  } finally {
    searching.value = false
  }
}

const loadAllRooms = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/rooms', { 
      params: { 
        check_in: searchParams.value.check_in,
        check_out: searchParams.value.check_out
      } 
    })
    rooms.value = data.data ?? data
  } finally {
    loading.value = false
  }
}

import { watch } from 'vue'
watch(() => searchParams.value, () => {
  if (searchParams.value.check_in && searchParams.value.check_out) {
    searchRooms()
  } else {
    loadAllRooms()
  }
}, { deep: true })

// Navigation handled via <router-link> in template

const resetFilters = () => {
  filters.value = { room_type_ids: [], max_price: 1500, views: [], non_smoking: false }
}

onMounted(async () => {
  const typesRes = await api.get('/rooms/types').catch(() => ({ data: [] }))
  roomTypes.value = typesRes.data

  if (searchParams.value.check_in && searchParams.value.check_out) {
    await searchRooms()
  } else {
    await loadAllRooms()
  }
})
</script>

<style scoped>
.slide-right-enter-active,
.slide-right-leave-active {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-right-enter-from,
.slide-right-leave-to {
  transform: translateX(100%);
  opacity: 0;
}

.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-track {
  background: rgba(0,0,0,0.05);
}
.overflow-y-auto::-webkit-scrollbar-thumb {
  background: rgba(0,0,0,0.1);
  border-radius: 10px;
}
</style>
