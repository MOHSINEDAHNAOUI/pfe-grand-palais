<template>
  <div class="min-h-screen flex bg-slate-50 overflow-hidden">
    
    <!-- LEFT: IMAGE (Immersive) -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
      <img src="https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=1200&q=80" 
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
           <h2 class="text-6xl font-serif mb-8 leading-tight">Accès <br><span class="italic text-accent">sécurisé</span>.</h2>
           <p class="text-white/60 font-light leading-relaxed">Restaurez l'accès à votre compte Grand Palais pour continuer à profiter de nos services exclusifs en toute sérénité.</p>
        </div>

        <div class="flex items-center gap-8 text-[10px] font-bold tracking-widest uppercase text-white/40">
           <span>Confidentialité</span>
           <span>Service Privé</span>
        </div>
      </div>
    </div>

    <!-- RIGHT: FORM (Clean & Elegant) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12">
      <div class="w-full max-w-sm animate-fade-in text-left">
        
        <router-link to="/login" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-primary transition-colors tracking-widest uppercase mb-10 group">
          <i class="las la-arrow-left text-lg group-hover:-translate-x-1 transition-transform"></i> Retour
        </router-link>

        <div class="mb-10 text-center lg:text-left">
          <h1 class="text-3xl font-serif text-primary mb-2">Mot de passe oublié</h1>
          <p class="text-slate-500 font-light text-sm leading-relaxed">Entrez votre adresse email. Nous vous enverrons un lien sécurisé pour redéfinir votre accès.</p>
        </div>

        <div v-if="error" class="mb-8 p-4 rounded-xl bg-rose-50 border border-rose-100 flex items-center gap-3 text-rose-600 text-xs font-medium animate-shake shadow-sm">
          <i class="las la-exclamation-triangle text-xl"></i> {{ error }}
        </div>

        <div v-if="successMsg" class="mb-8 p-4 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center gap-3 text-emerald-600 text-xs font-medium shadow-sm">
          <i class="las la-check-circle text-xl"></i> {{ successMsg }}
        </div>

        <form @submit.prevent="handleForgotPassword" class="space-y-6">
          <div class="space-y-2">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Adresse Email</label>
            <div class="relative group">
              <input v-model="email" type="email" required placeholder="votre@email.com"
                     class="w-full bg-white border border-slate-100 rounded-xl px-5 py-3.5 text-primary text-sm focus:outline-none focus:border-accent focus:shadow-md transition-all shadow-sm" />
              <div class="absolute inset-y-0 right-4 flex items-center text-slate-300">
                <i class="las la-envelope text-lg"></i>
              </div>
            </div>
          </div>

          <button type="submit" :disabled="loading" 
                  class="btn-premium w-full py-4 rounded-xl font-bold tracking-[0.2em] flex items-center justify-center gap-3 transition-all active:scale-95 disabled:grayscale text-[10px] mt-4 shadow-lg shadow-primary/20">
            {{ loading ? 'ENVOI EN COURS...' : 'ENVOYER LE LIEN' }}
            <i v-if="!loading" class="las la-paper-plane text-sm ml-1"></i>
          </button>
        </form>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'

const email      = ref('')
const loading    = ref(false)
const error      = ref('')
const successMsg = ref('')
const authStore  = useAuthStore()

const handleForgotPassword = async () => {
  error.value      = ''
  successMsg.value = ''
  loading.value    = true

  try {
    const response = await authStore.forgotPassword(email.value)
    successMsg.value = response.message || "Un lien de réinitialisation vous a été envoyé si l'email existe."
  } catch (err) {
    error.value = err.response?.data?.message || 'Une erreur est survenue lors de la demande.'
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
