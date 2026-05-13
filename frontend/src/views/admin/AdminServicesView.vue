<template>
  <AdminLayout page-title="Gestion des Services" @logout="logout">

    <!-- Simple Header Actions -->
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6 animate-fade-in">
      <div class="flex items-center gap-4 bg-white px-6 py-3 rounded-2xl border border-slate-100 shadow-sm overflow-x-auto no-scrollbar">
        <button @click="activeFilter = 'all'"
                class="px-5 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all"
                :class="activeFilter === 'all' ? 'bg-primary text-white shadow-md' : 'text-slate-400 hover:text-primary'">
          Tous
        </button>
        <button v-for="cat in categories" :key="cat" @click="activeFilter = cat"
                class="px-5 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all"
                :class="activeFilter === cat ? 'bg-primary text-white shadow-md' : 'text-slate-400 hover:text-primary'">
          {{ cat }}
        </button>
      </div>

      <button @click="openCreate" class="px-8 py-4 bg-primary text-white rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-accent transition-all shadow-xl flex items-center gap-2">
        <i class="las la-plus text-xl"></i> Nouveau Service
      </button>
    </div>

    <!-- Grid -->
    <div v-if="loading" class="py-40 text-center text-slate-400">
      <div class="w-10 h-10 border-4 border-accent border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
      <p class="text-xs font-bold uppercase tracking-widest">Chargement des prestations...</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 animate-fade-in" style="animation-delay: 0.1s">
      <div v-for="s in filteredServices" :key="s.id" 
           class="group bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-md transition-all relative">
        
        <div class="h-48 overflow-hidden relative">
          <img :src="getServiceImage(s)" class="w-full h-full object-cover transition-transform group-hover:scale-105" />
          <div class="absolute inset-0 bg-black/10"></div>
          <div class="absolute top-4 right-4">
             <div :class="s.is_active ? 'bg-emerald-500' : 'bg-slate-300'" class="w-3 h-3 rounded-full border-2 border-white shadow-sm"></div>
          </div>
        </div>

        <div class="p-6">
          <p class="text-[9px] font-bold text-accent uppercase tracking-widest mb-1">{{ s.category }}</p>
          <h3 class="text-lg font-serif text-primary mb-4 leading-tight">{{ s.name }}</h3>
          
          <div class="flex items-center justify-between mt-auto">
            <span class="text-xl font-serif text-primary font-bold">{{ parseFloat(s.price).toFixed(0) }} <span class="text-[10px] font-sans opacity-50">MAD</span></span>
            <div class="flex gap-2">
              <button @click="editService(s)" class="p-2 bg-slate-50 text-slate-400 rounded-lg hover:bg-primary hover:text-white transition-all"><i class="las la-edit"></i></button>
              <button @click="deleteService(s)" class="p-2 bg-slate-50 text-rose-300 rounded-lg hover:bg-rose-500 hover:text-white transition-all"><i class="las la-trash"></i></button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Simple Modal -->
    <Transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-primary/20 backdrop-blur-sm p-6" @click.self="showModal = false">
        <div class="bg-white w-full max-w-xl rounded-[2.5rem] shadow-2xl p-10 relative max-h-[90vh] overflow-y-auto custom-scrollbar">
          <button @click="showModal = false" class="absolute top-8 right-8 text-slate-400 hover:text-primary"><i class="las la-times text-2xl"></i></button>
          
          <h3 class="text-3xl font-serif text-primary mb-8">{{ editingService ? 'Modifier Service' : 'Nouveau Service' }}</h3>
          
          <div class="space-y-6">
            <!-- Image Upload -->
            <div class="space-y-2">
              <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Visuel du Service</label>
              <div class="relative group h-40 rounded-2xl overflow-hidden bg-slate-50 border-2 border-dashed border-slate-200 hover:border-accent/30 transition-all cursor-pointer">
                <input type="file" @change="onFileChange" class="absolute inset-0 opacity-0 z-10 cursor-pointer" accept="image/*" />
                
                <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                <div v-else class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 gap-2">
                  <i class="las la-image text-4xl"></i>
                  <span class="text-[9px] font-bold uppercase tracking-widest">Cliquez pour ajouter une image</span>
                </div>
                
                <div v-if="imagePreview" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <span class="text-white text-[10px] font-bold uppercase tracking-widest">Changer l'image</span>
                </div>
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Nom du service</label>
              <input v-model="form.name" type="text" placeholder="Ex: Room Service 24/7"
                     class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-primary focus:ring-2 focus:ring-primary/5 outline-none" />
            </div>

            <div class="space-y-2">
              <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Description</label>
              <textarea v-model="form.description" rows="3" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-medium text-slate-600 focus:ring-2 focus:ring-primary/5 outline-none resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-6">
               <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Prix (MAD)</label>
                <input v-model.number="form.price" type="number" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-primary focus:ring-2 focus:ring-primary/5 outline-none" />
              </div>
              <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Catégorie</label>
                
                <div v-if="!isNewCategory" class="relative">
                  <select v-model="form.category" @change="handleCategoryChange" 
                          class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-primary focus:ring-2 focus:ring-primary/5 outline-none cursor-pointer appearance-none">
                    <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                    <option value="new" class="text-accent font-bold">+ Nouvelle catégorie...</option>
                  </select>
                  <i class="las la-angle-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>

                <div v-else class="relative animate-fade-in">
                  <input v-model="form.category" type="text" placeholder="Nom de la nouvelle catégorie..."
                         class="w-full pl-6 pr-14 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-primary focus:ring-2 focus:ring-primary/5 outline-none" />
                  <button @click="isNewCategory = false; form.category = categories[0]" 
                          class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-rose-500 transition-colors">
                    <i class="las la-times text-xl"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-2">Type de Tarification</label>
              <select v-model="form.price_type" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-primary focus:ring-2 focus:ring-primary/5 outline-none cursor-pointer appearance-none">
                <option value="one_time">Paiement unique</option>
                <option value="per_person">Par personne</option>
                <option value="per_night">Par nuit</option>
                <option value="per_stay">Par séjour</option>
              </select>
            </div>

            <label class="flex items-center gap-4 cursor-pointer p-4 rounded-2xl bg-slate-50">
               <input type="checkbox" v-model="form.is_active" class="hidden" />
               <div class="w-6 h-6 rounded-lg border-2 border-slate-200 flex items-center justify-center transition-all" :class="{'bg-primary border-primary': form.is_active}">
                 <i v-if="form.is_active" class="las la-check text-white text-sm"></i>
               </div>
               <span class="text-xs font-bold text-primary uppercase tracking-widest">Activer le service</span>
            </label>

            <button @click="saveService" :disabled="saving || !form.name"
                    class="w-full py-5 bg-primary text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-accent transition-all shadow-xl disabled:opacity-50">
              {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
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

const router = useRouter()
const authStore = useAuthStore()

const services = ref([])
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const editingService = ref(null)
const activeFilter = ref('all')

const categories = computed(() => {
  const dbCats = services.value.map(s => s.category).filter(Boolean)
  return [...new Set([...dbCats, 'Gastronomie', 'Bien-être', 'Transport', 'Service Chambre'])]
})

const isNewCategory = ref(false)

const filteredServices = computed(() => 
  activeFilter.value === 'all' ? services.value : services.value.filter(s => s.category === activeFilter.value)
)

const loadServices = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/services')
    services.value = data.data ?? data
  } catch {
    const { data } = await api.get('/services')
    services.value = data
  } finally { loading.value = false }
}

