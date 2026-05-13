<template>
  <div class="min-h-screen flex items-center justify-center px-4"
       style="background: #FAF8F5;">
    <div class="bg-white max-w-md w-full p-10"
         style="border-top: 4px solid #C9A96E;">
 
      <!-- Header -->
      <div class="text-center mb-8">
        <p class="text-xs mb-3"
           style="color: #C9A96E; letter-spacing: 0.3em; text-transform: uppercase;">
          Grand Palais Hôtel
        </p>
        <h1 class="text-3xl font-light mb-3"
            style="font-family: 'Cormorant Garamond', serif; color: #1A1A1A;">
          Complétez votre profil
        </h1>
        <div class="gold-divider mb-3"></div>
        <p class="text-xs" style="color: #8B8178;">
          Bienvenue {{ authStore.user?.name }} !
          Veuillez compléter vos informations pour continuer.
        </p>
      </div>
 
      <!-- Error -->
      <div v-if="error" class="mb-5 p-3 text-xs"
           style="background: #FEF2F2; color: #DC2626; border-left: 3px solid #DC2626;">
        {{ error }}
      </div>
 
      <!-- Success -->
      <div v-if="success" class="mb-5 p-3 text-xs"
           style="background: #F0FDF4; color: #16A34A; border-left: 3px solid #16A34A;">
        ✓ Profil complété avec succès !
      </div>
 
      <!-- Form -->
      <div class="space-y-6 mb-8">
        <div>
          <label class="block text-xs mb-2"
                 style="color: #8B8178; letter-spacing: 0.1em; text-transform: uppercase;">
            Nom complet *
          </label>
          <input v-model="form.name" type="text" placeholder="Jean Dupont"
                 class="input-luxury" />
        </div>
 
        <div>
          <label class="block text-xs mb-2"
                 style="color: #8B8178; letter-spacing: 0.1em; text-transform: uppercase;">
            Téléphone
          </label>
          <input v-model="form.phone" type="tel" placeholder="+33 6 00 00 00 00"
                 class="input-luxury" />
        </div>
 
        <div>
          <label class="block text-xs mb-2"
                 style="color: #8B8178; letter-spacing: 0.1em; text-transform: uppercase;">
            Ville
          </label>
          <input v-model="form.city" type="text" placeholder="Paris"
                 class="input-luxury" />
        </div>
 
        <div>
          <label class="block text-xs mb-2"
                 style="color: #8B8178; letter-spacing: 0.1em; text-transform: uppercase;">
            CNI (Carte d'Identité)
          </label>
          <input v-model="form.cni" type="text" placeholder="AB123456"
                 class="input-luxury" />
        </div>
 
        <div>
          <label class="block text-xs mb-2"
                 style="color: #8B8178; letter-spacing: 0.1em; text-transform: uppercase;">
            Pays
          </label>
          <input v-model="form.country" type="text" placeholder="France"
                 class="input-luxury" />
        </div>
      </div>
 
      <button @click="saveProfile" :disabled="saving"
              class="btn-gold w-full mb-4">
        {{ saving ? 'Sauvegarde...' : 'Enregistrer et continuer' }}
      </button>
 
      <button @click="skip"
              class="w-full text-xs text-center transition-colors"
              style="color: #B0A898;">
        Passer cette étape →
      </button>
 
    </div>
  </div>
</template>
 
<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'
 
const router    = useRouter()
const authStore = useAuthStore()
const saving    = ref(false)
const error     = ref('')
const success   = ref(false)
 
const form = reactive({
  name:    authStore.user?.name    || '',
  phone:   authStore.user?.phone   || '',
  cni:     authStore.user?.cni     || '',
  city:    authStore.user?.city    || '',
  country: authStore.user?.country || '',
})
 
const saveProfile = async () => {
  if (!form.name.trim()) {
    error.value = 'Le nom est obligatoire'
    return
  }
 
  saving.value = true
  error.value  = ''
 
  try {
    await api.put('/auth/profile', form)
    await authStore.fetchUser()
    success.value = true
    setTimeout(() => router.push('/'), 1000)
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Erreur lors de la sauvegarde'
  } finally {
    saving.value = false
  }
}
 
const skip = () => router.push('/')
 
onMounted(() => {
  if (!authStore.isLoggedIn) router.push('/login')
})
</script>
