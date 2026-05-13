<template>
  <div class="min-h-screen flex bg-slate-50 overflow-hidden">
    
    <!-- LEFT: IMAGE (Immersive) -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
      <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80" 
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
           <p class="text-accent-light font-bold text-[10px] tracking-[0.5em] uppercase mb-6">Nouvelle Résidence</p>
           <h2 class="text-6xl font-serif mb-8 leading-tight">Créez votre <br><span class="italic text-accent">invitation</span>.</h2>
           <p class="text-white/60 font-light leading-relaxed">Rejoignez le cercle privilégié du Grand Palais et profitez d'un service de conciergerie personnalisé dès votre premier séjour.</p>
        </div>

        <div class="flex items-center gap-8 text-[10px] font-bold tracking-widest uppercase text-white/40">
           <span>✦ Avantages Privés</span>
           <span>✦ Offres Membres</span>
           <span>✦ Accueil VIP</span>
        </div>
      </div>
    </div>

    <!-- RIGHT: FORM (Clean & Elegant) -->
    <!-- RIGHT: FORM (Clean & Elegant) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12">
      <div class="w-full max-w-md animate-fade-in text-left">
        
        <div class="mb-8 text-center lg:text-left">
          <h1 class="text-3xl font-serif text-primary mb-2">Inscription</h1>
          <p class="text-slate-500 font-light text-sm">L'excellence vous attend. Remplissez vos informations.</p>
        </div>

        <div v-if="error" class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-100 flex items-center gap-3 text-rose-600 text-xs font-medium animate-shake shadow-sm">
          <i class="las la-exclamation-triangle text-xl"></i> {{ error }}
        </div>

        <form @submit.prevent="handleRegister" class="space-y-5">
          
          <!-- Identity Row -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-1.5">
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nom Complet</label>
              <div class="relative group">
                <input v-model="form.name" type="text" required placeholder="Jean Dupont"
                       class="w-full bg-white border border-slate-100 rounded-xl px-5 py-3.5 text-primary text-sm focus:outline-none focus:border-accent focus:shadow-md transition-all shadow-sm" />
                <div class="absolute inset-y-0 right-4 flex items-center text-slate-300">
                  <i class="las la-user text-lg"></i>
                </div>
              </div>
            </div>
            <div class="space-y-1.5">
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Téléphone</label>
              <div class="relative group">
                <input v-model="form.phone" type="tel" placeholder="+33 6..."
                       class="w-full bg-white border border-slate-100 rounded-xl px-5 py-3.5 text-primary text-sm focus:outline-none focus:border-accent focus:shadow-md transition-all shadow-sm" />
                <div class="absolute inset-y-0 right-4 flex items-center text-slate-300">
                  <i class="las la-phone text-lg"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Identity Row 2: Email & CNI -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-1.5">
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Adresse Email</label>
              <div class="relative group">
                <input v-model="form.email" type="email" required placeholder="votre@email.com"
                       class="w-full bg-white border border-slate-100 rounded-xl px-5 py-3.5 text-primary text-sm focus:outline-none focus:border-accent focus:shadow-md transition-all shadow-sm" />
                <div class="absolute inset-y-0 right-4 flex items-center text-slate-300">
                  <i class="las la-envelope text-lg"></i>
                </div>
              </div>
            </div>
            <div class="space-y-1.5">
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">CNI (Carte d'Identité)</label>
              <div class="relative group">
                <input v-model="form.cni" type="text" placeholder="AB123456"
                       class="w-full bg-white border border-slate-100 rounded-xl px-5 py-3.5 text-primary text-sm focus:outline-none focus:border-accent focus:shadow-md transition-all shadow-sm" />
                <div class="absolute inset-y-0 right-4 flex items-center text-slate-300">
                  <i class="las la-id-card text-lg"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Security Row -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-1.5">
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Mot de Passe</label>
              <div class="relative group">
                <input v-model="form.password" type="password" required placeholder="••••••••"
                       class="w-full bg-white border border-slate-100 rounded-xl px-5 py-3.5 text-primary text-sm focus:outline-none focus:border-accent focus:shadow-md transition-all shadow-sm" />
                <div class="absolute inset-y-0 right-4 flex items-center text-slate-300">
                  <i class="las la-lock text-lg"></i>
                </div>
              </div>
            </div>
            <div class="space-y-1.5">
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Confirmation</label>
              <div class="relative group">
                <input v-model="form.password_confirmation" type="password" required placeholder="••••••••"
                       class="w-full bg-white border border-slate-100 rounded-xl px-5 py-3.5 text-primary text-sm focus:outline-none focus:border-accent focus:shadow-md transition-all shadow-sm" />
                <div class="absolute inset-y-0 right-4 flex items-center text-slate-300">
                  <i class="las la-check-circle text-lg"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="flex items-start gap-3 ml-1 pt-2">
             <input type="checkbox" id="terms" required class="mt-1 w-3.5 h-3.5 rounded border-slate-200 text-accent focus:ring-accent cursor-pointer" />
             <label for="terms" class="text-[10px] text-slate-500 font-light leading-relaxed">
               J'accepte les <a href="#" class="text-accent hover:underline">Conditions</a> et la 
               <a href="#" class="text-accent hover:underline">Confidentialité</a>.
             </label>
          </div>

          <button type="submit" :disabled="loading" 
                  class="btn-premium w-full py-4 rounded-xl font-bold tracking-[0.2em] flex items-center justify-center gap-3 transition-all active:scale-95 disabled:grayscale text-[10px] mt-4 shadow-lg shadow-primary/20">
            {{ loading ? 'CRÉATION...' : 'ACCÉDER AU CERCLE' }}
          </button>
        </form>

        <!-- Social -->
        <div class="mt-6">
          <div class="flex items-center gap-4 mb-5">
             <div class="flex-1 h-px bg-slate-100"></div>
             <span class="text-[9px] font-bold text-slate-300 uppercase tracking-[0.2em]">Ou continuer avec</span>
             <div class="flex-1 h-px bg-slate-100"></div>
          </div>

          <div class="grid gap-4">
             <button @click="loginWithGoogle" class="w-full flex items-center justify-center gap-3 py-3.5 border border-slate-200 bg-white rounded-xl hover:bg-slate-50 hover:border-slate-300 hover:shadow-md transition-all duration-300 text-[11px] font-bold text-slate-700 uppercase tracking-widest active:scale-95 group shadow-sm">
               <img src="https://www.google.com/favicon.ico" class="w-4 h-4 grayscale group-hover:grayscale-0 transition-all duration-300" />
               Google
             </button>
          </div>
        </div>
        
        <div class="mt-6 text-center text-left">
          <p class="text-[11px] text-slate-500 font-light">
            Déjà membre de la Maison ? 
            <router-link to="/login" class="text-accent font-bold hover:underline ml-1">Connectez-vous</router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router    = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const error   = ref('')
const form    = reactive({
  name:                  '',
  email:                 '',
  phone:                 '',
  cni:                   '',
  password:              '',
  password_confirmation: '',
})

const handleRegister = async () => {
  error.value = ''
  if (form.password !== form.password_confirmation) {
    error.value = 'Les mots de passe ne correspondent pas'
    return
  }
  
  loading.value = true
  try {
    await authStore.register(form)
    router.push('/verify-email')
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Une erreur est survenue lors de l\'création de votre compte'
  } finally {
    loading.value = false
  }
}

const loginWithGoogle = () => {
    window.location.href = 'http://localhost:8000/api/auth/google/redirect'
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
