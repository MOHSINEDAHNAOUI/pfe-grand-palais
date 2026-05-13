<template>
  <div class="min-h-screen flex bg-slate-50 overflow-hidden">
    
    <!-- LEFT: IMAGE (Immersive) -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
      <img src="https://images.unsplash.com/photo-1541971875076-8f970d573be6?w=1200&q=80" 
           class="absolute inset-0 w-full h-full object-cover scale-105 animate-slow-zoom" />
      <div class="absolute inset-0 bg-primary/40 backdrop-blur-[2px]"></div>
      
      <div class="relative z-10 w-full flex flex-col justify-between p-20 text-white">
        <router-link to="/" class="flex items-center gap-3 group border-none">
           <div class="w-12 h-12 rounded-full border border-white/30 flex items-center justify-center bg-white/5 backdrop-blur-md shadow-2xl overflow-hidden group-hover:border-white transition-all group-hover:scale-105">
               <img src="@/assets/logo-premium.png" alt="Grand Palais" class="w-8 h-8 object-contain brightness-110 translate-y-[1px]" />
           </div>
           <span class="text-sm font-bold tracking-[0.4em] uppercase">Grand Palais</span>
        </router-link>

        <div class="max-w-md animate-slide-up">
           <p class="text-accent-light font-bold text-[10px] tracking-[0.5em] uppercase mb-6">Sécurité</p>
           <h2 class="text-6xl font-serif mb-8 leading-tight">Nouveau <br><span class="italic text-accent">départ</span>.</h2>
           <p class="text-white/60 font-light leading-relaxed">Choisissez un nouveau mot de passe fort pour sécuriser l'accès à votre compte Grand Palais.</p>
        </div>

        <div class="flex items-center gap-8 text-[10px] font-bold tracking-widest uppercase text-white/40">
           <span>✦ Fiabilité</span>
           <span>✦ Chiffrement Avancé</span>
        </div>
      </div>
    </div>

    <!-- RIGHT: FORM (Clean & Elegant) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 h-screen overflow-y-auto">
      <div class="w-full max-w-sm animate-fade-in text-left py-8">
        
        <div class="mb-10 text-center lg:text-left">
          <h1 class="text-3xl font-serif text-primary mb-2">Redéfinir le mot de passe</h1>
          <p class="text-slate-500 font-light text-sm leading-relaxed">Entrez votre nouveau mot de passe. Il expirera après 15 minutes d'inactivité de ce lien.</p>
        </div>

        <div v-if="error" class="mb-8 p-4 rounded-xl bg-rose-50 border border-rose-100 flex items-center gap-3 text-rose-600 text-xs font-medium animate-shake shadow-sm">
          <i class="las la-exclamation-triangle text-xl"></i> {{ error }}
        </div>

        <div v-if="successMsg" class="mb-8 p-4 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center gap-3 text-emerald-600 text-xs font-medium shadow-sm">
          <i class="las la-check-circle text-xl"></i> {{ successMsg }}
        </div>

        <form v-if="!successMsg" @submit.prevent="handleResetPassword" class="space-y-5">
          <div class="space-y-2">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Adresse Email</label>
            <div class="relative group">
              <input v-model="form.email" type="email" required disabled readonly
                     class="w-full bg-slate-50 text-slate-400 border border-slate-100 rounded-xl px-5 py-3.5 text-sm focus:outline-none shadow-sm cursor-not-allowed" />
              <div class="absolute inset-y-0 right-4 flex items-center text-slate-300">
                <i class="las la-envelope text-lg"></i>
              </div>
            </div>
          </div>

          <div class="space-y-2">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nouveau mot de passe</label>
            <div class="relative group">
              <input v-model="form.password" type="password" required placeholder="••••••••"
                     class="w-full bg-white border border-slate-100 rounded-xl px-5 py-3.5 text-primary text-sm focus:outline-none focus:border-accent focus:shadow-md transition-all shadow-sm" />
              <div class="absolute inset-y-0 right-4 flex items-center text-slate-300">
                <i class="las la-lock text-lg"></i>
              </div>
            </div>
          </div>

          <div class="space-y-2">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Confirmation</label>
            <div class="relative group">
              <input v-model="form.password_confirmation" type="password" required placeholder="••••••••"
                     class="w-full bg-white border border-slate-100 rounded-xl px-5 py-3.5 text-primary text-sm focus:outline-none focus:border-accent focus:shadow-md transition-all shadow-sm" />
              <div class="absolute inset-y-0 right-4 flex items-center text-slate-300">
                <i class="las la-check-circle text-lg"></i>
              </div>
            </div>
          </div>

          <button type="submit" :disabled="loading" 
                  class="btn-premium w-full py-4 rounded-xl font-bold tracking-[0.2em] flex items-center justify-center gap-3 transition-all active:scale-95 disabled:grayscale text-[10px] mt-4 shadow-lg shadow-primary/20">
            {{ loading ? 'MODIFICATION...' : 'RÉINITIALISER' }}
          </button>
        </form>

        <div v-else class="text-center mt-6">
          <router-link to="/login" class="btn-premium inline-flex py-4 px-8 rounded-xl font-bold tracking-[0.2em] text-[10px]">
            SE CONNECTER MAINTENANT
          </router-link>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route     = useRoute()
const authStore = useAuthStore()

const loading    = ref(false)
const error      = ref('')
const successMsg = ref('')

const form = reactive({
  email: '',
  password: '',
  password_confirmation: '',
  token: ''
})

onMounted(() => {
  // Récupérer le token et l'email depuis l'URL
  form.token = route.query.token || ''
  form.email = route.query.email || ''

  if (!form.token || !form.email) {
    error.value = "Lien de réinitialisation invalide ou corrompu."
  }
})

const handleResetPassword = async () => {
  error.value      = ''
  loading.value    = true

  if (form.password !== form.password_confirmation) {
    error.value   = "Les mots de passe ne correspondent pas."
    loading.value = false
    return
  }

  try {
    const response = await authStore.resetPassword(form)
    successMsg.value = response.message || "Votre mot de passe a été réinitialisé avec succès !"
  } catch (err) {
    error.value = err.response?.data?.message || err.response?.data?.email?.[0] || 'Ce lien a expiré ou une erreur est survenue.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.animate-slow-zoom {
  animation: slowZoom 20s linear infinite alternate;
}
@keyframes slowZoom {
  from { transform: scale(1); }
  to { transform: scale(1.1); }
}
.animate-slide-up {
  animation: slideUp 0.8s ease-out forwards;
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 1s ease-out forwards;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
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
