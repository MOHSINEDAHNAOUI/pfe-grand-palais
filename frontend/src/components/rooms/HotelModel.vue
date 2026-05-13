<template>
  <div ref="container" 
       class="w-full h-[650px] relative rounded-[3rem] overflow-hidden bg-[#b8d4f0] shadow-2xl touch-none border border-slate-200/50">
    
    <!-- Loading Overlay -->
    <div v-if="!initialized" class="absolute inset-0 z-50 bg-[#b8d4f0] flex flex-col items-center justify-center gap-6">
      <div class="relative w-16 h-16">
        <div class="absolute inset-0 border-4 border-white/20 rounded-full"></div>
        <div class="absolute inset-0 border-4 border-accent rounded-full border-t-transparent animate-spin"></div>
      </div>
      <p class="text-[10px] font-bold text-primary uppercase tracking-[0.4em] animate-pulse">Initialisation du Palais 3D...</p>
    </div>

    <!-- UI Overlay: Instructions -->
    <div class="absolute top-8 left-8 z-10 pointer-events-none space-y-4">
      <div class="bg-white/90 backdrop-blur-md px-6 py-4 rounded-3xl shadow-xl border border-white/20 flex flex-col gap-2">
        <p class="text-[10px] font-black uppercase tracking-widest text-primary">Exploration Immersive</p>
        <div class="space-y-1.5">
          <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-accent"></span> Pivot : Clic Gauche
          </p>
          <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-accent"></span> Zoom : Molette
          </p>
          <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-accent"></span> Sélection : Clic Chambre
          </p>
        </div>
      </div>
    </div>

    <!-- UI Overlay: Status Legend -->
    <div class="absolute bottom-8 right-8 z-10 flex flex-col gap-3">
      <div v-for="s in legend" :key="s.label" 
           class="flex items-center gap-4 bg-white/90 backdrop-blur-md px-4 py-2 rounded-2xl shadow-lg border border-white/20 group hover:scale-105 transition-all cursor-default">
        <div class="w-2.5 h-2.5 rounded-full shadow-sm" :style="{ backgroundColor: s.color }"></div>
        <span class="text-[9px] font-black text-primary uppercase tracking-widest">{{ s.label }}</span>
      </div>
    </div>

    <!-- Tooltip -->
    <transition name="fade">
      <div v-if="hoveredRoom" 
           :style="{ left: mouse.x + 'px', top: mouse.y + 'px' }"
           class="fixed z-[60] pointer-events-none bg-white/95 backdrop-blur-xl px-5 py-3 rounded-2xl shadow-2xl border border-slate-200 -translate-x-1/2 -translate-y-full mb-6">
        <div class="flex flex-col gap-1">
          <span class="text-[9px] font-black text-accent uppercase tracking-widest">Chambre {{ hoveredRoom.number }}</span>
          <span class="text-sm font-serif text-primary">{{ hoveredRoom.room_type?.name }}</span>
          <div class="h-px bg-slate-100 my-1"></div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ hoveredRoom.view }} View</span>
        </div>
      </div>
    </transition>

    <!-- Controls -->
    <div class="absolute top-8 right-8 z-10 flex gap-3">
      <button @click="resetCamera" class="w-12 h-12 bg-white/90 backdrop-blur-md rounded-2xl shadow-lg border border-white/20 flex items-center justify-center text-primary hover:bg-accent hover:text-white transition-all active:scale-95 group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:rotate-180 transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import * as THREE from 'three'
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls'

const props = defineProps({
  rooms: { type: Array, default: () => [] },
  selectedId: { type: Number, default: null }
})

const emit = defineEmits(['select-room'])

const container = ref(null)
const mouse = ref({ x: 0, y: 0 })
const hoveredRoom = ref(null)
const initialized = ref(false)

const legend = [
  { label: 'Disponible', color: '#22c55e' },
  { label: 'Indisponible', color: '#f59e0b' },
  { label: 'Maintenance', color: '#ef4444' }
]

let scene, camera, renderer, controls, raycaster
let roomMeshes = []
let animationId = null

