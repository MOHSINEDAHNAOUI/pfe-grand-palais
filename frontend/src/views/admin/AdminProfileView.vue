<template>
  <AdminLayout page-title="Mon Profil" @logout="handleLogout">
    <div class="animate-fade-in">

      <!-- Page Header -->
      <div class="mb-12">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
          <div>
            <div class="flex items-center gap-4 mb-4">
              <div class="h-[1px] w-10 bg-accent"></div>
              <p class="text-[10px] font-black text-accent tracking-[0.4em] uppercase">Espace Administration</p>
            </div>
            <h1 class="text-5xl font-serif text-primary leading-none tracking-tight">Mon Profil</h1>
            <p class="text-slate-400 text-sm mt-3">{{ authStore.user?.email }}</p>
          </div>

          <!-- Tabs -->
          <div class="flex items-center p-1.5 bg-white border border-slate-100 rounded-2xl shadow-sm">
            <button @click="activeTab = 'info'"
              class="px-8 py-3 text-[10px] font-bold uppercase tracking-widest transition-all rounded-xl"
              :class="activeTab === 'info' ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-primary'">
              <i class="las la-id-card mr-2"></i>Informations
            </button>
            <button @click="activeTab = 'security'"
              class="px-8 py-3 text-[10px] font-bold uppercase tracking-widest transition-all rounded-xl"
              :class="activeTab === 'security' ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-primary'">
              <i class="las la-shield-alt mr-2"></i>Sécurité
            </button>
          </div>
        </div>
      </div>

      <!-- Body -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

        <!-- Sidebar -->
        <div class="lg:col-span-4">
          <div class="bg-white rounded-[2.5rem] p-10 border border-slate-100 shadow-sm sticky top-32 group overflow-hidden">
            <div class="absolute -top-16 -right-16 w-56 h-56 bg-slate-50 rounded-full blur-3xl group-hover:bg-accent/5 transition-all duration-1000"></div>

            <div class="relative z-10">
              <!-- Animated Avatar -->
              <div class="relative w-40 h-40 mx-auto mb-10">
                <div class="absolute inset-0 rounded-[2.5rem] border-2 border-dashed border-slate-200 animate-[spin_20s_linear_infinite]"></div>
                <div class="p-2 w-full h-full">
                  <img :src="avatarUrl"
                    class="w-full h-full rounded-[2.2rem] object-cover border-4 border-white shadow-2xl transition-transform duration-700 group-hover:scale-105" />
                </div>
                <label class="absolute -bottom-2 -right-2 w-12 h-12 bg-white text-primary rounded-2xl flex items-center justify-center cursor-pointer shadow-xl hover:bg-primary hover:text-white transition-all hover:scale-110 active:scale-95 border border-slate-100">
                  <i class="las la-camera text-xl"></i>
                  <input type="file" class="hidden" accept="image/*" @change="uploadAvatar" />
                </label>
              </div>

              <div class="text-center mb-10">
                <h2 class="text-3xl font-serif text-primary mb-2 leading-none">{{ authStore.user?.name }}</h2>
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-accent/10 border border-accent/10">
                  <div class="w-1.5 h-1.5 rounded-full bg-accent animate-pulse"></div>
                  <p class="text-[9px] font-black text-accent tracking-[0.2em] uppercase">{{ roleLabel }}</p>
                </div>
              </div>

              <div class="space-y-5 pt-8 border-t border-slate-50">
                <div class="flex items-center gap-4 p-4 rounded-2xl hover:bg-slate-50 transition-all">
                  <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                    <i class="las la-envelope text-lg"></i>
                  </div>
                  <div class="overflow-hidden">
                    <span class="text-[9px] font-bold text-slate-300 uppercase tracking-widest block">Email</span>
                    <span class="text-xs font-bold text-primary truncate block">{{ authStore.user?.email }}</span>
                  </div>
                </div>
                <div class="flex items-center gap-4 p-4 rounded-2xl hover:bg-slate-50 transition-all">
                  <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                    <i class="las la-user-tag text-lg"></i>
                  </div>
                  <div>
                    <span class="text-[9px] font-bold text-slate-300 uppercase tracking-widest block">Rôle</span>
                    <span class="text-xs font-bold text-primary">{{ roleLabel }}</span>
                  </div>
                </div>
                <div class="flex items-center gap-4 p-4 rounded-2xl hover:bg-slate-50 transition-all">
                  <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500">
                    <i class="las la-circle text-sm"></i>
                  </div>
                  <div>
                    <span class="text-[9px] font-bold text-slate-300 uppercase tracking-widest block">Statut</span>
                    <span class="text-xs font-bold text-emerald-600">En ligne</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-8">

          <!-- Informations Tab -->
          <Transition name="tab-fade" mode="out-in">
            <div v-if="activeTab === 'info'" key="info">
              <div class="bg-white rounded-[2.5rem] p-10 md:p-12 border border-slate-100 shadow-sm">
                <div class="flex items-center gap-4 mb-10">
                  <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-accent text-2xl border border-slate-100">
                    <i class="las la-id-card"></i>
                  </div>
                  <div>
                    <h3 class="text-2xl font-serif text-primary">Informations du Compte</h3>
                    <p class="text-xs text-slate-400 font-light italic">Détails officiels de votre accès administrateur.</p>
                  </div>
                </div>

                <form @submit.prevent="updateProfile" class="space-y-8">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">Nom complet</label>
                      <div class="relative group">
                        <input v-model="form.name" type="text"
                          class="w-full bg-slate-50 border border-slate-100 rounded-[1.25rem] px-8 py-5 text-primary text-sm focus:outline-none focus:border-primary focus:bg-white transition-all shadow-sm group-hover:shadow-md pr-14" />
                        <div class="absolute inset-y-0 right-6 flex items-center text-slate-300 group-hover:text-primary transition-colors">
                          <i class="las la-pen text-sm"></i>
                        </div>
                      </div>
                    </div>
                    <div class="space-y-3">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">Adresse Email</label>
                      <div class="relative group">
                        <input v-model="form.email" type="email"
                          class="w-full bg-slate-50 border border-slate-100 rounded-[1.25rem] px-8 py-5 text-primary text-sm focus:outline-none focus:border-primary focus:bg-white transition-all shadow-sm group-hover:shadow-md pr-14" />
                        <div class="absolute inset-y-0 right-6 flex items-center text-slate-300 group-hover:text-primary transition-colors">
                          <i class="las la-pen text-sm"></i>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="flex justify-end">
                    <button type="submit" :disabled="saving"
                      class="px-12 py-5 bg-primary text-white rounded-2xl font-bold uppercase tracking-[0.2em] text-[10px] hover:bg-accent hover:shadow-xl hover:shadow-accent/20 transition-all flex items-center gap-3 disabled:opacity-50 active:scale-95">
                      <span v-if="saving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                      {{ saving ? 'Enregistrement...' : 'Enregistrer les modifications' }}
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Security Tab -->
            <div v-else-if="activeTab === 'security'" key="security">
              <div class="bg-white rounded-[2.5rem] p-10 md:p-12 border border-slate-100 shadow-sm">
                <div class="flex items-center gap-4 mb-10">
                  <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-primary text-2xl border border-slate-100">
                    <i class="las la-shield-alt"></i>
                  </div>
                  <div>
                    <h3 class="text-2xl font-serif text-primary">Sécurité du Compte</h3>
                    <p class="text-xs text-slate-400 font-light italic">Gérez vos accès et la protection de vos données.</p>
                  </div>
                </div>

                <form @submit.prevent="changePassword" class="space-y-8 max-w-lg">
                  <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">Mot de passe actuel</label>
                    <div class="relative">
                      <input v-model="passwordForm.current" :type="showPwd.current ? 'text' : 'password'" placeholder="••••••••"
                        class="w-full bg-slate-50 border border-slate-100 rounded-[1.25rem] px-8 py-5 text-primary text-sm focus:outline-none focus:border-primary focus:bg-white transition-all shadow-sm pr-14" />
                      <button type="button" @click="showPwd.current = !showPwd.current" class="absolute inset-y-0 right-6 flex items-center text-slate-300 hover:text-primary transition-colors">
                        <i :class="showPwd.current ? 'las la-eye-slash' : 'las la-eye'"></i>
                      </button>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">Nouveau mot de passe</label>
                      <div class="relative">
                        <input v-model="passwordForm.new" :type="showPwd.new ? 'text' : 'password'" placeholder="Minimum 8 caractères"
                          class="w-full bg-slate-50 border border-slate-100 rounded-[1.25rem] px-8 py-5 text-primary text-sm focus:outline-none focus:border-primary focus:bg-white transition-all shadow-sm pr-14" />
                        <button type="button" @click="showPwd.new = !showPwd.new" class="absolute inset-y-0 right-6 flex items-center text-slate-300 hover:text-primary transition-colors">
                          <i :class="showPwd.new ? 'las la-eye-slash' : 'las la-eye'"></i>
                        </button>
                      </div>
                    </div>
                    <div class="space-y-3">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">Confirmation</label>
                      <div class="relative">
                        <input v-model="passwordForm.confirm" :type="showPwd.confirm ? 'text' : 'password'" placeholder="••••••••"
                          class="w-full bg-slate-50 border border-slate-100 rounded-[1.25rem] px-8 py-5 text-primary text-sm focus:outline-none focus:border-primary focus:bg-white transition-all shadow-sm pr-14" />
                        <button type="button" @click="showPwd.confirm = !showPwd.confirm" class="absolute inset-y-0 right-6 flex items-center text-slate-300 hover:text-primary transition-colors">
                          <i :class="showPwd.confirm ? 'las la-eye-slash' : 'las la-eye'"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  <button type="submit" :disabled="saving"
                    class="px-12 py-5 bg-primary text-white rounded-2xl font-bold uppercase tracking-[0.2em] text-[10px] hover:bg-accent hover:shadow-xl transition-all flex items-center gap-3 disabled:opacity-50 active:scale-95">
                    <span v-if="saving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    {{ saving ? 'Mise à jour...' : 'Mettre à jour la sécurité' }}
                  </button>
                </form>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <Transition name="toast">
      <div v-if="toast.show" class="fixed bottom-10 right-10 z-50 flex items-center gap-4 px-8 py-5 rounded-2xl shadow-2xl text-white text-sm font-medium"
        :class="toast.type === 'success' ? 'bg-emerald-600' : 'bg-rose-600'">
        <i :class="toast.type === 'success' ? 'las la-check-circle text-2xl' : 'las la-exclamation-circle text-2xl'"></i>
        {{ toast.message }}
      </div>
    </Transition>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import api from '@/api/axios'
