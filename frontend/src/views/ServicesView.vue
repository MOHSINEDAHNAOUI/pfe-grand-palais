<template>
  <div class="bg-slate-50 min-h-screen pt-20">

    <!-- HERO -->
    <section class="relative h-[600px] flex items-center justify-center overflow-hidden">
      <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=1920&q=80"
             class="w-full h-full object-cover animate-slow-zoom" alt="Services Grand Palais" />
        <div class="absolute inset-0 bg-primary/40 backdrop-blur-[2px]"></div>
      </div>
      <div class="relative z-10 text-center text-white px-6 animate-slide-up">
        <p class="text-[10px] font-bold tracking-[0.5em] uppercase mb-6 text-accent-light">✦ L'Art de l'Hospitalité ✦</p>
        <h1 class="text-6xl md:text-8xl font-serif mb-8">Nos Services</h1>
        <p class="max-w-xl mx-auto text-sm md:text-lg font-light tracking-wide opacity-80 leading-relaxed">
          Au Grand Palais, chaque détail est une promesse d'excellence, 
          chaque service une orchestration du raffinement.
        </p>
      </div>
    </section>

    <!-- PHILOSOPHY -->
    <section class="py-32 bg-white border-b border-slate-100 overflow-hidden">
      <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
          <div class="relative">
             <div class="absolute -top-10 -left-10 text-[12rem] font-serif text-slate-50 select-none">"</div>
             <p class="text-accent font-bold tracking-[0.3em] uppercase text-xs mb-8">La Signature Beaumont</p>
             <h2 class="text-5xl md:text-6xl font-serif leading-tight mb-10 text-primary">
               L'anticipation est la forme suprême de <span class="italic text-accent">l'élégance</span>.
             </h2>
             <div class="w-20 h-1 bg-accent mb-10"></div>
             <p class="text-lg text-slate-500 font-light leading-relaxed max-w-lg">
               Nous croyons que le vrai luxe réside dans l'imperceptible. 
               Nos équipes sont formées pour précéder vos désirs, créant une fluidité 
               qui rend chaque séjour au Grand Palais absolument unique.
             </p>
          </div>
          <div class="grid grid-cols-2 gap-6">
            <div class="aspect-[4/5] rounded-[2rem] overflow-hidden mt-12 shadow-xl">
              <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500&q=80" class="w-full h-full object-cover" />
            </div>
            <div class="aspect-[4/5] rounded-[2rem] overflow-hidden shadow-xl">
              <img src="https://images.unsplash.com/photo-1544161599-43589d171754?w=500&q=80" class="w-full h-full object-cover" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SERVICES SIGNATURE -->
    <section class="py-32 px-6">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-24">
          <p class="text-accent font-bold tracking-[0.2em] uppercase text-xs mb-4">Expériences</p>
          <h2 class="text-5xl font-serif text-primary">Incontournables</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
          <div v-for="(service, idx) in mainServices" :key="service.title"
               class="group relative h-[500px] rounded-[3rem] overflow-hidden shadow-xl transition-all hover:shadow-2xl translate-y-0 hover:-translate-y-2">
            <img :src="service.image" :alt="service.title"
                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
            
            <div class="absolute bottom-0 left-0 right-0 p-10">
              <div class="flex items-center gap-4 mb-4">
                <span class="text-xs font-bold text-accent-light tracking-widest uppercase">{{ service.category }}</span>
                <div class="flex-1 h-px bg-white/20"></div>
              </div>
              <h3 class="text-3xl font-serif text-white mb-4">{{ service.title }}</h3>
              <p class="text-sm text-white/60 font-light leading-relaxed mb-8 max-w-md opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-y-4 group-hover:translate-y-0">
                {{ service.description }}
              </p>
              
              <div class="flex items-center justify-between">
                <div class="flex flex-col">
                  <span class="text-[10px] text-white/40 uppercase tracking-widest">{{ service.price ? 'À partir de' : 'Service' }}</span>
                  <span class="text-xl font-serif text-accent-light">{{ service.price || 'Inclus' }}</span>
                </div>
                <router-link to="/rooms">
                  <button class="w-12 h-12 rounded-full border border-white/30 flex items-center justify-center text-white hover:bg-white hover:text-primary transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                  </button>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CONCIERGERIE PREMIUM -->
    <section class="py-32 bg-slate-50 relative overflow-hidden">
      <!-- Animated patterns -->
      <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-accent/5 rounded-full blur-3xl -mr-64 -mt-64"></div>
      
      <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
          <div>
            <p class="text-accent font-bold tracking-[0.4em] uppercase text-[10px] mb-8">Exclusivité Mondiale</p>
            <h2 class="text-5xl md:text-7xl font-serif text-primary mb-10 leading-tight">La Conciergerie <br><span class="italic text-accent">Clefs d'Or</span></h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-12 gap-y-6 mb-12">
               <div v-for="item in conciergeServices" :key="item" class="flex items-center gap-4 group">
                 <i class="las la-check-circle text-accent/40 group-hover:text-accent transition-all"></i>
                 <span class="text-sm text-slate-500 font-light group-hover:text-primary transition-colors">{{ item }}</span>
               </div>
            </div>

            <div class="flex items-center gap-8 p-8 rounded-3xl bg-white border border-slate-100 shadow-sm">
               <div class="text-4xl text-accent">
                 <i class="las la-award"></i>
               </div>
               <div>
                  <h4 class="text-primary font-bold text-sm mb-1 uppercase tracking-widest">Disponibilité Sans Faille</h4>
                  <p class="text-slate-400 text-xs font-light">Nos concierges membres de l'association prestigieuse des Clefs d'Or sont à votre service 24h/24.</p>
               </div>
            </div>
          </div>
          
          <div class="relative group">
            <div class="absolute -inset-4 bg-accent/10 rounded-[3rem] blur-2xl transform rotate-3 group-hover:rotate-1 transition-all"></div>
            <div class="relative rounded-[3rem] overflow-hidden shadow-2xl h-[600px]">
              <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80" class="w-full h-full object-cover" />
              <div class="absolute bottom-10 left-10 p-10 bg-white/95 backdrop-blur-md rounded-3xl shadow-xl max-w-xs transition-transform group-hover:-translate-y-2">
                 <p class="text-accent font-bold text-[10px] tracking-widest uppercase mb-2">Concierge en Chef</p>
                 <h3 class="text-2xl font-serif text-primary mb-2">Service à la demande</h3>
                 <p class="text-slate-500 text-xs leading-relaxed">Réservation de dernière minute au Jules Verne ou jet privé pour Nice, nous réalisons l'impossible.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ RAFFINÉE -->
    <section class="py-32 px-6">
      <div class="max-w-4xl mx-auto">
        <div class="text-center mb-20">
          <p class="text-accent font-bold tracking-[0.2em] uppercase text-xs mb-4">Informations</p>
          <h2 class="text-4xl font-serif text-primary">Le Séjour en Détails</h2>
        </div>

        <div class="space-y-4">
          <div v-for="(faq, i) in faqs" :key="i"
               class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden transition-all hover:shadow-md">
            <button @click="toggleFaq(i)"
                    class="w-full px-10 py-8 flex items-center justify-between text-left group">
              <span class="text-lg font-serif text-primary group-hover:text-accent transition-colors">{{ faq.q }}</span>
              <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center transition-all"
                   :class="{ 'rotate-45 bg-accent text-white': openFaq === i }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
              </div>
            </button>
            <div v-show="openFaq === i" class="px-10 pb-8 animate-fade-in">
              <div class="h-px bg-slate-100 mb-6"></div>
              <p class="text-slate-600 font-light leading-relaxed">{{ faq.a }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER CTA -->
    <section class="py-32 bg-slate-50 border-t border-slate-200">
      <div class="max-w-7xl mx-auto px-6 text-center">
        <h2 class="text-5xl md:text-7xl font-serif text-primary mb-12">Au-delà de vos <span class="italic text-accent">attentes</span>.</h2>
        <div class="flex flex-wrap gap-6 justify-center">
          <router-link to="/rooms">
            <button class="btn-premium rounded-full px-12 py-5">Réserver Maintenant</button>
          </router-link>
          <router-link to="/about">
            <button class="btn-outline-premium !rounded-full px-12 py-5">Notre Histoire</button>
          </router-link>
        </div>
      </div>
    </section>

  </div>
</template>

<script setup>
import { ref } from 'vue'

const openFaq = ref(null)

const toggleFaq = (i) => {
  openFaq.value = openFaq.value === i ? null : i
}

const mainServices = [
  {
    title:       'Gastronomie Étoilée',
    category:    'Cuisine de Collection',
    image:       'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&q=80',
    description: 'Une orchestration culinaire récompensée par le Guide Michelin, où les produits du terroir français sont sublimés par notre chef exécutif.',
    price:       '185 MAD / menu',
    iconClass:   'las la-utensils',
  },
  {
    title:       'Le Sanctuaire Spa',
    category:    'Bien-être Holistique',
    image:       'https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=800&q=80',
    description: '1500 m² dédiés à l\'équilibre des sens. Soins sur mesure, balnéothérapie et une piscine intérieure baignée de lumière naturelle.',
    price:       '120 MAD / soin',
    iconClass:   'las la-spa',
  },
  {
    title:       'Piscine à Débordement',
    category:    'Détente Panorama',
    image:       'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&q=80',
    description: 'Nagez au-dessus de la ville. Notre piscine extérieure offre une vue imprenable sur les monuments historiques de Paris.',
    price:       null,
    iconClass:   'las la-swimming-pool',
  },
  {
    title:       'Lounge Impérial',
    category:    'Art de Vivre',
    image:       'https://images.unsplash.com/photo-1470337458703-46ad1756a187?w=800&q=80',
    description: 'Dégustez nos cocktails signatures dans un cadre feutré, entouré de livres rares et d\'œuvres d\'art contemporaines.',
    price:       null,
    iconClass:   'las la-glass-martini-alt',
  },
]

const conciergeServices = [
  'Tables Gastronomiques privatisées',
  'Excursions Sur-mesure (Louvre VIP)',
  'Jets Privés & Transferts Hélicoptère',
  'Personal Shopper Avenue Montaigne',
  'Arrangements Floraux Signature',
  'Coordination d\'Événements de Prestige',
]

const faqs = [
  {
    q: 'Comment accéder au Spa & Fitness ?',
    a: 'L\'accès à la salle de sport et à la piscine à débordement est gracieusement offert à tous nos résidents. Pour les soins et massages, nous vous recommandons de réserver au moins 48h à l\'avance.',
  },
  {
    q: 'Puis-je commander en chambre 24h/24 ?',
    a: 'Absolument. Notre service In-Room Dining propose une carte complète de jour comme de nuit, préparée par nos cuisines dédiées.',
  },
  {
    q: 'Organisez-vous des transferts aéroport ?',
    a: 'Notre Conciergerie dispose d\'une flotte de berlines de luxe pour vos transferts vers CDG, Orly ou Le Bourget. Service avec chauffeur privé.',
  },
  {
    q: 'Proposez-vous des services pour enfants ?',
    a: 'Nous accueillons les familles avec une attention particulière : service de babysitting Clefs d\'Or, menus enfants gastronomiques et activités culturelles dédiées.',
  },
]
</script>

<style scoped>
.animate-slow-zoom {
  animation: slowZoom 30s linear infinite alternate;
}

@keyframes slowZoom {
  from { transform: scale(1); }
  to { transform: scale(1.15); }
}

.animate-fade-in {
  animation: fadeIn 0.6s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
rangements personnalisés : champagne, fleurs, décoration de chambre, dîner en tête-à-tête. Contactez-nous pour créer votre moment unique.',
  },
]