const initScene = () => {
  if (!container.value) return

  scene = new THREE.Scene()
  scene.background = new THREE.Color('#b8d4f0')
  scene.fog = new THREE.FogExp2('#cce0f5', 0.0008)

  camera = new THREE.PerspectiveCamera(50, container.value.clientWidth / container.value.clientHeight, 0.1, 2000)
  camera.position.set(0, 130, 200)

  renderer = new THREE.WebGLRenderer({ antialias: true, logarithmicDepthBuffer: true })
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))
  renderer.setSize(container.value.clientWidth, container.value.clientHeight)
  renderer.shadowMap.enabled = true
  renderer.shadowMap.type = THREE.PCFSoftShadowMap
  renderer.toneMapping = THREE.ACESFilmicToneMapping
  renderer.toneMappingExposure = 1.1
  container.value.appendChild(renderer.domElement)

  controls = new OrbitControls(camera, renderer.domElement)
  controls.enableDamping = true
  controls.dampingFactor = 0.06
  controls.maxPolarAngle = Math.PI / 2.1
  controls.minDistance = 60
  controls.maxDistance = 450
  controls.target.set(0, 8, -15)
  controls.update()

  // Lighting
  const hemi = new THREE.HemisphereLight(0xfff8e1, 0xd4a96a, 1.1)
  scene.add(hemi)

  const sun = new THREE.DirectionalLight(0xfff3d0, 2.8)
  sun.position.set(120, 200, 100)
  sun.castShadow = true
  sun.shadow.mapSize.set(2048, 2048)
  scene.add(sun)

  const spot = new THREE.SpotLight(0xffffff, 4)
  spot.position.set(-150, 100, 150)
  spot.castShadow = true
  scene.add(spot)

  raycaster = new THREE.Raycaster()

  buildPalace()
  animate()
  initialized.value = true
}

const getStatusColor = (status) => {
  switch(status) {
    case 'available': return 0x22c55e
    case 'occupied': return 0xf59e0b
    case 'maintenance': return 0xef4444
    default: return 0x6b7280
  }
}

