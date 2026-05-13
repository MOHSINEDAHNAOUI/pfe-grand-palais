<template>
  <AdminLayout page-title="Tableau de bord — Réception" @logout="logout">

    <div class="max-w-[1600px] mx-auto animate-fade-in">
      
      <!-- Premium Stats Strip -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div v-for="stat in todayStats" :key="stat.label"
             class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-primary/5 group hover:scale-[1.02] transition-all duration-500 relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-slate-50 rounded-bl-[4rem] -z-10 group-hover:bg-accent/5 transition-colors"></div>
          
          <div class="flex items-start justify-between">
            <div>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">{{ stat.label }}</p>
              <h3 class="text-4xl font-serif text-primary mb-1">{{ stat.value }}</h3>
              <p class="text-[10px] font-medium text-slate-400">{{ stat.sub }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all group-hover:rotate-12"
                 :style="{ backgroundColor: stat.color + '15', color: stat.color }">
              <i :class="[stat.icon, 'text-2xl']"></i>
            </div>
          </div>
          
          <div class="mt-6 h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-1000"
                 :style="{ width: '100%', backgroundColor: stat.color }"></div>
          </div>
        </div>
      </div>

      <!-- Center Piece: Clock & Search -->
      <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-10">
        
        <!-- Luxury Clock Card -->
        <div class="bg-primary p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden group">
          <div class="absolute top-0 right-0 w-64 h-64 bg-accent/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-1000"></div>
          
          <div class="relative z-10 text-center">
            <p class="text-accent font-bold tracking-[0.4em] uppercase text-[10px] mb-6">Service de Conciergerie • Temps Réel</p>
            <h2 class="text-6xl font-serif text-white mb-4 tracking-tight tabular-nums">{{ currentTime }}</h2>
            <p class="text-slate-400 text-sm font-light uppercase tracking-widest">{{ today }}</p>
            
            <div class="mt-10 grid grid-cols-3 gap-4 border-t border-white/10 pt-8">
              <div>
                <p class="text-emerald-400 text-lg font-serif">{{ arrivals.length }}</p>
                <p class="text-[8px] text-slate-500 uppercase font-bold tracking-widest mt-1">Arrivées</p>
              </div>
              <div>
                <p class="text-blue-400 text-lg font-serif">{{ departures.length }}</p>
                <p class="text-[8px] text-slate-500 uppercase font-bold tracking-widest mt-1">Départs</p>
              </div>
              <div>
                <p class="text-accent text-lg font-serif">{{ availableRooms.length }}</p>
                <p class="text-[8px] text-slate-500 uppercase font-bold tracking-widest mt-1">Disponibles</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Intelligent Search Card -->
        <div class="xl:col-span-2 bg-white p-10 rounded-[3.5rem] border border-slate-100 shadow-2xl shadow-primary/5 flex flex-col">
          <div class="flex items-center gap-4 mb-8">
            <span class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-primary shadow-sm"><i class="las la-search text-2xl"></i></span>
            <div>
              <h2 class="text-2xl font-serif text-primary">Recherche Opérationnelle</h2>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Identification rapide des dossiers</p>
            </div>
          </div>

          <div class="flex flex-col sm:flex-row gap-4 mb-8">
            <div class="relative flex-1 group min-w-[200px]">
              <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 transition-colors group-focus-within:text-accent"><i class="las la-id-card text-xl"></i></span>
              <input v-model="searchQuery" type="text" placeholder="Référence ou nom..."
                     class="w-full pl-14 pr-6 py-4 bg-slate-50 border-none rounded-[1.5rem] text-sm font-medium text-primary focus:ring-4 focus:ring-accent/5 outline-none transition-all"
                     @keyup.enter="searchReservation" />
            </div>
            <button @click="searchReservation" 
                    class="px-8 py-4 bg-primary text-white rounded-[1.5rem] font-bold text-[10px] uppercase tracking-widest hover:bg-accent transition-all duration-500 shadow-xl shadow-primary/20 shrink-0">
              Chercher
            </button>
          </div>

          <!-- Search Result Area -->
          <div class="flex-1 min-h-[140px] flex items-center justify-center rounded-[2.5rem] border-2 border-dashed border-slate-50 transition-all"
               :class="searchResult ? 'bg-white border-transparent' : 'bg-slate-50/30'">
            
            <div v-if="searchResult" class="w-full p-6 animate-scale-in">
              <div class="flex items-center justify-between gap-6 p-6 rounded-[2rem] bg-white border border-slate-100 shadow-xl">
                <div class="flex items-center gap-5">
                  <div class="relative">
                    <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(searchResult.user?.name || 'U')}&size=100&background=0f172a&color=fff&bold=true`"
                         class="w-16 h-16 rounded-2xl object-cover shadow-lg" />
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-4 border-white" :style="statusDotStyle(searchResult.status)"></div>
                  </div>
                  <div>
                    <div class="flex items-center gap-3 mb-1">
                      <h4 class="text-lg font-serif text-primary">{{ searchResult.user?.name }}</h4>
                      <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-[0.2em]" :style="statusStyle(searchResult.status)">
                        {{ statusLabel(searchResult.status) }}
                      </span>
                    </div>
                    <p class="text-[10px] font-bold text-accent uppercase tracking-widest">{{ searchResult.reference }} • Chambre {{ searchResult.room?.number }}</p>
                  </div>
                </div>

                <div class="flex gap-3">
                  <button v-if="searchResult.status === 'confirmed'" @click="checkIn(searchResult)"
                          class="px-6 py-3 bg-emerald-500 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-600 transition-all flex items-center gap-2">
                    <i class="las la-check text-lg"></i> Check-in
                  </button>
                  <button v-if="searchResult.status === 'checked_in'" @click="checkOut(searchResult)"
                          class="px-6 py-3 bg-blue-500 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-blue-600 transition-all flex items-center gap-2">
                    <i class="las la-sign-out-alt text-lg"></i> Check-out
                  </button>
                  <router-link :to="`/admin/reservations?search=${searchResult.reference}`" 
                               class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-primary hover:text-white transition-all">
                    <i class="las la-external-link-alt text-xl"></i>
                  </router-link>
                </div>
              </div>
            </div>

            <div v-else-if="searchError" class="text-center text-rose-500 flex flex-col items-center gap-2">
               <i class="las la-exclamation-circle text-3xl"></i>
               <p class="text-[10px] font-bold uppercase tracking-widest">{{ searchError }}</p>
            </div>

            <div v-else class="text-center text-slate-300">
               <i class="las la-keyboard text-4xl mb-3 block opacity-20"></i>
               <p class="text-[10px] font-bold uppercase tracking-[0.3em]">En attente de saisie...</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Dynamic Lists Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-12">
        
        <!-- Arrivées du Jour -->
        <div class="bg-white rounded-[3rem] border border-slate-100 shadow-2xl shadow-primary/5 overflow-hidden flex flex-col">
          <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-emerald-50/30">
            <div class="flex items-center gap-4">
              <span class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20"><i class="las la-sign-in-alt text-xl"></i></span>
              <h3 class="text-sm font-bold text-primary uppercase tracking-widest">Arrivées Prévues</h3>
            </div>
            <span class="px-4 py-1.5 rounded-full bg-white text-emerald-600 text-xs font-black shadow-sm">{{ arrivals.length }}</span>
          </div>
          <div class="flex-1 overflow-y-auto max-h-[500px] custom-scrollbar">
            <div v-if="!arrivals.length" class="py-20 text-center opacity-30">
              <i class="las la-bed text-5xl mb-4 block"></i>
              <p class="text-[10px] font-bold uppercase tracking-widest">Aucune arrivée prévue</p>
            </div>
            <div v-else class="divide-y divide-slate-50">
              <div v-for="r in arrivals" :key="r.id" class="p-6 hover:bg-slate-50/50 transition-all group">
                <div class="flex items-center justify-between gap-4">
                  <div class="flex items-center gap-4">
                    <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(r.user?.name || 'U')}&background=f8fafc&color=0f172a&bold=true`"
                         class="w-10 h-10 rounded-xl border border-slate-100" />
                    <div>
                      <p class="text-xs font-bold text-primary truncate max-w-[120px]">{{ r.user?.name }}</p>
                      <p class="text-[9px] font-bold text-accent uppercase tracking-widest">CH {{ r.room?.number }} • {{ r.adults }} PERS.</p>
                    </div>
                  </div>
                  <button v-if="r.status === 'confirmed'" @click="checkIn(r)"
                          class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-emerald-500 hover:text-white transition-all">
                    Check-in
                  </button>
                  <span v-else class="text-[9px] font-black text-emerald-500 uppercase tracking-widest"><i class="las la-check-circle mr-1"></i>Arrivé</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Départs du Jour -->
        <div class="bg-white rounded-[3rem] border border-slate-100 shadow-2xl shadow-primary/5 overflow-hidden flex flex-col">
          <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-blue-50/30">
            <div class="flex items-center gap-4">
              <span class="w-10 h-10 rounded-xl bg-blue-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/20"><i class="las la-sign-out-alt text-xl"></i></span>
              <h3 class="text-sm font-bold text-primary uppercase tracking-widest">Départs Prévus</h3>
            </div>
            <span class="px-4 py-1.5 rounded-full bg-white text-blue-600 text-xs font-black shadow-sm">{{ departures.length }}</span>
          </div>
          <div class="flex-1 overflow-y-auto max-h-[500px] custom-scrollbar">
            <div v-if="!departures.length" class="py-20 text-center opacity-30">
              <i class="las la-walking text-5xl mb-4 block"></i>
              <p class="text-[10px] font-bold uppercase tracking-widest">Aucun départ prévu</p>
            </div>
            <div v-else class="divide-y divide-slate-50">
              <div v-for="r in departures" :key="r.id" class="p-6 hover:bg-slate-50/50 transition-all group">
                <div class="flex items-center justify-between gap-4">
                  <div class="flex items-center gap-4">
                    <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(r.user?.name || 'U')}&background=f8fafc&color=0f172a&bold=true`"
                         class="w-10 h-10 rounded-xl border border-slate-100" />
                    <div>
                      <p class="text-xs font-bold text-primary truncate max-w-[120px]">{{ r.user?.name }}</p>
                      <p class="text-[9px] font-bold text-accent uppercase tracking-widest">CH {{ r.room?.number }}</p>
                    </div>
                  </div>
                  <button v-if="r.status === 'checked_in'" @click="checkOut(r)"
                          class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-blue-500 hover:text-white transition-all">
                    Check-out
                  </button>
                  <span v-else class="text-[9px] font-black text-blue-500 uppercase tracking-widest"><i class="las la-check-circle mr-1"></i>Parti</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- État des Chambres & Flux -->
        <div class="bg-white rounded-[3rem] border border-slate-100 shadow-2xl shadow-primary/5 overflow-hidden flex flex-col">
          <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-slate-50">
            <div class="flex items-center gap-4">
              <span class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/20"><i class="las la-hotel text-xl"></i></span>
              <h3 class="text-sm font-bold text-primary uppercase tracking-widest">Contrôle Inventaire</h3>
            </div>
            <span class="px-4 py-1.5 rounded-full bg-white text-primary text-xs font-black shadow-sm">{{ availableRooms.length }} Dispo</span>
          </div>
          
          <div class="flex-1 overflow-y-auto max-h-[400px] custom-scrollbar p-4">
            <div class="grid grid-cols-2 gap-3">
              <div v-for="room in allRooms" :key="room.id"
                   class="p-4 rounded-2xl border transition-all hover:shadow-lg"
                   :class="room.status === 'available' ? 'bg-emerald-50/30 border-emerald-100' : 'bg-white border-slate-100'">
                <div class="flex items-center justify-between mb-2">
                  <p class="text-xs font-black text-primary">N°{{ room.number }}</p>
                  <div class="w-2 h-2 rounded-full" :class="roomStatusColor(room.status)"></div>
                </div>
                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest truncate mb-3">{{ room.room_type?.name }}</p>
                <select @change="changeRoomStatus(room, $event.target.value)"
                        :value="room.status"
                        class="w-full text-[8px] font-black uppercase tracking-widest bg-white border border-slate-100 rounded-lg py-1.5 px-2 outline-none focus:ring-2 focus:ring-accent/20 cursor-pointer transition-all">
                  <option value="available">DISPONIBLE</option>
                  <option value="occupied">OCCUPÉE</option>
                  <option value="maintenance">MAINTENANCE</option>
                  <option value="blocked">BLOQUÉE</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Bottom Summary Strip -->
          <div class="p-6 bg-slate-50 border-t border-slate-100 grid grid-cols-3 gap-4">
            <div class="text-center">
              <p class="text-emerald-600 font-serif text-lg">{{ roomSummary.available }}</p>
              <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Dispo</p>
            </div>
            <div class="text-center">
              <p class="text-accent font-serif text-lg">{{ roomSummary.occupied }}</p>
              <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Occupées</p>
            </div>
            <div class="text-center">
              <p class="text-amber-600 font-serif text-lg">{{ roomSummary.maintenance }}</p>
              <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Entretien</p>
            </div>
          </div>
        </div>

      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminLayout from '@/components/admin/AdminLayout.vue'
import api from '@/api/axios'

const router    = useRouter()
const authStore = useAuthStore()

const todayData    = ref([])
const allRooms     = ref([])
const searchQuery  = ref('')
const searchResult = ref(null)
const searchError  = ref('')
const currentTime  = ref('')
let   clockTimer   = null

const today = new Date().toLocaleDateString('fr-FR', {
  weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
})

const arrivals = computed(() =>
  todayData.value.filter(r => new Date(r.check_in).toDateString() === new Date().toDateString())
)
const departures = computed(() =>
  todayData.value.filter(r =>
    new Date(r.check_out).toDateString() === new Date().toDateString() &&
    r.status === 'checked_in'
  )
)
const availableRooms = computed(() => allRooms.value.filter(r => r.status === 'available'))
const roomSummary = computed(() => ({
  available:   allRooms.value.filter(r => r.status === 'available').length,
  occupied:    allRooms.value.filter(r => r.status === 'occupied').length,
  maintenance: allRooms.value.filter(r => r.status === 'maintenance').length,
}))

const todayStats = computed(() => [
  { label: 'Arrivées',     value: arrivals.value.length,      sub: "Aujourd'hui",    color: '#10b981', icon: 'las la-sign-in-alt' },
  { label: 'Départs',      value: departures.value.length,    sub: "À valider",      color: '#3b82f6', icon: 'las la-sign-out-alt' },
  { label: 'Disponibles',  value: availableRooms.value.length,sub: 'Unités libres',  color: '#b45309', icon: 'las la-hotel' },
  { label: 'Occupées',     value: roomSummary.value.occupied, sub: 'Résidents',      color: '#f59e0b', icon: 'las la-user-friends' },
])

const checkIn  = async (r) => { 
  try {
    await api.post(`/admin/reservations/${r.id}/check-in`)
    r.status = 'checked_in'
    if (searchResult.value?.id === r.id) searchResult.value.status = 'checked_in'
  } catch (e) { console.error(e) }
}
const checkOut = async (r) => { 
  try {
    await api.post(`/admin/reservations/${r.id}/check-out`)
    r.status = 'checked_out'
    if (searchResult.value?.id === r.id) searchResult.value.status = 'checked_out'
  } catch (e) { console.error(e) }
}

const changeRoomStatus = async (room, status) => {
  try {
    await api.post(`/admin/rooms/${room.id}/status`, { status })
    room.status = status
  } catch (e) { console.error(e) }
}

const searchReservation = async () => {
  searchError.value  = ''
  searchResult.value = null
  if (!searchQuery.value.trim()) return
  try {
    const { data } = await api.get('/admin/reservations', { params: { search: searchQuery.value } })
    const results = data.data ?? data
    if (results.length > 0) searchResult.value = results[0]
    else searchError.value = 'Aucune réservation trouvée.'
  } catch { searchError.value = 'Erreur lors de la recherche.' }
}

const updateClock = () => {
  currentTime.value = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
}

const roomStatusColor = (s) => ({
  available: 'bg-emerald-500', occupied: 'bg-accent', maintenance: 'bg-amber-500', blocked: 'bg-rose-500'
}[s] ?? 'bg-slate-300')

const statusLabel = (s) => ({ pending: 'En attente', confirmed: 'Confirmée', checked_in: 'Présent', checked_out: 'Parti', cancelled: 'Annulée' }[s] ?? s)
const statusStyle = (s) => ({
  pending: 'background: #fef3c7; color: #b45309;', confirmed: 'background: #dbeafe; color: #1d4ed8;',
  checked_in: 'background: #dcfce7; color: #15803d;', checked_out: 'background: #f3e8ff; color: #7e22ce;',
  cancelled: 'background: #fee2e2; color: #b91c1c;',
}[s] ?? '')
const statusDotStyle = (s) => ({
  pending: 'background-color: #f59e0b;', confirmed: 'background-color: #3b82f6;',
  checked_in: 'background-color: #10b981;', checked_out: 'background-color: #a855f7;',
  cancelled: 'background-color: #ef4444;',
}[s] ?? '')

const logout = async () => { await authStore.logout(); router.push('/login') }

onMounted(async () => {
  updateClock()
  clockTimer = setInterval(updateClock, 1000)
  const [todayRes, roomsRes] = await Promise.allSettled([
    api.get('/admin/dashboard/today'),
    api.get('/admin/rooms'),
  ])
  if (todayRes.status === 'fulfilled') todayData.value = todayRes.value.data
  if (roomsRes.status === 'fulfilled') allRooms.value  = roomsRes.value.data.data ?? roomsRes.value.data
})

onUnmounted(() => clearInterval(clockTimer))
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

.animate-scale-in { animation: scaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
