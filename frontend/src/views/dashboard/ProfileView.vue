<template>
  <div class="min-h-screen bg-slate-50/50 py-16 px-6">
    
    <div class="max-w-6xl mx-auto">
      
      <!-- HEADER -->
      <div class="mb-16 animate-fade-in">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-10">
          <div>
            <div class="flex items-center gap-4 mb-4">
              <div class="h-[1px] w-12 bg-accent"></div>
              <p class="text-[10px] font-black text-accent tracking-[0.4em] uppercase">Espace Résident</p>
            </div>
            <h1 class="text-6xl font-serif text-primary leading-none tracking-tight">Votre Profil</h1>
          </div>
          
          <div class="flex items-center p-1.5 bg-white border border-slate-100 rounded-2xl shadow-sm">
             <button @click="activeTab = 'informations'" 
                     class="px-8 py-3 text-[10px] font-bold uppercase tracking-widest transition-all rounded-xl"
                     :class="activeTab === 'informations' ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-primary'">
               Informations
             </button>
             <button @click="activeTab = 'confidentialite'"
                     class="px-8 py-3 text-[10px] font-bold uppercase tracking-widest transition-all rounded-xl"
                     :class="activeTab === 'confidentialite' ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-primary'">
               Sécurité
             </button>
             <button @click="activeTab = 'avis'"
                     class="px-8 py-3 text-[10px] font-bold uppercase tracking-widest transition-all rounded-xl"
                     :class="activeTab === 'avis' ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-primary'">
               Mes Avis
             </button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        
        <!-- SIDEBAR: AVATAR & QUICK LOOK -->
        <div class="lg:col-span-4 space-y-8 animate-slide-up">
          <div class="bg-white rounded-[2.5rem] p-10 border border-slate-100 shadow-sm sticky top-32 group overflow-hidden">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-slate-50 rounded-full blur-3xl group-hover:bg-accent/5 transition-all duration-1000"></div>
            
            <div class="relative z-10">
              <!-- Avatar -->
              <div class="relative w-40 h-40 mx-auto mb-10">
                <div class="absolute inset-0 rounded-[2.5rem] border-2 border-dashed border-slate-200 animate-[spin_20s_linear_infinite]"></div>
                <div class="p-2 w-full h-full">
                   <img :src="avatarUrl" referrerpolicy="no-referrer"
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
                     <p class="text-[9px] font-black text-accent tracking-[0.2em] uppercase">{{ authStore.user?.roles?.[0]?.name ?? 'Résident Distinction' }}</p>
                  </div>
               </div>

              <div class="space-y-4 pt-8 border-t border-slate-50">
                 <div class="flex flex-col gap-1">
                    <span class="text-[9px] font-bold text-slate-300 uppercase tracking-widest">Membre depuis</span>
                    <span class="text-xs font-bold text-primary">{{ formatDateLong(authStore.user?.created_at) }}</span>
                 </div>
                 <div class="flex flex-col gap-1">
                     <span class="text-[9px] font-bold text-slate-300 uppercase tracking-widest">Total Séjours</span>
                     <span class="text-xs font-bold text-primary">{{ myReviews.length + pendingReviews.length }} Séjours Officiels</span>
                  </div>
              </div>

              <div class="mt-10">
                 <router-link to="/dashboard/reservations" class="block">
                    <button class="w-full py-4 bg-slate-50 text-[10px] font-bold text-primary tracking-widest uppercase rounded-2xl hover:bg-slate-100 transition-all">Consulter l'Historique</button>
                 </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- MAIN CONTENT: FORMS -->
        <div class="lg:col-span-8 space-y-10 animate-slide-up" style="animation-delay: 0.1s">
          
          <!-- INFORMATIONS TAB -->
          <div v-if="activeTab === 'informations'" class="space-y-10">
            <!-- Personal Information Card -->
            <div class="bg-white rounded-[2.5rem] p-10 md:p-12 border border-slate-100 shadow-sm">
              <div class="flex items-center gap-4 mb-10">
                 <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-accent text-2xl border border-slate-100">
                    <i class="las la-id-card"></i>
                 </div>
                 <div>
                    <h3 class="text-2xl font-serif text-primary">Informations Personnelles</h3>
                    <p class="text-xs text-slate-400 font-light italic">Détails officiels de votre résidence au Grand Palais.</p>
                 </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                 <div v-for="field in profileFields" :key="field.key" 
                      class="space-y-3"
                      :class="field.fullWidth ? 'md:col-span-2' : ''">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">{{ field.label }}</label>
                    <div class="relative group">
                       <input v-model="form[field.key]" :type="field.type || 'text'"
                              class="w-full bg-slate-50 border border-slate-100 rounded-[1.25rem] px-8 py-5 text-primary text-sm focus:outline-none focus:border-primary focus:bg-white transition-all shadow-sm group-hover:shadow-md"
                              :placeholder="field.placeholder" />
                       <div class="absolute inset-y-0 right-6 flex items-center text-slate-300 group-hover:text-primary transition-colors">
                          <i class="las la-pen text-sm"></i>
                       </div>
                    </div>
                 </div>
              </div>

              <div class="flex justify-end mt-12">
                 <button @click="updateProfile" :disabled="saving"
                         class="btn-premium px-12 py-5 rounded-2xl disabled:opacity-50 active:scale-95 transition-all shadow-xl shadow-primary/10">
                    {{ saving ? 'Mise à jour...' : 'Enregistrer les Modifications' }}
                 </button>
              </div>
            </div>

            <!-- Security Card -->
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

              <div v-if="authStore.user?.provider" class="p-6 bg-blue-50/50 rounded-2xl border border-blue-50 text-blue-700 text-xs italic flex items-center gap-4">
                 <i class="lab la-google text-2xl"></i>
                 <span>Authentification gérée via votre compte partenaire.</span>
              </div>

              <div v-else class="space-y-8 max-w-lg">
                 <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Clé d'accès actuelle</label>
                    <input v-model="passwordForm.current" type="password" placeholder="••••••••"
                           class="w-full bg-slate-50/50 border border-slate-100 rounded-2xl px-6 py-4 text-primary text-sm focus:outline-none focus:border-accent focus:bg-white transition-all shadow-sm" />
                 </div>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                       <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nouvelle clé</label>
                       <input v-model="passwordForm.new" type="password" placeholder="••••••••"
                              class="w-full bg-slate-50/50 border border-slate-100 rounded-2xl px-6 py-4 text-primary text-sm focus:outline-none focus:border-accent focus:bg-white transition-all shadow-sm" />
                    </div>
                    <div class="space-y-2">
                       <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Confirmation</label>
                       <input v-model="passwordForm.confirm" type="password" placeholder="••••••••"
                              class="w-full bg-slate-50/50 border border-slate-100 rounded-2xl px-6 py-4 text-primary text-sm focus:outline-none focus:border-accent focus:bg-white transition-all shadow-sm" />
                    </div>
                 </div>
                 <button @click="changePassword"
                         class="px-10 py-4 bg-primary text-white text-[10px] font-bold tracking-widest uppercase rounded-xl hover:bg-accent transition-all active:scale-95 shadow-lg shadow-primary/20">
                    Mettre à jour la sécurité
                 </button>
              </div>
            </div>

            <!-- Account Closure -->
            <div class="p-10 border border-rose-100 bg-rose-50/30 rounded-[2.5rem] flex flex-col md:flex-row items-center justify-between gap-8 group">
               <div class="max-w-md">
                  <h4 class="text-xl font-serif text-rose-950 mb-2">Clôture du Profil Résident</h4>
                  <p class="text-xs text-rose-800 font-light leading-relaxed italic">
                     La suppression de votre accès est irréversible. Vos points et avantages seront définitivement révoqués.
                  </p>
               </div>
               <button @click="requestDeletion" :disabled="requestingDeletion"
                       class="px-10 py-4 border border-rose-200 text-rose-600 text-[10px] font-bold tracking-widest uppercase rounded-2xl hover:bg-rose-600 hover:text-white transition-all active:scale-95 disabled:opacity-50">
                  {{ requestingDeletion ? 'Demande en cours...' : 'Désactiver le Compte' }}
               </button>
            </div>
          </div>

          <!-- CONFIDENTIALITE TAB -->
          <div v-if="activeTab === 'confidentialite'" class="space-y-10">
            <div class="bg-white rounded-[2.5rem] p-10 md:p-12 border border-slate-100 shadow-sm">
              <div class="flex items-center gap-4 mb-10">
                 <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-accent text-2xl border border-slate-100">
                    <i class="las la-gavel"></i>
                 </div>
                 <div>
                    <h3 class="text-2xl font-serif text-primary">Mention Légale</h3>
                    <p class="text-xs text-slate-400 font-light italic">Informations juridiques et conditions d'utilisation du Grand Palais.</p>
                 </div>
              </div>

              <div class="prose prose-slate prose-xs max-w-none bg-slate-50/50 p-8 rounded-[2rem] border border-slate-100 max-h-96 overflow-y-auto custom-scrollbar">
                <h4 class="text-primary font-serif mb-4">ÉDITEUR DU SITE</h4>
                <p class="text-slate-600 leading-relaxed mb-6">
                  Le présent site est édité par la société <strong>Grand Palais Luxury Hotels & Resorts</strong>, 
                  Société à Responsabilité Limitée au capital de 10 000 000 MAD, immatriculée au Registre du Commerce de Marrakech sous le numéro 123 456.<br>
                  Siège social : Avenue Mohammed VI, Hivernage, Marrakech, Maroc.<br>
                  Directeur de la publication : Le Concierge du Palais.
                </p>

                <h4 class="text-primary font-serif mb-4">HÉBERGEMENT</h4>
                <p class="text-slate-600 leading-relaxed mb-6">
                  Le site est hébergé par <strong>Palais Cloud Services</strong>, 
                  technologie de pointe assurant une confidentialité absolue et une haute disponibilité.
                </p>

                <h4 class="text-primary font-serif mb-4">PROTECTION DES DONNÉES (RGPD)</h4>
                <p class="text-slate-600 leading-relaxed mb-6">
                  Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez d'un droit d'accès, 
                  de rectification, de suppression et d'opposition aux données personnelles vous concernant. 
                  Vos données sont collectées exclusivement pour la gestion de vos réservations et l'amélioration de votre expérience au sein de notre établissement.
                </p>

                <h4 class="text-primary font-serif mb-4">PROPRIÉTÉ INTELLECTUELLE</h4>
                <p class="text-slate-600 leading-relaxed">
                  L'intégralité du site (textes, images, logos, design) est la propriété exclusive du Grand Palais. 
                  Toute reproduction ou représentation, en tout ou partie, sans l'accord préalable de l'éditeur est interdite.
                </p>
              </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-10 md:p-12 border border-slate-100 shadow-sm">
              <div class="flex items-center gap-4 mb-10">
                 <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-primary text-2xl border border-slate-100">
                    <i class="las la-user-secret"></i>
                 </div>
                 <div>
                    <h3 class="text-2xl font-serif text-primary">Confidentialité & Données</h3>
                    <p class="text-xs text-slate-400 font-light italic">Contrôlez la visibilité de vos données et votre anonymat.</p>
                 </div>
              </div>
              
              <div class="flex items-center justify-between p-6 bg-slate-50/50 rounded-2xl border border-slate-100">
                <div>
                  <span class="text-xs font-bold text-primary uppercase tracking-widest">Partage de Profil</span>
                  <p class="text-[10px] text-slate-400 italic">Autoriser nos partenaires à consulter vos préférences de séjour.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" checked class="sr-only peer">
                  <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
              </div>
            </div>
          </div>

          <!-- AVIS TAB -->
          <div v-if="activeTab === 'avis'" class="space-y-10">
            <!-- General Review Form (Always visible) -->
            <div class="bg-white rounded-[2.5rem] p-10 md:p-12 border border-slate-100 shadow-sm overflow-hidden relative group">
               <div class="absolute -top-12 -right-12 w-48 h-48 bg-accent/5 rounded-full blur-3xl group-hover:bg-accent/10 transition-all duration-1000"></div>
               
               <div class="relative z-10">
                  <div class="flex items-center gap-4 mb-8">
                     <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-accent text-2xl border border-slate-100">
                        <i class="las la-pen-nib"></i>
                     </div>
                     <div>
                        <h3 class="text-2xl font-serif text-primary">Exprimer votre Avis</h3>
                        <p class="text-xs text-slate-400 font-light italic">Partagez votre ressenti général sur le Grand Palais.</p>
                     </div>
                  </div>
 
                  <div class="space-y-6">
                     <div class="flex items-center gap-2 mb-4">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mr-4">Votre Note :</span>
                        <button v-for="star in 5" :key="star" 
                                @click="generalReview.rating = star"
                                class="text-3xl transition-all hover:scale-110"
                                :class="star <= generalReview.rating ? 'text-accent' : 'text-slate-200'">
                           <i class="las la-star"></i>
                        </button>
                     </div>
 
                     <textarea v-model="generalReview.comment" 
                               placeholder="Comment s'est passée votre visite ? Vos suggestions sont les bienvenues..."
                               rows="4"
                               class="w-full bg-slate-50/50 border border-slate-100 rounded-3xl px-8 py-6 text-primary text-sm focus:outline-none focus:border-accent focus:bg-white transition-all shadow-inner"></textarea>
                     
                     <div class="flex justify-end">
                        <button @click="submitGeneralReview" :disabled="submittingGeneral"
                                class="btn-premium px-12 py-4 rounded-2xl !text-[11px] shadow-xl shadow-primary/10 active:scale-95 transition-all disabled:opacity-50">
                           {{ submittingGeneral ? 'Publication...' : 'Publier mon Témoignage' }}
                        </button>
                     </div>
                  </div>
               </div>
            </div>
 
            <!-- Pending Reviews -->
            <div v-if="pendingReviews.length > 0" class="bg-white rounded-[2.5rem] p-10 md:p-12 border border-slate-100 shadow-sm">
               <div class="flex items-center gap-4 mb-10">
                  <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-accent text-2xl border border-slate-100">
                     <i class="las la-star"></i>
                  </div>
                  <div>
                     <h3 class="text-2xl font-serif text-primary">Séjours à Noter</h3>
                     <p class="text-xs text-slate-400 font-light italic">Partagez votre expérience sur vos derniers séjours.</p>
                  </div>
               </div>

               <div class="space-y-6">
                  <div v-for="res in pendingReviews" :key="res.id" class="p-8 rounded-3xl bg-slate-50/50 border border-slate-100">
                     <div class="flex flex-col md:flex-row justify-between gap-6 mb-8">
                        <div>
                           <p class="text-[10px] font-bold text-accent tracking-widest uppercase mb-2">Réservation #{{ res.reference }}</p>
                           <h4 class="text-xl font-serif text-primary">Chambre {{ res.room?.number }} — {{ res.room?.room_type?.name }}</h4>
                           <p class="text-xs text-slate-400 mt-1">{{ formatDateLong(res.check_in) }} au {{ formatDateLong(res.check_out) }}</p>
                        </div>
                        <div class="flex items-center gap-1">
                           <button v-for="star in 5" :key="star" 
                                   @click="reviewForms[res.id].rating = star"
                                   class="text-2xl transition-all"
                                   :class="star <= reviewForms[res.id].rating ? 'text-accent' : 'text-slate-200'">
                              <i class="las la-star"></i>
                           </button>
                        </div>
                     </div>

                     <div class="space-y-4">
                        <textarea v-model="reviewForms[res.id].comment" 
                                  placeholder="Votre message (confort, propreté, service...)"
                                  rows="3"
                                  class="w-full bg-white border border-slate-100 rounded-2xl px-6 py-4 text-primary text-sm focus:outline-none focus:border-accent transition-all"></textarea>
                        
                        <div class="flex justify-end">
                           <button @click="submitReview(res.id)" :disabled="submittingReview === res.id"
                                   class="btn-premium px-8 py-3 !rounded-xl !text-[10px]">
                              {{ submittingReview === res.id ? 'Envoi...' : 'Publier mon avis' }}
                           </button>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Past Reviews -->
            <div class="bg-white rounded-[2.5rem] p-10 md:p-12 border border-slate-100 shadow-sm">
               <div class="flex items-center gap-4 mb-10">
                  <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-primary text-2xl border border-slate-100">
                     <i class="las la-comment-alt"></i>
                  </div>
                  <div>
                     <h3 class="text-2xl font-serif text-primary">Mes Avis Publiés</h3>
                     <p class="text-xs text-slate-400 font-light italic">Historique de vos partages d'expérience.</p>
                  </div>
               </div>

               <div v-if="loadingReviews" class="flex justify-center py-10">
                  <div class="w-8 h-8 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
               </div>

               <div v-else-if="myReviews.length > 0" class="space-y-8">
                  <div v-for="rev in myReviews" :key="rev.id" class="border-b border-slate-50 pb-8 last:border-0 last:pb-0">
                     <div class="flex justify-between items-start mb-4">
                        <div>
                           <div class="flex items-center gap-2 mb-2">
                              <span v-for="star in 5" :key="star" 
                                    class="text-xs"
                                    :class="star <= rev.rating ? 'text-accent' : 'text-slate-200'">
                                 <i class="las la-star"></i>
                              </span>
                              <span class="text-[9px] font-bold text-slate-300 uppercase tracking-widest ml-2">{{ formatDateLong(rev.created_at) }}</span>
                           </div>
                           <h5 class="text-sm font-bold text-primary">{{ rev.room?.room_type?.name }} — Chambre {{ rev.room?.number }}</h5>
                        </div>
                        <span v-if="!rev.is_approved" class="px-3 py-1 bg-amber-50 text-amber-600 text-[8px] font-bold uppercase rounded-full">En attente de modération</span>
                     </div>
                     <p class="text-xs text-slate-500 italic leading-relaxed">"{{ rev.comment }}"</p>
                  </div>
               </div>

               <div v-else class="text-center py-20 bg-slate-50/50 rounded-3xl border border-dashed border-slate-200">
                  <p class="text-xs text-slate-400">Vous n'avez pas encore publié d'avis.</p>
               </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Floating Global Feedback -->
    <div v-if="success || error" class="fixed bottom-12 right-12 z-50 animate-slide-up-fast">
       <div v-if="success" class="flex items-center gap-4 bg-emerald-600 text-white px-8 py-4 rounded-2xl shadow-2xl">
          <i class="las la-check-circle text-2xl"></i>
          <span class="text-sm font-medium">{{ success }}</span>
       </div>
       <div v-if="error" class="flex items-center gap-4 bg-rose-600 text-white px-8 py-4 rounded-2xl shadow-2xl">
          <i class="las la-exclamation-circle text-2xl"></i>
          <span class="text-sm font-medium">{{ error }}</span>
       </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'

