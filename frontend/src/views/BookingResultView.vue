<template>
  <div class="min-h-screen flex items-center justify-center px-6 bg-slate-50/50 relative overflow-hidden">
    
    <!-- Background Decor -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-accent/5 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-primary/5 rounded-full blur-3xl animate-pulse"></div>

    <div class="bg-white max-w-lg w-full rounded-[3rem] p-12 md:p-16 text-center shadow-xl shadow-primary/5 border border-slate-100 relative animate-slide-up">
      
      <!-- LOADING -->
      <div v-if="loading" class="flex flex-col items-center gap-8 py-12">
        <div class="w-16 h-16 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
        <div>
          <p class="text-[10px] font-bold text-accent tracking-[0.4em] uppercase mb-2">Grand Palais</p>
          <p class="text-sm text-slate-400 font-light italic">Orchestration de votre demande...</p>
        </div>
      </div>

      <!-- CONFIRMED -->
      <template v-else-if="currentStatus === 'confirmed'">
        <div class="w-24 h-24 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-10 shadow-inner group">
          <svg class="w-10 h-10 text-emerald-500 transform transition-transform group-hover:scale-110 duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        
        <p class="text-[10px] font-bold text-accent tracking-[0.4em] uppercase mb-4">Confirmation d'Exception</p>
        <h1 class="text-4xl font-serif text-primary mb-6">Séjour Immortalisé</h1>
        
        <div class="w-12 h-px bg-slate-100 mx-auto mb-6"></div>
        
        <p class="text-slate-500 font-light leading-relaxed mb-4">
          Nous avons l'honneur de vous confirmer votre réservation au Grand Palais.
        </p>
        
        <div v-if="currentRef" class="bg-slate-50 rounded-2xl py-4 px-6 mb-8 inline-block border border-slate-100">
           <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mb-1">Référence du Séjour</p>
           <p class="text-lg font-bold text-primary tracking-widest">{{ currentRef }}</p>
        </div>

        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest leading-relaxed mb-10">
           ✦ Un QR code d'accès a été transmis à votre adresse email.
        </p>

        <router-link to="/dashboard/reservations">
          <button class="btn-premium w-full rounded-2xl py-5">Gérer mes Séjours</button>
        </router-link>
      </template>

      <!-- CANCELLED -->
      <template v-else-if="currentStatus === 'cancelled'">
        <div class="w-24 h-24 rounded-full bg-rose-50 flex items-center justify-center mx-auto mb-10">
          <svg class="w-10 h-10 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        
        <p class="text-[10px] font-bold text-rose-400 tracking-[0.4em] uppercase mb-4">Avis de Résiliation</p>
        <h1 class="text-4xl font-serif text-primary mb-6">Demande Annulée</h1>
        
        <p class="text-slate-500 font-light leading-relaxed mb-10">
           Votre réservation a été révoquée comme convenu. Nous regrettons ce changement mais espérons orchestrer votre accueil très prochainement.
        </p>
        
        <router-link to="/rooms">
          <button class="btn-premium w-full rounded-2xl py-5">Reprendre l'Exploration</button>
        </router-link>
      </template>

      <!-- ALREADY CONFIRMED -->
      <template v-else-if="currentStatus === 'already'">
        <div class="w-24 h-24 rounded-full bg-blue-50 flex items-center justify-center mx-auto mb-10">
          <span class="text-4xl">✨</span>
        </div>
        <h1 class="text-4xl font-serif text-primary mb-6">Déjà Orchestrée</h1>
        <p class="text-slate-500 font-light leading-relaxed mb-10">
          Cette orchestration a déjà été finalisée. Vous pouvez retrouver tous les détails dans votre espace résident.
        </p>
        <router-link to="/dashboard/reservations">
          <button class="btn-premium w-full rounded-2xl py-5">Espace Résident</button>
        </router-link>
      </template>

      <!-- INVALID / ERROR -->
      <template v-else>
        <div class="w-24 h-24 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-10 text-slate-300">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h1 class="text-4xl font-serif text-primary mb-6">Lien Invalide</h1>
        <p class="text-slate-500 font-light leading-relaxed mb-10">
          L'invitation que vous tentez d'utiliser n'est plus active ou a expiré.
        </p>
        <router-link to="/">
          <button class="btn-premium w-full rounded-2xl py-5">Retour au Palais</button>
        </router-link>
      </template>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios'

const route         = useRoute()
const loading       = ref(false)
const currentStatus = ref(route.query.status || '')
const currentRef    = ref(route.query.ref || '')

onMounted(async () => {
  const id    = route.params.id
  const token = route.query.token

  if (id && token && !currentStatus.value) {
    loading.value = true
    try {
      const { data } = await api.get(`/reservations/${id}/confirm-arrival/${token}`)
      currentStatus.value = data.status === 'confirmed' ? 'confirmed' : 'invalid'
      currentRef.value    = data.reference ?? ''
    } catch (e) {
      const msg = e.response?.data?.message ?? ''
      if (msg.includes('already') || msg.includes('déjà')) {
        currentStatus.value = 'already'
      } else {
        currentStatus.value = 'invalid'
      }
    } finally {
      loading.value = false
    }
  }
})
</script>

<style scoped>
.animate-slide-up {
  animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
