<template>
  <AdminLayout page-title="Offres & Conventions" @logout="logout">

    <!-- Modern Header -->
    <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-8 animate-slide-up">
      <div class="max-w-2xl">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-[2px] bg-accent"></div>
          <p class="text-[10px] font-bold text-accent uppercase tracking-[0.4em]">Privilèges & Conventions</p>
        </div>
        <h1 class="text-5xl font-serif text-primary leading-tight">Accords <span class="italic text-accent">Exclusifs</span></h1>
        <p class="text-slate-400 font-light mt-4 text-sm leading-relaxed max-w-lg">
          Pilotez les offres promotionnelles et les conventions d'entreprise avec une précision absolue.
        </p>
      </div>
      
      <button @click="openCreate"
              class="px-10 py-5 bg-primary text-white rounded-[1.5rem] text-[10px] font-bold uppercase tracking-widest hover:bg-accent transition-all duration-500 shadow-xl shadow-primary/10 flex items-center gap-3 active:scale-95 group">
        <i class="las la-plus text-lg transition-transform group-hover:rotate-90"></i>
        Nouvelle Offre
      </button>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12 animate-slide-up" style="animation-delay: 0.1s">
      <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-primary/5 flex items-center gap-6">
        <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-500">
          <i class="las la-check-circle text-3xl"></i>
        </div>
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Offres Actives</p>
          <p class="text-2xl font-serif text-primary">{{ promotions.filter(p => p.is_active).length }}</p>
        </div>
      </div>
      <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-primary/5 flex items-center gap-6">
        <div class="w-16 h-16 rounded-2xl bg-accent/5 flex items-center justify-center text-accent">
          <i class="las la-handshake text-3xl"></i>
        </div>
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Conventions</p>
          <p class="text-2xl font-serif text-primary">{{ promotions.filter(p => p.company_name).length }}</p>
        </div>
      </div>
      <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-primary/5 flex items-center gap-6">
        <div class="w-16 h-16 rounded-2xl bg-primary/5 flex items-center justify-center text-primary">
          <i class="las la-chart-bar text-3xl"></i>
        </div>
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Utilisations</p>
          <p class="text-2xl font-serif text-primary">{{ promotions.reduce((acc, p) => acc + p.used_count, 0) }}</p>
        </div>
      </div>
    </div>

    <!-- Filters Bar -->
    <div class="mb-10 flex flex-col md:flex-row gap-6 items-center justify-between animate-slide-up" style="animation-delay: 0.2s">
      <div class="relative w-full md:max-w-md group">
        <i class="las la-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-accent transition-colors"></i>
        <input v-model="searchQuery" type="text" placeholder="Rechercher un code ou un partenaire..."
               class="w-full pl-16 pr-8 py-5 bg-white border border-slate-100 rounded-[1.5rem] text-sm font-medium text-primary focus:ring-4 focus:ring-accent/5 outline-none transition-all shadow-sm" />
      </div>
      
      <div class="flex items-center gap-4">
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Trier par :</span>
        <select v-model="sortBy" class="bg-white border border-slate-100 rounded-xl px-4 py-2 text-xs font-bold text-primary outline-none cursor-pointer">
          <option value="latest">Plus récentes</option>
          <option value="value">Valeur</option>
          <option value="usage">Usages</option>
        </select>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-40">
      <div class="relative w-16 h-16 mb-8">
        <div class="absolute inset-0 border-4 border-[#D4AF37]/10 rounded-full"></div>
        <div class="absolute inset-0 border-4 border-[#D4AF37] rounded-full border-t-transparent animate-spin"></div>
      </div>
      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.4em]">Chargement des Offres...</p>
    </div>

    <!-- Promotions Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10 mb-20 animate-slide-up" style="animation-delay: 0.1s">
      <div v-for="promo in filteredPromotions" :key="promo.id"
           class="group bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-primary/5 overflow-hidden transition-all duration-700 hover:shadow-2xl hover:shadow-primary/10 hover:-translate-y-2 relative">

        <!-- Banner Image -->
        <div class="h-48 w-full relative overflow-hidden bg-slate-100">
          <img :src="promo.logo ? getLogoUrl(promo.logo) : 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800&q=80'" 
               class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
          <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-primary/20 to-transparent"></div>
          
          <div class="absolute top-6 right-6">
             <div class="flex items-center gap-2 px-3 py-1.5 bg-white/10 backdrop-blur-md rounded-full border border-white/20">
               <div class="w-1.5 h-1.5 rounded-full" :class="promo.is_active ? 'bg-emerald-400 animate-pulse' : 'bg-slate-400'"></div>
               <span class="text-[8px] font-bold text-white uppercase tracking-widest">{{ promo.is_active ? 'Actif' : 'Inactif' }}</span>
             </div>
          </div>

          <div class="absolute bottom-6 left-8 right-8">
             <span class="text-[8px] font-bold uppercase tracking-widest px-3 py-1 bg-accent text-white rounded-lg mb-2 inline-block shadow-lg">
               {{ promo.company_name ? 'CONVENTION PARTENAIRE' : 'OFFRE PRIVILÈGE' }}
             </span>
             <h3 class="text-3xl font-serif text-white tracking-wide drop-shadow-lg">
               {{ promo.code }}
             </h3>
          </div>
        </div>

        <!-- Content -->
        <div class="p-8">
          <div class="flex justify-between items-start mb-6">
            <div>
              <h4 class="text-lg font-serif text-primary mb-1">{{ promo.name }}</h4>
              <p v-if="promo.company_name" class="text-[10px] font-bold text-accent uppercase tracking-widest">{{ promo.company_name }}</p>
            </div>
            <div class="text-right">
              <span class="text-3xl font-serif text-primary">{{ promo.value }}</span>
              <span class="text-sm font-bold text-accent">{{ promo.type === 'percentage' ? '%' : ' MAD' }}</span>
            </div>
          </div>

          <p class="text-xs text-slate-400 font-light leading-relaxed mb-8 h-12 line-clamp-3">
            {{ promo.description || 'Privilège exclusif réservé aux résidents distingués du Grand Palais.' }}
          </p>

          <div class="grid grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
              <span class="text-[8px] font-bold uppercase tracking-widest text-slate-400 block mb-1">Usages</span>
              <span class="text-xs font-bold text-primary">{{ promo.used_count }} <span class="text-slate-300">/ {{ promo.max_uses || '∞' }}</span></span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
              <span class="text-[8px] font-bold uppercase tracking-widest text-slate-400 block mb-1">Expiration</span>
              <span class="text-xs font-bold" :class="isExpired(promo.expires_at) ? 'text-rose-500' : 'text-primary'">{{ formatDate(promo.expires_at) }}</span>
            </div>
          </div>

          <!-- Quick Action Overlay -->
          <div class="absolute inset-0 flex items-center justify-center gap-4 opacity-0 group-hover:opacity-100 transition-all duration-500 z-20 backdrop-blur-sm bg-primary/60">
            <button @click.stop="editPromo(promo)"
                    class="w-12 h-12 flex items-center justify-center rounded-2xl bg-accent text-white shadow-xl transition-all duration-500 hover:scale-110 active:scale-95">
              <i class="las la-edit text-xl"></i>
            </button>
            <button @click.stop="toggleActive(promo)"
                    class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white text-primary shadow-xl transition-all duration-500 hover:scale-110 active:scale-95">
              <i :class="promo.is_active ? 'las la-pause' : 'las la-play'" class="text-xl"></i>
            </button>
            <button @click.stop="deletePromo(promo)"
                    class="w-12 h-12 flex items-center justify-center rounded-2xl bg-rose-500 text-white shadow-xl transition-all duration-500 hover:scale-110 active:scale-95">
              <i class="las la-trash text-xl"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Management Modal -->
    <Transition name="modal">
      <div v-if="showModal"
           class="fixed inset-0 z-[100] flex items-center justify-center p-6 backdrop-blur-xl bg-primary/20"
           @click.self="showModal = false">
        <div class="bg-white max-w-3xl w-full rounded-[3.5rem] shadow-2xl relative border border-slate-100 animate-slide-up max-h-[90vh] overflow-y-auto custom-scrollbar">
          
          <button @click="showModal = false"
                  class="absolute top-10 right-10 w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-primary hover:text-white transition-all duration-500 z-10">
            <i class="las la-times text-xl"></i>
          </button>

          <div class="p-12 md:p-16">
            <div class="mb-12">
              <p class="text-[10px] font-bold text-accent uppercase tracking-[0.4em] mb-2">Configurations</p>
              <h3 class="text-4xl font-serif text-primary">{{ editingPromo ? 'Raffiner l\'Offre' : 'Nouvelle Signature' }}</h3>
            </div>

            <div class="space-y-10">
              
              <!-- Section: Identity & Partner -->
              <div class="space-y-6">
                <h4 class="text-[10px] font-bold text-primary uppercase tracking-widest flex items-center gap-3">
                  <span class="w-8 h-px bg-slate-200"></span> Identité & Partenariat
                </h4>
                
                <div class="flex flex-col md:flex-row gap-8 items-start">
                   <!-- Logo/Image Preview -->
                   <div class="relative group w-32 h-32 rounded-3xl overflow-hidden bg-slate-50 border-2 border-dashed border-slate-200 hover:border-accent transition-all shrink-0">
                     <input type="file" @change="handleLogoUpload" accept="image/*" class="absolute inset-0 opacity-0 z-10 cursor-pointer" />
                     <img v-if="logoPreview || (editingPromo && editingPromo.logo)" 
                          :src="logoPreview || getLogoUrl(editingPromo.logo)" 
                          class="w-full h-full object-cover" />
                     <div v-else class="absolute inset-0 flex flex-col items-center justify-center text-slate-300 gap-1">
                       <i class="las la-image text-3xl"></i>
                       <span class="text-[8px] font-bold uppercase tracking-widest text-center px-2">Visuel</span>
                     </div>
                     <div class="absolute inset-0 bg-primary/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <i class="las la-camera text-white text-xl"></i>
                     </div>
                   </div>

                   <div class="flex-1 space-y-6 w-full">
                      <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Code Privilège</label>
                        <input v-model="form.code" type="text" placeholder="Ex: OCP2026"
                               class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-lg font-serif text-primary focus:ring-2 focus:ring-accent/10 outline-none uppercase placeholder:text-slate-300" />
                      </div>
                      <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Nom du Partenaire (Convention)</label>
                        <input v-model="form.company_name" type="text" placeholder="Ex: Groupe OCP, Ministère..."
                               class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-primary focus:ring-2 focus:ring-accent/10 outline-none placeholder:text-slate-300" />
                      </div>
                   </div>
                </div>
              </div>

              <!-- Section: Value & Details -->
              <div class="space-y-6">
                <h4 class="text-[10px] font-bold text-primary uppercase tracking-widest flex items-center gap-3">
                  <span class="w-8 h-px bg-slate-200"></span> Valeur & Description
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Type de Réduction</label>
                    <select v-model="form.type" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-primary focus:ring-2 focus:ring-accent/10 outline-none appearance-none cursor-pointer">
                      <option value="percentage">Pourcentage (%)</option>
                      <option value="fixed">Montant Fixe (MAD)</option>
                    </select>
                  </div>
                  <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Valeur</label>
                    <div class="relative">
                      <input v-model.number="form.value" type="number" step="0.01" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-primary focus:ring-2 focus:ring-accent/10 outline-none" />
                      <span class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-bold text-accent">{{ form.type === 'percentage' ? '%' : 'MAD' }}</span>
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Intitulé de l'Offre</label>
                  <input v-model="form.name" type="text" placeholder="Ex: Convention Exclusive Cadres"
                         class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-primary focus:ring-2 focus:ring-accent/10 outline-none placeholder:text-slate-300" />
                </div>

                <div class="space-y-2">
                  <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Description</label>
                  <textarea v-model="form.description" rows="2" placeholder="Détails de l'avantage..."
                            class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-medium text-slate-600 focus:ring-2 focus:ring-accent/10 outline-none resize-none placeholder:text-slate-300"></textarea>
                </div>
              </div>

              <!-- Section: Constraints & Validity -->
              <div class="space-y-6">
                <h4 class="text-[10px] font-bold text-primary uppercase tracking-widest flex items-center gap-3">
                  <span class="w-8 h-px bg-slate-200"></span> Contraintes & Validité
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Montant Min. (MAD)</label>
                    <input v-model.number="form.min_amount" type="number" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-primary focus:ring-2 focus:ring-accent/10 outline-none" />
                  </div>
                  <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Usages Max.</label>
                    <input v-model.number="form.max_uses" type="number" placeholder="Illimité" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-primary focus:ring-2 focus:ring-accent/10 outline-none" />
                  </div>
                  <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Date de Début</label>
                    <input v-model="form.starts_at" type="datetime-local" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-primary focus:ring-2 focus:ring-accent/10 outline-none" />
                  </div>
                  <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Date d'Expiration</label>
                    <input v-model="form.expires_at" type="datetime-local" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-primary focus:ring-2 focus:ring-accent/10 outline-none" />
                  </div>
                </div>

                <label class="flex items-center gap-4 cursor-pointer p-4 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-colors">
                  <input type="checkbox" v-model="form.is_active" class="hidden" />
                  <div class="w-6 h-6 rounded-lg border-2 border-slate-200 flex items-center justify-center transition-all" :class="{'bg-accent border-accent': form.is_active}">
                    <i v-if="form.is_active" class="las la-check text-white text-xs"></i>
                  </div>
                  <span class="text-[10px] font-bold text-primary uppercase tracking-widest">Activer l'offre immédiatement</span>
                </label>
              </div>

              <div v-if="saveError" class="p-6 rounded-2xl bg-rose-50 border-l-4 border-rose-500 text-rose-600 text-[10px] font-bold uppercase tracking-widest animate-shake">
                {{ saveError }}
              </div>

              <!-- Modal Actions -->
              <div class="flex flex-col sm:flex-row gap-4 pt-6">
                <button @click="savePromo" :disabled="saving || !form.code || !form.value"
                        class="flex-1 px-8 py-5 bg-primary text-white rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-accent transition-all duration-500 disabled:opacity-50 shadow-xl shadow-primary/10">
                  {{ saving ? 'TRAITEMENT...' : 'ENREGISTRER LE PRIVILÈGE' }}
                </button>
                <button @click="showModal = false"
                        class="px-8 py-5 bg-slate-100 text-slate-500 rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-slate-200 transition-all duration-500">
                  ANNULER
                </button>
              </div>
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
import api from '@/api/axios'