const authStore = useAuthStore()
const saving    = ref(false)
const success   = ref('')
const error     = ref('')
const activeTab = ref('informations')
 
const pendingReviews = ref([])
const myReviews      = ref([])
const loadingReviews = ref(false)
const submittingReview = ref(null)
const submittingGeneral = ref(false)
const requestingDeletion = ref(false)
const reviewForms    = reactive({})
const generalReview  = reactive({ rating: 5, comment: '' })

const profileFields = [
  { key: 'name',    label: 'Nom Complet', placeholder: 'votre nom' },
  { key: 'email',   label: 'Adresse Email', placeholder: 'e-mail', type: 'email' },
  { key: 'phone',   label: 'Téléphone', placeholder: '+212 7 00 00 00 00', type: 'tel' },
  { key: 'cni',     label: 'CNI (Carte d\'Identité)', placeholder: 'XXXXXXXX' },
  { key: 'city',    label: 'Ville', placeholder: 'votre ville' },
  { key: 'address', label: 'Adresse Principale', placeholder: 'adresse', fullWidth: true },
  { key: 'country', label: 'Pays', placeholder: 'votre pays' },
]

const form = reactive({
  name:    authStore.user?.name    || '',
  email:   authStore.user?.email   || '',
  phone:   authStore.user?.phone   || '',
  cni:     authStore.user?.cni     || '',
  city:    authStore.user?.city    || '',
  address: authStore.user?.address || '',
  country: authStore.user?.country || '',
})

