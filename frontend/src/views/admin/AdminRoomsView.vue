<template>
  <AdminLayout page-title="Gestion des Chambres" @logout="logout">

    <!-- Simple Controls -->
    <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-6 animate-fade-in">
      <div class="flex flex-wrap gap-3">
        <button @click="selectedFloor = 'all'"
                class="px-6 py-3 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all border"
                :class="selectedFloor === 'all' ? 'bg-primary border-primary text-white shadow-lg' : 'bg-white border-slate-100 text-slate-400 hover:text-primary'">
          Toutes
        </button>
        <button v-for="f in floors" :key="f" @click="selectedFloor = f"
                class="px-6 py-3 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all border"
                :class="selectedFloor === f ? 'bg-primary border-primary text-white shadow-lg' : 'bg-white border-slate-100 text-slate-400 hover:text-primary'">
          Étage {{ f }}
        </button>
      </div>

      <!-- The status legend has been removed as it is now included in the 3D model -->
    </div>

    <!-- Loading -->
    <div v-if="loading" class="py-40 text-center text-slate-400">
      <div class="w-10 h-10 border-4 border-accent border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
      <p class="text-xs font-bold uppercase tracking-widest">Mise à jour du plan...</p>
    </div>

    <!-- 3D Hotel Model -->
    <div v-else class="animate-fade-in" style="animation-delay: 0.1s">
      <HotelModel :rooms="filteredRooms" @select-room="editRoom" />
    </div>

    <!-- Simple Edit Modal -->
    <Transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-primary/20 backdrop-blur-sm p-6" @click.self="showModal = false">
        <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl p-10 relative">
          <button @click="showModal = false" class="absolute top-8 right-8 text-slate-400 hover:text-primary"><i class="las la-times text-2xl"></i></button>
          
          <h3 class="text-3xl font-serif text-primary mb-2">Chambre {{ editingRoom?.number }}</h3>
          <p class="text-accent text-[10px] font-bold uppercase tracking-widest mb-10">{{ editingRoom?.room_type?.name }} — Étage {{ editingRoom?.floor }}</p>
          
          <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-4 custom-scrollbar">
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Numéro</label>
                <input v-model="form.number" type="text" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all" />
              </div>
              <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Étage</label>
                <select v-model="form.floor" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all appearance-none cursor-pointer">
                  <option v-for="f in floors" :key="f" :value="f">Étage {{ f }}</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Type</label>
                <select v-model="form.room_type_id" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all appearance-none cursor-pointer">
                  <option v-for="type in roomTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                </select>
              </div>
              <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Surface (m²)</label>
                <input v-model="form.surface" type="number" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Vue</label>
                <select v-model="form.view" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all appearance-none cursor-pointer">
                  <option value="">Aucune vue spécifique</option>
                  <option value="Mer">Vue Mer</option>
                  <option value="Piscine">Vue Piscine</option>
                  <option value="Jardin">Vue Jardin</option>
                  <option value="Ville">Vue Ville</option>
                  <option value="Montagne">Vue Montagne</option>
                  <option value="Cour">Vue sur Cour</option>
                </select>
              </div>
              <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Prix Forcé (Optionnel)</label>
                <input v-model="form.price_override" type="number" step="0.01" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all" placeholder="MAD" />
              </div>
            </div>

            <div class="flex items-center gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100 cursor-pointer" @click="form.smoking = !form.smoking">
              <div class="w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all" :class="form.smoking ? 'bg-primary border-primary' : 'border-slate-300 bg-white'">
                <i v-if="form.smoking" class="las la-check text-white text-sm"></i>
              </div>
              <label class="text-[10px] font-bold text-slate-600 uppercase tracking-widest cursor-pointer">Chambre Fumeur</label>
            </div>

            <div>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Statut de la Chambre</p>
              <div class="grid grid-cols-2 gap-4">
                <button v-for="s in ['available', 'occupied', 'maintenance', 'blocked']" :key="s"
                        @click="form.status = s"
                        class="px-6 py-4 rounded-2xl border-2 text-[10px] font-bold uppercase tracking-widest transition-all flex items-center gap-3"
                        :class="form.status === s ? 'border-primary bg-primary text-white shadow-md' : 'border-slate-50 bg-slate-50/50 text-slate-400 hover:border-slate-100'">
                  <div :class="statusClass(s)" class="w-2 h-2 rounded-full"></div>
                  {{ statusLabel(s) }}
                </button>
              </div>
            </div>

            <div class="pt-6">
               <button @click="saveRoom" :disabled="saving"
                       class="w-full py-5 bg-primary text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-accent transition-all shadow-xl disabled:opacity-50">
                 {{ saving ? 'Enregistrement...' : 'Enregistrer les modifications' }}
               </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>

  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminLayout from '@/components/admin/AdminLayout.vue'
import HotelModel from '@/components/rooms/HotelModel.vue'
import api from '@/api/axios'

const router = useRouter()
const authStore = useAuthStore()

const rooms = ref([])
const roomTypes = ref([])
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const editingRoom = ref(null)
const selectedFloor = ref('all')
const floors = ['A', 'B', 'C', 'D', 'E', 'F']

const form = reactive({
  number: '',
  floor: '',
  room_type_id: null,
  surface: '',
  view: '',
  price_override: '',
  smoking: false,
  status: 'available'
})

const filteredRooms = computed(() => {
  if (selectedFloor.value === 'all') return rooms.value
  return rooms.value.filter(r => String(r.floor) === String(selectedFloor.value))
})

const loadData = async () => {
  loading.value = true
  try {
    const [roomsRes, typesRes] = await Promise.all([
      api.get('/admin/rooms'),
      api.get('/rooms/types')
    ])
    rooms.value = roomsRes.data.data ?? roomsRes.data
    roomTypes.value = typesRes.data.data ?? typesRes.data
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

const editRoom = (room) => {
  editingRoom.value = room
  Object.assign(form, {
    number: room.number,
    floor: room.floor,
    room_type_id: room.room_type_id,
    surface: room.surface,
    view: room.view || '',
    price_override: room.price_override || '',
    smoking: !!room.smoking,
    status: room.status
  })
  showModal.value = true
}

const saveRoom = async () => {
  saving.value = true
  try {
    const payload = { ...form }
    if (payload.price_override === '') payload.price_override = null
    await api.put(`/admin/rooms/${editingRoom.value.id}`, payload)
    showModal.value = false
    await loadData()
  } catch (e) { console.error(e) }
  finally { saving.value = false }
}

const statusClass = (s) => ({
  'bg-emerald-500': s === 'available',
  'bg-amber-500':   s === 'occupied',
  'bg-rose-500':    s === 'maintenance',
  'bg-slate-400':   s === 'blocked',
}[s])

const statusLabel = (s) => ({ available: 'Libre', occupied: 'Occupée', maintenance: 'Travaux', blocked: 'Bloquée' }[s])
const logout = async () => { await authStore.logout(); router.push('/login') }

onMounted(loadData)
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Custom Scrollbar for the modal */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9; 
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}
</style>
