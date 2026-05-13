<template>
  <div class="min-h-screen flex items-center justify-center"
       style="background: #FAF8F5;">
    <div class="text-center">
      <div class="w-12 h-12 rounded-full animate-spin mx-auto mb-4"
           style="border: 2px solid #C9A96E; border-top-color: transparent;"></div>
      <p class="text-xs mb-2"
         style="color: #C9A96E; letter-spacing: 0.2em; text-transform: uppercase;">
        Grand Palais
      </p>
      <p class="text-sm" style="color: #8B8178;">Connexion en cours...</p>
    </div>
  </div>
</template>
 
<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
 
const router    = useRouter()
const authStore = useAuthStore()
 
onMounted(async () => {
  const params = new URLSearchParams(window.location.search)
  const token  = params.get('token')
  const error  = params.get('error')

  if (error || !token) {
    router.push('/login?error=social_auth_failed')
    return
  }

  authStore.token = token
  localStorage.setItem('token', token)
  await authStore.fetchUser()

  // Rediriger vers l'accueil directement
  router.push('/?welcome=google')
})
</script>