const passwordForm = reactive({ current: '', new: '', confirm: '' })

const avatarUrl = computed(() => {
  if (authStore.user?.avatar) {
    if (authStore.user.avatar.startsWith('http')) {
      return authStore.user.avatar
    }
    return `http://localhost:8000/storage/${authStore.user.avatar}`
  }
  const name = encodeURIComponent(authStore.user?.name || 'U')
  return `https://ui-avatars.com/api/?name=${name}&background=C9A96E&color=fff&size=200`
})

const formatDateLong = (d) => {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' })
}

const updateProfile = async () => {
  saving.value  = true
  success.value = ''
  error.value   = ''
  try {
    const { data } = await api.put('/auth/profile', form)
    authStore.user = data.user ?? data
    success.value  = 'Vos détails ont été enregistrés.'
    setTimeout(() => success.value = '', 5000)
  } catch (e) {
    error.value = e.response?.data?.message || 'Erreur lors de la mise à jour.'
    setTimeout(() => error.value = '', 5000)
  } finally {
    saving.value = false
  }
}

const changePassword = async () => {
  error.value   = ''
  success.value = ''
  if (passwordForm.new !== passwordForm.confirm) {
    error.value = 'Les clés ne correspondent pas.'
    return
  }
  try {
    await api.put('/auth/password', {
      current_password:      passwordForm.current,
      password:              passwordForm.new,
      password_confirmation: passwordForm.confirm,
    })
    success.value = 'Sécurité mise à jour.'
    Object.assign(passwordForm, { current: '', new: '', confirm: '' })
    setTimeout(() => success.value = '', 5000)
  } catch (e) {
    setTimeout(() => error.value = '', 5000)
  }
}

