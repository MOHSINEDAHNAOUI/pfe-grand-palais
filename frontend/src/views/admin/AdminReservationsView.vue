<template>
  <AdminLayout page-title="Gestion des Réservations" @logout="logout">

    <!-- Simple Filters -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mb-8 flex flex-wrap items-center gap-4 animate-fade-in">
      <div class="flex-1 min-w-[300px] relative">
        <i class="las la-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
        <input v-model="filters.search" @input="debouncedLoad" type="text" placeholder="Rechercher un client ou une référence..." 
               class="w-full pl-14 pr-6 py-4 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/10 outline-none transition-all" />
      </div>
      
      <select v-model="filters.status" @change="loadReservations" 
              class="px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm outline-none cursor-pointer focus:ring-2 focus:ring-primary/10">
        <option value="">Tous les statuts</option>
        <option value="pending">En attente</option>
        <option value="confirmed">Confirmé</option>
        <option value="checked_in">Arrivé</option>
        <option value="checked_out">Parti</option>
        <option value="cancelled">Annulé</option>
      </select>

      <input v-model="filters.date" @change="loadReservations" type="date" 
             class="px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm outline-none cursor-pointer focus:ring-2 focus:ring-primary/10" />
      
      <button @click="resetFilters" class="p-4 bg-primary text-white rounded-2xl hover:bg-accent transition-all">
        <i class="las la-sync"></i>
      </button>
    </div>

    <!-- Main Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden animate-fade-in" style="animation-delay: 0.1s">
      <div v-if="loading" class="py-32 text-center text-slate-400">
        <div class="w-10 h-10 border-4 border-accent border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-xs font-bold uppercase tracking-widest">Chargement des données...</p>
      </div>

      <div v-else-if="!reservations.length" class="py-32 text-center text-slate-300">
        <i class="las la-inbox text-5xl mb-4 opacity-20"></i>
        <p class="italic">Aucune réservation trouvée</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50 border-b border-slate-50">
              <th class="py-5 px-8">Référence</th>
              <th class="py-5 px-8">Client</th>
              <th class="py-5 px-8">Chambre</th>
              <th class="py-5 px-8">Dates</th>
              <th class="py-5 px-8">Prix</th>
              <th class="py-5 px-8">Statut</th>
              <th class="py-5 px-8 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="r in reservations" :key="r.id" class="hover:bg-slate-50/50 transition-colors">
              <td class="py-5 px-8">
                <span class="font-bold text-primary">{{ r.reference }}</span>
              </td>
              <td class="py-5 px-8">
                <div class="flex items-center gap-4">
                  <img :src="getAvatarUrl(r.user)" class="w-10 h-10 rounded-xl object-cover" />
                  <div>
                    <p class="font-bold text-primary text-sm">{{ r.user?.name }}</p>
                    <p class="text-[10px] text-slate-400">{{ r.user?.email }}</p>
                  </div>
                </div>
              </td>
              <td class="py-5 px-8">
                <p class="font-bold text-primary">N° {{ r.room?.number }}</p>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest">{{ r.room?.room_type?.name }}</p>
              </td>
              <td class="py-5 px-8 text-[11px] font-bold text-slate-500">
                {{ formatDate(r.check_in) }} - {{ formatDate(r.check_out) }}
              </td>
              <td class="py-5 px-8">
                <span class="font-serif text-primary font-bold">{{ parseFloat(r.total_price).toLocaleString() }} MAD</span>
              </td>
              <td class="py-5 px-8">
                <span :class="statusStyle(r.status)" class="text-[9px] font-bold uppercase tracking-widest px-3 py-1 rounded-full border">
                  {{ statusLabel(r.status) }}
                </span>
              </td>
              <td class="py-5 px-8 text-right">
                <div class="flex justify-end gap-2">
                  <button v-if="r.status === 'pending'" @click="confirmReservation(r)" :disabled="actionLoading" class="px-4 py-2 bg-blue-500 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-blue-600 disabled:opacity-50">Confirmer</button>
                  <button v-if="r.status === 'confirmed'" @click="handleCheckIn(r)" :disabled="actionLoading" class="px-4 py-2 bg-emerald-500 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-600 disabled:opacity-50">Arrivée</button>
                  <button v-if="r.status === 'checked_in'" @click="handleCheckOut(r)" :disabled="actionLoading" class="px-4 py-2 bg-primary text-white rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-accent disabled:opacity-50">Départ</button>
                  <button @click="viewDetails(r)" class="p-2 bg-slate-100 text-slate-400 rounded-xl hover:bg-slate-200"><i class="las la-eye"></i></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Simple Pagination -->
      <div v-if="meta?.last_page > 1" class="p-6 bg-slate-50/50 flex items-center justify-between border-t border-slate-50">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
          {{ meta.from }}-{{ meta.to }} sur {{ meta.total }}
        </p>
        <div class="flex gap-2">
          <button @click="changePage(meta.current_page - 1)" :disabled="meta.current_page === 1" class="px-4 py-2 bg-white border border-slate-100 rounded-xl text-xs disabled:opacity-50">Précédent</button>
          <button @click="changePage(meta.current_page + 1)" :disabled="meta.current_page === meta.last_page" class="px-4 py-2 bg-white border border-slate-100 rounded-xl text-xs disabled:opacity-50">Suivant</button>
        </div>
      </div>
    </div>

    <!-- Simple Detail Modal -->
    <Transition name="fade">
      <div v-if="selectedReservation" class="fixed inset-0 z-[100] flex items-center justify-center bg-primary/20 backdrop-blur-sm p-6" @click.self="selectedReservation = null">
        <div class="bg-white w-full max-w-xl rounded-[2.5rem] shadow-2xl p-10 relative">
          <button @click="selectedReservation = null" class="absolute top-8 right-8 text-slate-400 hover:text-primary"><i class="las la-times text-2xl"></i></button>
          
          <h3 class="text-3xl font-serif text-primary mb-8">Détails Réservation</h3>
          
          <div class="space-y-6">
            <div class="flex justify-between py-3 border-b border-slate-50">
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Référence</span>
              <span class="font-bold text-primary">{{ selectedReservation.reference }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-slate-50">
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Client</span>
              <span class="font-bold text-primary">{{ selectedReservation.user?.name }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-slate-50">
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Chambre</span>
              <span class="font-bold text-primary">№ {{ selectedReservation.room?.number }} ({{ selectedReservation.room?.room_type?.name }})</span>
            </div>
            <div class="flex justify-between py-3 border-b border-slate-50">
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Séjour</span>
              <span class="font-bold text-primary">{{ formatDate(selectedReservation.check_in) }} - {{ formatDate(selectedReservation.check_out) }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-slate-50">
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Montant Total</span>
              <span class="text-xl font-serif text-accent font-bold">{{ parseFloat(selectedReservation.total_price).toLocaleString() }} MAD</span>
            </div>
            <div class="flex justify-between py-3 border-b border-slate-50">
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Statut</span>
              <span :class="statusStyle(selectedReservation.status)" class="text-[9px] font-bold uppercase tracking-widest px-4 py-1 rounded-full border">
                {{ statusLabel(selectedReservation.status) }}
              </span>
            </div>
          </div>

          <div class="mt-10 flex gap-4">
             <button v-if="selectedReservation.status === 'confirmed'"
               @click="handleCheckIn(selectedReservation)"
               :disabled="actionLoading"
               class="flex-1 bg-emerald-500 text-white py-4 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-emerald-600 transition-all disabled:opacity-60 flex items-center justify-center gap-2">
               <span v-if="actionLoading" class="w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
               {{ actionLoading ? 'Traitement...' : 'Arrivée Client' }}
             </button>
             <button v-if="selectedReservation.status === 'checked_in'"
               @click="handleCheckOut(selectedReservation)"
               :disabled="actionLoading"
               class="flex-1 bg-primary text-white py-4 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-accent transition-all disabled:opacity-60 flex items-center justify-center gap-2">
               <span v-if="actionLoading" class="w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
               {{ actionLoading ? 'Traitement...' : 'Départ Client' }}
             </button>
             <button @click="selectedReservation = null" class="px-8 py-4 bg-slate-100 text-slate-400 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Fermer</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Toast Notification -->
    <Transition name="toast">
      <div v-if="toast.show"
        class="fixed bottom-10 right-10 z-[200] flex items-center gap-4 px-8 py-5 rounded-2xl shadow-2xl text-white text-sm font-medium"
        :class="toast.type === 'success' ? 'bg-emerald-600' : 'bg-rose-600'">
        <i :class="toast.type === 'success' ? 'las la-check-circle text-2xl' : 'las la-exclamation-circle text-2xl'"></i>
        {{ toast.message }}
      </div>
    </Transition>

  </AdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminLayout from '@/components/admin/AdminLayout.vue'
import api from '@/api/axios'

const router = useRouter()
const authStore = useAuthStore()

const reservations = ref([])
const loading = ref(false)
const actionLoading = ref(false)
const meta = ref(null)
const selectedReservation = ref(null)
const currentPage = ref(1)
const filters = reactive({ status: '', search: '', date: '' })
const toast = reactive({ show: false, message: '', type: 'success' })
let debounceTimer = null

const showToast = (message, type = 'success') => {
  toast.message = message
  toast.type = type
  toast.show = true
  setTimeout(() => toast.show = false, 4000)
}

const loadReservations = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/reservations', { params: { page: currentPage.value, ...filters } })
    reservations.value = data.data
    meta.value = data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const debouncedLoad = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(loadReservations, 500)
}

const resetFilters = () => {
  Object.assign(filters, { status: '', search: '', date: '' })
  currentPage.value = 1
  loadReservations()
}

const changePage = (page) => { 
  if (page < 1 || page > meta.value?.last_page) return
  currentPage.value = page 
}

const confirmReservation = async (r) => {
  await api.post(`/admin/reservations/${r.id}/confirm`)
  r.status = 'confirmed'
  showToast('Réservation confirmée.')
}

const handleCheckIn = async (r) => {
  actionLoading.value = true
  try {
    await api.post(`/admin/reservations/${r.id}/check-in`)
    r.status = 'checked_in'
    showToast(`Arrivée enregistrée pour ${r.user?.name ?? 'le client'}.`)
    selectedReservation.value = null
    await loadReservations()
  } catch (e) {
    showToast(e.response?.data?.message || 'Erreur lors de l\'enregistrement.', 'error')
  } finally {
    actionLoading.value = false
  }
}

const handleCheckOut = async (r) => {
  actionLoading.value = true
  try {
    await api.post(`/admin/reservations/${r.id}/check-out`)
    r.status = 'checked_out'
    showToast(`Départ enregistré pour ${r.user?.name ?? 'le client'}.`)
    selectedReservation.value = null
    await loadReservations()
  } catch (e) {
    showToast(e.response?.data?.message || 'Erreur lors du départ.', 'error')
  } finally {
    actionLoading.value = false
  }
}

const viewDetails = (r) => { selectedReservation.value = r }
const logout = async () => { await authStore.logout(); router.push('/login') }

const formatDate = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' }) : '—'
const getAvatarUrl = (user) => {
  if (user?.avatar) {
    if (user.avatar.startsWith('http')) return user.avatar
    return `http://localhost:8000/storage/${user.avatar}`
  }
  const name = encodeURIComponent(user?.name || 'U')
  return `https://ui-avatars.com/api/?name=${name}&background=0f172a&color=fff&size=64&bold=true`
}
const statusLabel = (s) => ({ pending: 'En Attente', confirmed: 'Confirmé', checked_in: 'Présent', checked_out: 'Terminé', cancelled: 'Annulé' }[s] ?? s)
const statusStyle = (s) => ({
  pending: 'border-amber-100 text-amber-600 bg-amber-50',
  confirmed: 'border-blue-100 text-blue-600 bg-blue-50',
  checked_in: 'border-emerald-100 text-emerald-600 bg-emerald-50',
  checked_out: 'border-slate-100 text-slate-600 bg-slate-50',
  cancelled: 'border-red-100 text-red-600 bg-red-50',
}[s] ?? '')

watch(currentPage, loadReservations)
onMounted(loadReservations)
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
</style>