const router    = useRouter()
const authStore = useAuthStore()

const promotions   = ref([])
const searchQuery  = ref('')
const sortBy       = ref('latest')
const loading      = ref(false)
const saving       = ref(false)
const showModal    = ref(false)
const editingPromo = ref(null)
const saveError    = ref('')
const logoFile     = ref(null)
const logoPreview  = ref(null)

const form = reactive({
  code:         '',
  name:         '',
  description:  '',
  company_name: '',
  type:         'percentage',
  value:        0,
  min_amount:   null,
  max_uses:     null,
  starts_at:    '',
  expires_at:   '',
  is_active:    true,
})

const filteredPromotions = computed(() => {
  let list = [...promotions.value]
  
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(p => 
      p.code.toLowerCase().includes(q) || 
      p.name.toLowerCase().includes(q) || 
      (p.company_name && p.company_name.toLowerCase().includes(q))
    )
  }
  
  if (sortBy.value === 'value') {
    list.sort((a, b) => b.value - a.value)
  } else if (sortBy.value === 'usage') {
    list.sort((a, b) => b.used_count - a.used_count)
  } else {
    list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
  }
  
  return list
})

const loadData = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/promotions')
    promotions.value = data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  editingPromo.value = null
  saveError.value    = ''
  logoFile.value     = null
  logoPreview.value  = null
  Object.assign(form, {
    code: '', name: '', description: '', company_name: '', type: 'percentage',
    value: 0, min_amount: null, max_uses: null, starts_at: '', expires_at: '', is_active: true,
  })
  showModal.value = true
}