const requestDeletion = async () => {
  if (!confirm('Souhaitez-vous vraiment recevoir un email de confirmation pour clôturer votre profil ?')) return
  requestingDeletion.value = true
  success.value = ''
  error.value   = ''
  try {
    const { data } = await api.post('/auth/request-deletion')
    success.value = data.message
    setTimeout(() => success.value = '', 8000)
  } catch (e) {
    error.value = e.response?.data?.message || 'Une erreur est survenue.'
    setTimeout(() => error.value = '', 5000)
  } finally {
    requestingDeletion.value = false
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
    authStore.user = data.user ?? data
    success.value  = 'Portrait mis à jour.'
    setTimeout(() => success.value = '', 5000)
  } catch (e) {
    error.value = 'Échec du transfert.'
    setTimeout(() => error.value = '', 5000)
  }
}
 
const fetchReviewData = async () => {
  loadingReviews.value = true
  try {
    const [pendingRes, userRev] = await Promise.all([
      api.get('/auth/pending-reviews'),
      api.get('/auth/reviews')
    ])
    pendingReviews.value = pendingRes.data
    myReviews.value      = userRev.data
    
    // Initialize forms
    pendingReviews.value.forEach(res => {
      if (!reviewForms[res.id]) {
        reviewForms[res.id] = { rating: 5, comment: '' }
      }
    })
  } catch (e) {
    console.error('Error fetching reviews:', e)
  } finally {
    loadingReviews.value = false
  }
}
 