import AdminLayout from '@/components/admin/AdminLayout.vue'

const authStore = useAuthStore()
const router = useRouter()
const activeTab = ref('info')
const saving = ref(false)

const form = reactive({
  name: authStore.user?.name || '',
  email: authStore.user?.email || '',
})

const passwordForm = reactive({ current: '', new: '', confirm: '' })
const showPwd = reactive({ current: false, new: false, confirm: false })

const toast = reactive({ show: false, message: '', type: 'success' })

const showToast = (message, type = 'success') => {
  toast.message = message
  toast.type = type
  toast.show = true
  setTimeout(() => toast.show = false, 4000)
}

const roleLabel = computed(() => ({
  admin:        'Directeur Général',
  manager:      'Manager d\'Hôtel',
  receptionist: 'Chef de Réception',
}[authStore.user?.roles?.[0]?.name] ?? 'Administration'))

const avatarUrl = computed(() => {
  if (authStore.user?.avatar) {
    if (authStore.user.avatar.startsWith('http')) return authStore.user.avatar
    return `http://localhost:8000/storage/${authStore.user.avatar}`
  }
  const name = encodeURIComponent(authStore.user?.name || 'A')
  return `https://ui-avatars.com/api/?name=${name}&background=b45309&color=fff&size=200&bold=true`
})

