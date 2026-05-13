<template>
  <div class="min-h-screen flex items-center justify-center px-4 bg-slate-50">
    <div class="max-w-md w-full animate-slide-up">
      <div class="card-elegant p-10 text-center relative overflow-hidden">
        <!-- Top accent border -->
        <div class="absolute top-0 left-0 w-full h-1.5 bg-accent"></div>

        <!-- Success -->
        <template v-if="status === 'success'">
          <div class="w-20 h-20 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-8 transition-transform hover:scale-110 duration-500">
             <i class="las la-check-circle text-4xl text-emerald-500"></i>
          </div>
          
          <p class="text-[10px] font-bold tracking-[0.3em] uppercase text-accent mb-3">
            Grand Palais Hôtel
          </p>
          
          <h1 class="text-4xl font-serif text-primary mb-3">
            Email confirmé !
          </h1>
          
          <div class="gold-divider"></div>
          
          <p class="text-sm text-slate-500 font-light mb-8 leading-relaxed">
            Votre adresse email a été vérifiée avec succès. <br>
            Vous pouvez maintenant accéder à tous nos services d'exception.
          </p>
          
          <router-link to="/login">
            <button class="btn-gold-premium w-full flex items-center justify-center gap-2 group">
              <span>Se connecter</span>
              <i class="las la-sign-in-alt text-lg transition-transform group-hover:translate-x-1"></i>
            </button>
          </router-link>
        </template>

        <!-- Already verified -->
        <template v-else-if="status === 'already'">
          <div class="w-20 h-20 rounded-full bg-amber-50 flex items-center justify-center mx-auto mb-8 transition-transform hover:scale-110 duration-500">
             <i class="las la-certificate text-4xl text-amber-500"></i>
          </div>

          <p class="text-[10px] font-bold tracking-[0.3em] uppercase text-accent mb-3">
            Grand Palais Hôtel
          </p>

          <h1 class="text-4xl font-serif text-primary mb-3">
            Déjà vérifié
          </h1>

          <div class="gold-divider"></div>

          <p class="text-sm text-slate-500 font-light mb-8 leading-relaxed">
            Votre adresse email est déjà enregistrée et vérifiée dans notre système.
          </p>

          <router-link to="/">
            <button class="btn-premium w-full flex items-center justify-center gap-2 group">
              <span>Retour à l'accueil</span>
              <i class="las la-home text-lg"></i>
            </button>
          </router-link>
        </template>

        <!-- Error -->
        <template v-else>
          <div class="w-20 h-20 rounded-full bg-rose-50 flex items-center justify-center mx-auto mb-8 transition-transform hover:scale-110 duration-500">
             <i class="las la-exclamation-triangle text-4xl text-rose-500"></i>
          </div>

          <h1 class="text-3xl font-serif text-primary mb-3">
            Lien invalide
          </h1>

          <div class="gold-divider"></div>

          <p class="text-sm text-slate-500 font-light mb-8 leading-relaxed">
            Ce lien de vérification est invalide ou a expiré. Veuillez demander un nouveau lien si nécessaire.
          </p>

          <router-link to="/login">
            <button class="btn-outline-premium w-full flex items-center justify-center gap-2 group">
              <i class="las la-arrow-left text-lg"></i>
              <span>Retour à la connexion</span>
            </button>
          </router-link>
        </template>
      </div>

      <!-- Footer Help -->
      <p class="text-center mt-8 text-[10px] text-slate-400 uppercase tracking-widest">
        © 2026 Grand Palais Hôtel. Tous droits réservés.
      </p>
    </div>
  </div>
</template>
 
<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
 
const route  = useRoute()
const status = computed(() => route.query.status || 'error')
</script>
