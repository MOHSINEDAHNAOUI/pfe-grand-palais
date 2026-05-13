<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-50 px-6">
    <div class="max-w-md w-full text-center animate-fade-in">
      
      <div class="mb-10 relative">
        <div class="w-24 h-24 bg-white rounded-full mx-auto flex items-center justify-center shadow-xl border border-slate-100 relative z-10">
           <i class="las la-heart text-rose-500 text-5xl animate-pulse"></i>
        </div>
        <div class="absolute inset-0 bg-rose-500/5 rounded-full blur-3xl scale-150"></div>
      </div>

      <h1 class="text-4xl font-serif text-primary mb-6 leading-tight">Au revoir, <br><span class="text-accent italic">Cher Résident</span>.</h1>
      
      <div class="gold-divider mx-auto mb-8"></div>

      <p class="text-slate-500 font-light leading-relaxed mb-12">
        Votre profil a été clôturé avec succès. <br>
        Nous sommes sincèrement tristes de vous voir partir, mais nous respectons votre choix. 
        Sachez que les portes du <span class="font-bold text-primary">Grand Palais</span> vous resteront toujours ouvertes si vous décidez de revenir un jour.
      </p>

      <router-link to="/">
        <button class="btn-premium px-12 py-4 rounded-xl text-[10px] shadow-xl shadow-primary/10 active:scale-95 transition-all">
          RETOUR À L'ACCUEIL
        </button>
      </router-link>

      <p class="mt-12 text-[10px] font-bold text-slate-300 uppercase tracking-widest italic">
        ✦ Excellence & Sérénité ✦
      </p>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

onMounted(() => {
  // S'assurer que le client est bien déconnecté localement
  authStore.logout()
})
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 1.2s ease-out forwards;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.gold-divider {
  width: 40px;
  height: 2px;
  background: #C9A96E;
}
</style>
