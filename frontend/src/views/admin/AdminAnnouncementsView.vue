<template>
  <AdminLayout page-title="Annonces & Événements" @logout="logout">
    
    <div class="max-w-4xl mx-auto animate-fade-in">
      
      <!-- Modern Header -->
      <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-8">
        <div>
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-[2px] bg-accent"></div>
            <p class="text-[10px] font-bold text-accent uppercase tracking-[0.4em]">Diffusion Globale</p>
          </div>
          <h1 class="text-5xl font-serif text-primary leading-tight">Annonces & <span class="italic text-accent">Événements</span></h1>
          <p class="text-slate-400 font-light mt-4 text-sm leading-relaxed max-w-lg">
            Communiquez avec l'ensemble de vos clients en un instant. Promouvez vos offres ou annoncez vos événements exclusifs.
          </p>
        </div>
      </div>

      <!-- Main Composer -->
      <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-2xl shadow-primary/5 p-12 md:p-16 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-[8rem] -z-10 group-hover:scale-110 transition-transform duration-1000"></div>
        
        <div class="space-y-10">
          
          <!-- Announcement Type -->
          <div class="space-y-4">
             <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4">Type de Diffusion</label>
             <div class="grid grid-cols-3 gap-4">
               <button v-for="type in types" :key="type.id" @click="form.type = type.id"
                       class="p-6 rounded-[1.5rem] border-2 transition-all duration-500 text-center group/type"
                       :class="form.type === type.id ? 'border-accent bg-accent text-white shadow-xl shadow-accent/20' : 'border-slate-50 bg-slate-50/50 text-slate-400 hover:border-slate-200'">
                  <i :class="[type.icon, 'text-3xl mb-3 block transition-transform group-hover/type:scale-110']"></i>
                  <span class="text-[10px] font-bold uppercase tracking-widest">{{ type.label }}</span>
               </button>
             </div>
          </div>

          <!-- Content -->
          <div class="space-y-6">
            <div class="space-y-2">
              <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4">Titre de l'Annonce</label>
              <input v-model="form.title" type="text" placeholder="Ex: Soirée de Gala - Grand Palais 2026"
                     class="w-full px-8 py-5 bg-slate-50/50 border-none rounded-[1.5rem] text-lg font-serif text-primary focus:ring-4 focus:ring-accent/5 outline-none placeholder:text-slate-300 transition-all" />
            </div>

            <div class="space-y-2">
              <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4">Message (Contenu de l'Email)</label>
              <textarea v-model="form.message" rows="6" placeholder="Rédigez votre annonce ici..."
                        class="w-full px-8 py-6 bg-slate-50/50 border-none rounded-[2rem] text-sm font-medium text-slate-600 focus:ring-4 focus:ring-accent/5 outline-none resize-none placeholder:text-slate-300 transition-all leading-relaxed"></textarea>
            </div>
          </div>

          <!-- Call to Action (Optional) -->
          <div class="p-8 bg-slate-50/50 rounded-[2.5rem] border border-slate-100 space-y-6">
            <h4 class="text-[10px] font-bold text-primary uppercase tracking-widest flex items-center gap-3">
              <span class="w-8 h-px bg-slate-200"></span> Bouton d'Action (Optionnel)
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4">Texte du Bouton</label>
                <input v-model="form.action_label" type="text" placeholder="Ex: Découvrir l'offre"
                       class="w-full px-6 py-4 bg-white border border-slate-100 rounded-2xl text-xs font-bold text-primary focus:ring-2 focus:ring-accent/10 outline-none" />
              </div>
              <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4">Lien de Destination (URL)</label>
                <input v-model="form.action_url" type="url" placeholder="https://..."
                       class="w-full px-6 py-4 bg-white border border-slate-100 rounded-2xl text-xs font-bold text-primary focus:ring-2 focus:ring-accent/10 outline-none" />
              </div>
            </div>
          </div>

          <!-- Alerts -->
          <Transition name="fade">
            <div v-if="success" class="p-6 bg-emerald-50 text-emerald-700 rounded-2xl border border-emerald-100 text-[10px] font-bold uppercase tracking-widest flex items-center gap-4 animate-slide-up">
              <i class="las la-check-circle text-2xl"></i>
              {{ success }}
            </div>
          </Transition>
          <Transition name="fade">
            <div v-if="error" class="p-6 bg-rose-50 text-rose-700 rounded-2xl border border-rose-100 text-[10px] font-bold uppercase tracking-widest flex items-center gap-4 animate-shake">
              <i class="las la-exclamation-circle text-2xl"></i>
              {{ error }}
            </div>
          </Transition>

          <!-- Submit -->
          <div class="pt-6">
            <button @click="sendAnnouncement" :disabled="sending || !form.title || !form.message"
                    class="w-full py-8 bg-primary text-white rounded-[2rem] text-xs font-bold uppercase tracking-[0.4em] hover:bg-accent transition-all duration-700 shadow-2xl shadow-primary/20 disabled:opacity-50 active:scale-95 flex items-center justify-center gap-4 group">
               <span v-if="sending" class="w-6 h-6 border-3 border-white/20 border-t-white rounded-full animate-spin"></span>
               <i v-else class="las la-paper-plane text-2xl transition-transform group-hover:translate-x-2 group-hover:-translate-y-2"></i>
               {{ sending ? 'DIFFUSION EN COURS...' : 'DIFFUSER À TOUS LES CLIENTS' }}
            </button>
            <p class="text-center text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-6 flex items-center justify-center gap-3">
              <i class="las la-info-circle text-lg"></i>
              L'annonce sera envoyée instantanément par email à tous les clients inscrits.
            </p>
          </div>

        </div>
      </div>

    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminLayout from '@/components/admin/AdminLayout.vue'
import api from '@/api/axios'

const authStore = useAuthStore()
const router = useRouter()

const sending = ref(false)
const success = ref('')
const error   = ref('')

const types = [
  { id: 'promotion', label: 'Promotion', icon: 'las la-percentage' },
  { id: 'event',     label: 'Événement', icon: 'las la-calendar-star' },
  { id: 'info',      label: 'Information', icon: 'las la-info-circle' },
]

const form = reactive({
  title: '',
  message: '',
  type: 'promotion',
  action_label: '',
  action_url: '',
})

const sendAnnouncement = async () => {
  if (!confirm('Voulez-vous vraiment envoyer cette annonce à TOUS vos clients ?')) return
  
  sending.value = true
  error.value = ''; success.value = ''
  
  try {
    const { data } = await api.post('/admin/announcements/send', form)
    success.value = data.message
    form.title = ''; form.message = ''; form.action_label = ''; form.action_url = ''
  } catch (e) {
    error.value = e.response?.data?.message ?? "Erreur lors de l'envoi de l'annonce."
  } finally {
    sending.value = false
  }
}

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-shake {
  animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
}
@keyframes shake {
  10%, 90% { transform: translate3d(-1px, 0, 0); }
  20%, 80% { transform: translate3d(2px, 0, 0); }
  30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
  40%, 60% { transform: translate3d(4px, 0, 0); }
}
</style>