const editPromo = (promo) => {
  editingPromo.value = promo
  saveError.value    = ''
  logoFile.value     = null
  logoPreview.value  = null
  
  const formatForInput = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toISOString().slice(0, 16)
  }

  Object.assign(form, {
    code:         promo.code,
    name:         promo.name,
    description:  promo.description || '',
    company_name: promo.company_name || '',
    type:         promo.type,
    value:        promo.value,
    min_amount:   promo.min_amount,
    max_uses:     promo.max_uses,
    starts_at:    formatForInput(promo.starts_at),
    expires_at:   formatForInput(promo.expires_at),
    is_active:    promo.is_active,
  })
  showModal.value = true
}

const handleLogoUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  logoFile.value = file
  const reader = new FileReader()
  reader.onload = (e) => {
    logoPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const savePromo = async () => {
  saving.value    = true
  saveError.value = ''
  
  try {
    const formData = new FormData()
    formData.append('code', form.code)
    formData.append('name', form.name)
    formData.append('description', form.description || '')
    formData.append('company_name', form.company_name || '')
    formData.append('type', form.type)
    formData.append('value', form.value)
    formData.append('is_active', form.is_active ? 1 : 0)
    
    if (form.min_amount) formData.append('min_amount', form.min_amount)
    if (form.max_uses) formData.append('max_uses', form.max_uses)
    if (form.starts_at) formData.append('starts_at', form.starts_at)
    if (form.expires_at) formData.append('expires_at', form.expires_at)
    
    if (logoFile.value) {
      formData.append('logo', logoFile.value)
    }

    if (editingPromo.value) {
      formData.append('_method', 'PUT')
      await api.post(`/admin/promotions/${editingPromo.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await api.post('/admin/promotions', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }
    
    showModal.value = false
    await loadData()
  } catch (e) {
    saveError.value = e.response?.data?.message || 'Erreur lors de la sauvegarde.'
  } finally {
    saving.value = false
  }
}

const toggleActive = async (promo) => {
  try {
    await api.patch(`/admin/promotions/${promo.id}/toggle`)
    promo.is_active = !promo.is_active
  } catch (e) {
    console.error(e)
  }
}

const deletePromo = async (promo) => {
  if (!confirm(`Supprimer la promotion "${promo.code}" ?`)) return
  try {
    await api.delete(`/admin/promotions/${promo.id}`)
    promotions.value = promotions.value.filter(p => p.id !== promo.id)
  } catch (e) {
    if (e.response?.status === 422) {
      alert(e.response.data.message)
    } else {
      console.error(e)
    }
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'Jamais'
  return new Intl.DateTimeFormat('fr-FR', { 
    day: '2-digit', month: 'short', year: 'numeric' 
  }).format(new Date(dateString))
}

const isExpired = (dateString) => {
  if (!dateString) return false
  return new Date(dateString) < new Date()
}

const getLogoUrl = (path) => {
  if (!path) return ''
  return `http://localhost:8000/storage/${path}`
}

const logout = async () => { await authStore.logout(); router.push('/login') }

onMounted(loadData)
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
.modal-enter-from, .modal-leave-to { opacity: 0; transform: scale(0.9) translateY(40px); filter: blur(10px); }

select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23b45309' stroke-width='3'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5' /%3E%3C/svg%3E");
  background-position: right 1.5rem center;
  background-repeat: no-repeat;
  background-size: 1.25rem;
}

::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #D4AF37; }
</style>