const submitReview = async (resId) => {
  submittingReview.value = resId
  try {
    await api.post(`/reservations/${resId}/review`, reviewForms[resId])
    success.value = 'Votre avis a été publié avec succès.'
    await fetchReviewData()
    setTimeout(() => success.value = '', 5000)
  } catch (e) {
    error.value = e.response?.data?.message || 'Erreur lors de l\'envoi.'
    setTimeout(() => error.value = '', 5000)
  } finally {
    submittingReview.value = null
  }
}

const submitGeneralReview = async () => {
  if (!generalReview.comment.trim()) {
    error.value = 'Veuillez écrire un commentaire.'
    return
  }
  submittingGeneral.value = true
  try {
    await api.post('/reviews', generalReview)
    success.value = 'Votre témoignage a été publié.'
    generalReview.comment = ''
    generalReview.rating = 5
    await fetchReviewData()
    setTimeout(() => success.value = '', 5000)
  } catch (e) {
    error.value = e.response?.data?.message || 'Erreur lors de l\'envoi.'
    setTimeout(() => error.value = '', 5000)
  } finally {
    submittingGeneral.value = false
  }
}
 
watch(activeTab, (val) => {
  if (val === 'avis') fetchReviewData()
})
 
onMounted(() => {
  if (activeTab.value === 'avis') fetchReviewData()
})
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.8s ease-out forwards;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-slide-up {
  animation: slideUp 0.8s ease-out forwards;
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slide-up-fast {
  animation: slideUpFast 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes slideUpFast {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

input::placeholder {
  color: #cbd5e1;
  font-weight: 400;
  font-style: italic;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #C9A96E;
}
</style>