const buildPalace = () => {
  if (!scene) return
  
  // Cleanup existing
  const toRemove = []
  scene.traverse(child => {
    if (child.userData.isStructure || child.userData.isRoom || child.userData.isEnv) {
      toRemove.push(child)
    }
  })
  toRemove.forEach(c => scene.remove(c))
  roomMeshes = []

  const M  = (color, rough=0.5, metal=0) => new THREE.MeshStandardMaterial({color, roughness:rough, metalness:metal})
  const GL = (color, op=0.45) => new THREE.MeshStandardMaterial({color, metalness:0.85, roughness:0.03, transparent:true, opacity:op})
  
  const gold = M('#D4AF37', 0.1, 0.9)
  const cream = M('#f5f0e8', 0.25, 0.06)
  const palmTrunk = M('#795548', 0.9)
  const palmLeaf = M('#388e3c', 0.75)

  // Terrain & Water
  const ground = new THREE.Mesh(new THREE.PlaneGeometry(900, 900), M('#c4b49a', 0.85))
  ground.rotation.x = -Math.PI/2
  ground.receiveShadow = true
  ground.userData.isEnv = true
  scene.add(ground)

  const sea = new THREE.Mesh(new THREE.PlaneGeometry(900, 350), M('#0077b6', 0.1, 0.6))
  sea.position.set(0, 0.1, -420)
  sea.rotation.x = -Math.PI/2
  sea.userData.isEnv = true
  scene.add(sea)

  // Building structure (Simplified C-Shape for high-end resort feel)
  const floorsCount = 6, R = 62, arc = Math.PI * 1.3, rPerF = 20, fH = 5.5
  const outerR = R + 9, innerR = R

  for (let fi = 0; fi < floorsCount; fi++) {
    const yMid = fi * fH + fH / 2
    const floorRooms = props.rooms.filter(r => String(r.floor) === String.fromCharCode(65 + fi))

    for (let i = 0; i < rPerF; i++) {
      const roomData = floorRooms[i]
      const t = (i + 0.5) / rPerF
      const ang = (Math.PI - arc / 2) + t * arc
      const rx = Math.sin(ang) * (innerR + 4.5)
      const rz = -Math.cos(ang) * (innerR + 4.5)

      const color = roomData ? getStatusColor(roomData.status) : 0xd4c5a9
      const rMat = new THREE.MeshStandardMaterial({ color, emissive: color, emissiveIntensity: 0.1, metalness: 0.1, roughness: 0.3 })
      const rm = new THREE.Mesh(new THREE.BoxGeometry(8, fH-1, 9), rMat)
      rm.position.set(rx, yMid, rz)
      rm.rotation.y = -ang
      rm.castShadow = true
      rm.receiveShadow = true
      rm.userData = { isRoom: true, room: roomData }
      scene.add(rm)
      if (roomData) roomMeshes.push(rm)

      // Windows
      const win = new THREE.Mesh(new THREE.BoxGeometry(6, 3, 0.5), GL('#b3d9f7', 0.5))
      win.position.set(Math.sin(ang) * (outerR + 0.1), yMid, -Math.cos(ang) * (outerR + 0.1))
      win.rotation.y = -ang
      win.userData.isStructure = true
      scene.add(win)
    }
    
    // Floor Slabs
    const slab = new THREE.Mesh(new THREE.CylinderGeometry(outerR+1, outerR+1, 0.4, 64, 1, false, Math.PI-arc/2, arc), gold)
    slab.position.y = fi * fH
    slab.userData.isStructure = true
    scene.add(slab)
  }

  // Pool
  const pool = new THREE.Mesh(new THREE.BoxGeometry(80, 0.5, 50), GL('#00b4d8', 0.9))
  pool.position.set(0, 0.5, -15)
  pool.userData.isEnv = true
  scene.add(pool)

  // Palms
  const palmPos = [[-90, -40], [90, -40], [-50, 100], [50, 100], [0, 80]]
  palmPos.forEach(([px, pz]) => {
    const h = 10 + Math.random() * 5
    const t = new THREE.Mesh(new THREE.CylinderGeometry(0.3, 0.6, h, 8), palmTrunk)
    t.position.set(px, h/2, pz)
    t.castShadow = true
    t.userData.isEnv = true
    scene.add(t)
    const l = new THREE.Mesh(new THREE.SphereGeometry(3, 8, 8), palmLeaf)
    l.position.set(px, h, pz)
    l.userData.isEnv = true
    scene.add(l)
  })
}

const animate = () => {
  animationId = requestAnimationFrame(animate)
  controls.update()
  renderer.render(scene, camera)
}

const onPointerMove = (event) => {
  const rect = container.value?.getBoundingClientRect()
  if (!rect) return

  mouse.value.x = event.clientX
  mouse.value.y = event.clientY
  
  const x = ((event.clientX - rect.left) / container.value.clientWidth) * 2 - 1
  const y = -((event.clientY - rect.top) / container.value.clientHeight) * 2 + 1
  
  raycaster.setFromCamera({ x, y }, camera)
  const intersects = raycaster.intersectObjects(roomMeshes)
  
  if (intersects.length > 0) {
    const object = intersects[0].object
    if (hoveredRoom.value?.id !== object.userData.room?.id) {
       hoveredRoom.value = object.userData.room
       document.body.style.cursor = 'pointer'
    }
  } else {
    hoveredRoom.value = null
    document.body.style.cursor = 'default'
  }
}

const onMouseClick = (event) => {
  if (hoveredRoom.value) {
    emit('select-room', hoveredRoom.value)
  }
}

const resetCamera = () => {
  camera.position.set(0, 130, 200)
  controls.target.set(0, 8, -15)
}

watch(() => props.rooms, () => buildPalace(), { deep: true })

onMounted(() => {
  setTimeout(initScene, 100)
  window.addEventListener('mousemove', onPointerMove)
  window.addEventListener('click', onMouseClick)
})

onUnmounted(() => {
  cancelAnimationFrame(animationId)
  window.removeEventListener('mousemove', onPointerMove)
  window.removeEventListener('click', onMouseClick)
  if (renderer) {
    renderer.dispose()
    renderer.domElement.remove()
  }
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
