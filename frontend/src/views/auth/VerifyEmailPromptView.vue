<template>
  <div class="min-h-screen flex items-center justify-center px-4 bg-slate-50">
    <div class="max-w-md w-full animate-slide-up">
      <div class="card-elegant p-10 text-center relative overflow-hidden">
        <!-- Top accent border -->
        <div class="absolute top-0 left-0 w-full h-1.5 bg-accent"></div>

        <div class="w-20 h-20 rounded-full bg-accent/10 flex items-center justify-center mx-auto mb-8 transition-transform hover:scale-110 duration-500">
           <i class="las la-envelope-open-text text-4xl text-accent"></i>
        </div>

        <p class="text-[10px] font-bold tracking-[0.3em] uppercase text-accent mb-3">
          Grand Palais Hôtel
        </p>
        
        <h1 class="text-4xl font-serif text-primary mb-3">
          Vérifiez votre email
        </h1>
        
        <div class="gold-divider"></div>

        <div class="space-y-4 mb-8">
          <p class="text-sm text-slate-500 font-light">
            Un email de confirmation a été envoyé à :
          </p>
          <p class="text-lg font-semibold text-primary">
            {{ authStore.user?.email }}
          </p>
          <p class="text-xs text-slate-400 leading-relaxed max-w-[280px] mx-auto">
            Veuillez cliquer sur le lien contenu dans l'email pour activer votre compte. 
            N'oubliez pas de vérifier vos courriers indésirables.
          </p>
        </div>

        <div v-if="success" class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center gap-3 text-left">
          <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
            <i class="las la-check text-sm"></i>
          </div>
          <div>
            <p class="text-xs font-bold text-emerald-800">Succès</p>
            <p class="text-[10px] text-emerald-600">L'email a été renvoyé avec succès.</p>
          </div>
        </div>

        <div class="flex flex-col gap-4">
          <button @click="resend" :disabled="loading || cooldown > 0"
                  class="btn-gold-premium w-full flex items-center justify-center gap-2 group">
            <template v-if="cooldown > 0">
              <i class="las la-clock text-lg"></i>
              <span>Renvoyer dans {{ cooldown }}s</span>
            </template>
            <template v-else-if="loading">
              <i class="las la-spinner animate-spin text-lg"></i>
              <span>Envoi en cours...</span>
            </template>
            <template v-else>
              <i class="las la-paper-plane text-lg transition-transform group-hover:translate-x-1 group-hover:-translate-y-1"></i>
              <span>Renvoyer l'email</span>
            </template>
          </button>

          <router-link to="/" class="text-[10px] font-bold tracking-widest uppercase text-slate-400 hover:text-accent transition-colors flex items-center justify-center gap-2">
            Continuer sans vérifier <i class="las la-arrow-right"></i>
          </router-link>
        </div>
      </div>

      <!-- Footer Help -->
      <p class="text-center mt-8 text-[10px] text-slate-400 uppercase tracking-widest">
        Besoin d'aide ? <a href="#" class="text-accent hover:underline">Contactez le support</a>
      </p>
    </div>
  </div>
</template>
 
<script setup>
import { ref, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'
 
const authStore = useAuthStore()
const loading   = ref(false)
const success   = ref(false)
const cooldown  = ref(0)
let timer       = null
 
const resend = async () => {
  loading.value = true
  try {
    await api.post('/auth/email/resend')
    success.value = true
    cooldown.value = 60
    timer = setInterval(() => {
      cooldown.value--
      if (cooldown.value <= 0) clearInterval(timer)
    }, 1000)
  } finally {
    loading.value = false
  }
}
 
onUnmounted(() => clearInterval(timer))
</script>
