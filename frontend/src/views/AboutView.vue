<template>
  <div class="bg-slate-50 min-h-screen pt-20">

    <!-- HERO -->
    <section class="relative h-[500px] flex items-center justify-center overflow-hidden">
      <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1920&q=80"
             class="w-full h-full object-cover" alt="Grand Palais Hôtel" />
        <div class="absolute inset-0 bg-primary/40 backdrop-blur-[2px]"></div>
      </div>
      <div class="relative z-10 text-center text-white px-6 animate-slide-up">
        <p class="text-xs font-bold tracking-[0.4em] uppercase mb-4 text-accent-light">✦ Héritage & Excellence ✦</p>
        <h1 class="text-5xl md:text-7xl font-serif mb-6">Notre Histoire</h1>
        <p class="max-w-2xl mx-auto text-sm md:text-base font-light tracking-wide opacity-80 leading-relaxed">
          Un hôtel de légende au cœur de Marrakech, où l'hospitalité marocaine 
          se perpétue depuis plus de 25 ans dans un cadre d'exception.
        </p>
      </div>
    </section>

    <!-- HISTOIRE SECTION -->
    <section class="py-32 px-6">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
          <div>
            <p class="text-accent font-bold tracking-[0.2em] uppercase text-xs mb-6">L'Art de Recevoir</p>
            <h2 class="text-5xl md:text-6xl font-serif text-slate-900 leading-tight mb-8">
              Une tradition <br><span class="italic text-accent">depuis 1998</span>
            </h2>
            <div class="w-16 h-1 bg-accent mb-8"></div>
            <div class="space-y-6 text-lg text-slate-600 font-light leading-relaxed">
              <p>
                Fondé en 1998 par la famille Beaumont, le Grand Palais est né d'une vision : 
                créer un refuge d'excellence au cœur de Marrakech, où chaque client serait traité 
                comme un invité d'honneur.
              </p>
              <p>
                Ce qui n'était autrefois qu'un palais de l'époque impériale est devenu 
                l'un des établissements les plus prisés du Royaume, reconnu internationalement 
                pour son service irréprochable.
              </p>
              <p>
                Aujourd'hui, nous continuons de perpétuer cette tradition de l'hospitalité 
                marocaine dans sa forme la plus pure, alliant modernité et classicisme.
              </p>
            </div>
          </div>

          <!-- Timeline -->
          <div class="relative space-y-12 before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent">
            <div v-for="(event, idx) in timeline" :key="idx" 
                 class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group transition-all">
              <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-slate-50 group-hover:bg-accent group-hover:text-white shadow-xl z-10 transition-all shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                <span class="text-[10px] font-bold">{{ event.year.slice(-2) }}</span>
              </div>
              <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-6 rounded-2xl shadow-sm border border-slate-100 group-hover:border-accent/20 transition-all">
                <div class="flex items-center justify-between mb-1">
                  <time class="font-serif font-bold text-accent">{{ event.year }}</time>
                </div>
                <h4 class="font-bold text-slate-900 mb-2">{{ event.title }}</h4>
                <p class="text-xs text-slate-500 leading-relaxed">{{ event.desc }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CHIFFRES CLÉS -->
    <section class="py-24 bg-white border-y border-slate-100">
      <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-12">
          <div v-for="stat in stats" :key="stat.label" 
               class="text-center p-8 rounded-[2rem] border border-slate-50 hover:bg-slate-50 transition-all group">
            <p class="text-5xl font-serif text-accent mb-4 group-hover:scale-110 transition-transform">{{ stat.value }}</p>
            <h4 class="text-primary font-bold text-sm tracking-widest uppercase mb-2">{{ stat.label }}</h4>
            <p class="text-slate-400 text-xs font-light">{{ stat.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- PHOTO GALLERY -->
    <section class="py-32 px-6">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <p class="text-accent font-bold tracking-[0.2em] uppercase text-xs mb-4">L'Établissement</p>
          <h2 class="text-5xl font-serif text-slate-900">Nos Espaces de Collection</h2>
        </div>

        <div class="grid grid-cols-12 gap-6 h-[800px]">
          <div class="col-span-12 md:col-span-8 rounded-[3rem] overflow-hidden group">
            <img src="https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=900&q=80"
                 class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
          </div>
          <div class="col-span-12 md:col-span-4 flex flex-col gap-6">
            <div class="flex-1 rounded-[3rem] overflow-hidden group">
              <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600&q=80"
                   class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
            </div>
            <div class="flex-1 rounded-[3rem] overflow-hidden group">
              <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&q=80"
                   class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-24">
          <div v-for="space in spaces" :key="space.name" class="card-elegant !rounded-[2.5rem] !p-0">
            <div class="relative h-64 overflow-hidden">
               <img :src="space.image" :alt="space.name" class="w-full h-full object-cover transition-transform duration-700 hover:scale-110" />
               <div class="absolute top-6 left-6">
                 <span class="px-3 py-1 bg-white/20 backdrop-blur-md border border-white/20 rounded-full text-[10px] font-bold text-white uppercase tracking-widest">
                   {{ space.category }}
                 </span>
               </div>
            </div>
            <div class="p-8">
              <h3 class="text-2xl font-serif text-primary mb-3">{{ space.name }}</h3>
              <p class="text-sm text-slate-500 font-light leading-relaxed">{{ space.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ÉQUIPE -->
    <section class="py-32 bg-slate-50 overflow-hidden border-y border-slate-100">
      <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-20">
          <p class="text-accent font-bold tracking-[0.2em] uppercase text-xs mb-4">La Direction</p>
          <h2 class="text-5xl font-serif text-primary">Les Artisans de votre Séjour</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div v-for="member in team" :key="member.name" class="group">
            <div class="relative mb-8 aspect-square rounded-[2rem] overflow-hidden border border-slate-100 shadow-sm">
              <img :src="member.photo" :alt="member.name" 
                   class="w-full h-full object-cover transition-all duration-700 group-hover:scale-110 group-hover:grayscale" />
              <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-end p-8">
                <p class="text-white text-xs font-light leading-relaxed italic">{{ member.bio }}</p>
              </div>
            </div>
            <h3 class="text-xl font-serif text-primary mb-1">{{ member.name }}</h3>
            <p class="text-accent font-bold text-[10px] tracking-widest uppercase">{{ member.role }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- LOCALISATION -->
    <section class="py-32 px-6">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
          <div>
            <p class="text-accent font-bold tracking-[0.2em] uppercase text-xs mb-6">Emplacement Privilégié</p>
            <h2 class="text-5xl md:text-6xl font-serif text-slate-900 leading-tight mb-12">
              Au cœur du <br><span class="italic text-accent">Triangle d'Or</span>
            </h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-12">
              <div v-for="info in locationInfo" :key="info.label" class="flex gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-center text-xl text-accent shrink-0">
                  <i :class="info.iconClass"></i>
                </div>
                <div>
                  <h4 class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">{{ info.label }}</h4>
                  <p class="text-sm font-semibold text-slate-900">{{ info.value }}</p>
                </div>
              </div>
            </div>

            <div class="flex flex-wrap gap-4">
               <router-link to="/rooms">
                 <button class="btn-premium rounded-xl">Réserver</button>
               </router-link>
               <router-link to="/services">
                 <button class="btn-outline-premium !rounded-xl">Nos Services</button>
               </router-link>
            </div>
          </div>

          <div class="relative h-[600px] rounded-[3rem] overflow-hidden shadow-2xl">
            <img src="https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=800&q=80"
                 class="w-full h-full object-cover" alt="Paris" />
            <div class="absolute inset-x-8 bottom-8 bg-white/95 backdrop-blur-md p-8 rounded-3xl shadow-xl border border-slate-100 animate-slide-up">
              <p class="text-accent font-bold text-[10px] tracking-[0.3em] uppercase mb-2">Signature Address</p>
              <h3 class="text-2xl font-serif text-primary mb-1">Hivernage, Marrakech</h3>
              <p class="text-slate-400 text-sm">Marrakech, Maroc • À 5 min de la Jemaa el-Fna</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- DISTINCTIONS -->
    <section class="py-32 px-6 bg-white border-t border-slate-100">
      <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
          <p class="text-accent font-bold tracking-[0.2em] uppercase text-xs mb-4">Reconnaissance</p>
          <h2 class="text-4xl font-serif text-slate-900">Nos Distinctions Mondiale</h2>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-12 text-center">
          <div v-for="award in awards" :key="award.title" class="group">
            <div class="text-4xl mb-4 group-hover:scale-110 transition-transform text-accent">
              <i :class="award.iconClass"></i>
            </div>
            <h4 class="font-bold text-slate-900 mb-1">{{ award.title }}</h4>
            <p class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">{{ award.year }}</p>
            <p class="text-xs text-slate-400">{{ award.org }}</p>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>

<script setup>
const timeline = [
  { year: '1998', title: 'Fondation', desc: 'Ouverture du Grand Palais par la famille Beaumont dans un hôtel particulier du XVIIIe siècle.' },
  { year: '2003', title: 'Michelin Star', desc: 'Notre restaurant gastronomique obtient sa première distinction internationale.' },
  { year: '2012', title: 'Label d\'Excellence', desc: 'Obtention de la plus haute distinction hôtelière du Royaume.' },
  { year: '2024', title: 'Aujourd\'hui', desc: 'Plus de 25 ans d\'excellence et d\'innovation au service de nos fidèles clients.' },
]

const stats = [
  { value: '25+',  label: 'Héritage', desc: 'Années d\'excellence' },
  { value: '80',  label: 'Élégance',    desc: 'Chambres & Suites' },
  { value: '80+', label: 'Dévouement',        desc: 'Collaborateurs' },
  { value: '98%',  label: 'Confiance',   desc: 'Satisfaction client' },
]

const spaces = [
  {
    name:     'Le Grand Hall',
    category: 'Architecture',
    image:    'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=600&q=80',
    desc:     'Une majesté de marbre et de cristal dès votre arrivée.',
  },
  {
    name:     'Jardins à la Française',
    category: 'Nature',
    image:    'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=600&q=80',
    desc:     'Un havre de paix de deux hectares au milieu de l\'agitation.',
  },
  {
    name:     'Terrasse de l\'Atlas',
    category: 'Panorama',
    image:    'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=600&q=80',
    desc:     'Dînez sous les étoiles avec une vue imprenable sur les sommets de l\'Atlas.',
  },
]

const team = [
  {
    name:  'Mohamed Amine Mansouri',
    role:  'Directeur de l\'Hospitalité',
    photo: '/images/team/director_mansouri_1777821701120.png',
    bio:   'Maître de l\'art de recevoir marocain, alliant tradition et excellence moderne.',
  },
  {
    name:  'Fatima-Zahra El Idrissi',
    role:  'Chef Exécutif Gastronomique',
    photo: '/images/team/chef_fatima_zahra_1777821975334.png',
    bio:   'Ambassadrice des saveurs de l\'Atlas, réinventant la cuisine impériale.',
  },
  {
    name:  'Yassine Benjelloun',
    role:  'Concierge Clefs d\'Or',
    photo: '/images/team/concierge_yassine_1777822341514.png',
    bio:   'Gardien des secrets de Marrakech, orchestrateur d\'expériences uniques.',
  },
  {
    name:  'Laila Bennani',
    role:  'Spa & Wellness Manager',
    photo: '/images/team/spa_laila_1777822554609.png',
    bio:   'Gardienne des rituels ancestraux et du bien-être holistique.',
  },
]

const locationInfo = [
  { iconClass: 'las la-map-marker', label: 'Adresse',  value: 'Hivernage, Marrakech, Maroc' },
  { iconClass: 'las la-phone',      label: 'Téléphone', value: '+212 5 24 00 00 00' },
  { iconClass: 'las la-subway',     label: 'Gare',     value: 'Marrakech ONCF - 5 min' },
  { iconClass: 'las la-plane',      label: 'Aéroport', value: 'RAK Menara - 15 min' },
]

const awards = [
  { iconClass: 'las la-star',        title: 'Étoile Michelin',    year: '2003 — 2024', org: 'Guide Michelin' },
  { iconClass: 'las la-trophy',      title: 'Palace Label',       year: '2012 — 2024', org: 'Ministère du Tourisme' },
  { iconClass: 'las la-certificate', title: 'Forbes 5 Étoiles',  year: '2015 — 2024', org: 'Forbes Travel Guide' },
  { iconClass: 'las la-medal',       title: 'Best Luxury Hotel', year: '2023',         org: 'World Travel Awards' },
]
</script>