const updateProfile = async () => {
  saving.value = true
  try {
    await api.put('/auth/profile', { name: form.name, email: form.email })
    authStore.user.name = form.name
    authStore.user.email = form.email
    showToast('Profil mis à jour avec succès.')
  } catch (e) {
    showToast(e.response?.data?.message || 'Erreur lors de la mise à jour.', 'error')
  } finally {
    saving.value = false
  }
}

const changePassword = async () => {
  if (passwordForm.new !== passwordForm.confirm) {
    showToast('Les mots de passe ne correspondent pas.', 'error')
    return
  }
  saving.value = true
  try {
    await api.put('/auth/password', {
      current_password: passwordForm.current,
      password: passwordForm.new,
      password_confirmation: passwordForm.confirm,
    })
    Object.assign(passwordForm, { current: '', new: '', confirm: '' })
    showToast('Mot de passe mis à jour avec succès.')
  } catch (e) {
    showToast(e.response?.data?.message || 'Erreur de mise à jour.', 'error')
  } finally {
    saving.value = false
  }
}

const uploadAvatar = async (e) => {
  const file = e.target.files[0]
  if (!file) return
  const formData = new FormData()
  formData.append('avatar', file)
  try {
    const { data } = await api.post('/auth/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    authStore.user.avatar = data.avatar || data.user?.avatar
    showToast('Photo de profil mise à jour.')
  } catch (e) {
    showToast('Échec du téléchargement de la photo.', 'error')
  }
}

const handleLogout = async () => {
  await api.post('/auth/logout').catch(() => {})
  authStore.logout()
  router.push({ name: 'login' })
}
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.6s ease-out forwards;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

.tab-fade-enter-active, .tab-fade-leave-active { transition: all 0.3s ease; }
.tab-fade-enter-from { opacity: 0; transform: translateX(10px); }
.tab-fade-leave-to   { opacity: 0; transform: translateX(-10px); }

.toast-enter-active, .toast-leave-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(20px); }
</style>