const handleCategoryChange = (e) => {
  if (e.target.value === 'new') {
    isNewCategory.value = true
    form.category = ''
  }
}

const form = reactive({ name: '', description: '', category: 'Gastronomie', price: 0, price_type: 'one_time', is_active: true, image: null })
const imagePreview = ref(null)

const onFileChange = (e) => {
  const file = e.target.files[0]
  if (!file) return
  form.image = file
  imagePreview.value = URL.createObjectURL(file)
}

const openCreate = () => {
  editingService.value = null
  isNewCategory.value = false
  imagePreview.value = null
  Object.assign(form, { name: '', description: '', category: 'Gastronomie', price: 0, price_type: 'one_time', is_active: true, image: null })
  showModal.value = true
}

const editService = (s) => {
  editingService.value = s
  imagePreview.value = s.image ? `http://localhost:8000/storage/${s.image}` : null
  Object.assign(form, { 
    name: s.name, 
    description: s.description || '', 
    category: s.category || 'Gastronomie', 
    price: parseFloat(s.price), 
    price_type: s.price_type || 'one_time',
    is_active: s.is_active,
    image: null 
  })
  showModal.value = true
}

const saveService = async () => {
  saving.value = true
  try {
    const formData = new FormData()
    Object.keys(form).forEach(key => {
      if (form[key] !== null) {
        // Pour Laravel, is_active doit être envoyé comme 1 ou 0
        if (key === 'is_active') formData.append(key, form[key] ? 1 : 0)
        else formData.append(key, form[key])
      }
    })

    if (editingService.value) {
      // Laravel supporte mal PUT avec FormData, on utilise POST avec _method=PUT
      formData.append('_method', 'PUT')
      await api.post(`/admin/services/${editingService.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await api.post('/admin/services', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }
    showModal.value = false
    await loadServices()
  } catch (e) { 
    console.error('Erreur lors de la sauvegarde du service:', e)
    alert('Une erreur est survenue lors de l\'enregistrement.')
  }
  finally { saving.value = false }
}

const deleteService = async (s) => {
  if (!confirm(`Supprimer ${s.name} ?`)) return
  try {
    await api.delete(`/admin/services/${s.id}`)
    await loadServices()
  } catch (e) { console.error(e) }
}

const getServiceImage = (s) => {
  if (s?.image) {
    const timestamp = s.updated_at ? new Date(s.updated_at).getTime() : new Date().getTime()
    return `http://localhost:8000/storage/${s.image}?t=${timestamp}`
  }
  return 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=90'
}

const logout = async () => { await authStore.logout(); router.push('/login') }
onMounted(loadServices)
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
