<template>
  <div class="relative">
    <!-- Image principale -->
    <div class="relative overflow-hidden rounded-2xl bg-gray-100 h-72 md:h-96 cursor-zoom-in"
         @click="openLightbox(activeIndex)">
      <img
        :src="activeImage"
        :alt="'Image ' + (activeIndex + 1)"
        class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
      />
      <div class="absolute inset-0 flex items-center justify-between px-4">
        <button @click.stop="prev"
                class="w-10 h-10 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow hover:bg-white transition">
          ‹
        </button>
        <button @click.stop="next"
                class="w-10 h-10 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow hover:bg-white transition">
          ›
        </button>
      </div>
      <div class="absolute bottom-3 right-3 bg-black/50 text-white text-xs px-3 py-1 rounded-full backdrop-blur">
        {{ activeIndex + 1 }} / {{ images.length }}
      </div>
    </div>

    <!-- Thumbnails -->
    <div class="flex gap-2 mt-3 overflow-x-auto pb-1">
      <button
        v-for="(img, i) in images"
        :key="i"
        @click="activeIndex = i"
        :class="[
          'shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-all',
          i === activeIndex ? 'border-primary-500 opacity-100' : 'border-transparent opacity-60 hover:opacity-80'
        ]"
      >
        <img :src="getUrl(img)" class="w-full h-full object-cover" :alt="'Thumb ' + (i + 1)" />
      </button>
    </div>

    <!-- Lightbox -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="lightboxOpen"
             class="fixed inset-0 z-50 bg-black/95 flex items-center justify-center"
             @click.self="lightboxOpen = false">
          <button @click="lightboxOpen = false"
                  class="absolute top-4 right-4 text-white text-3xl leading-none hover:text-gray-300">×</button>
          <button @click="prev"
                  class="absolute left-4 text-white text-5xl hover:text-gray-300 leading-none">‹</button>
          <img :src="activeImage" class="max-w-5xl max-h-[85vh] object-contain rounded-lg shadow-2xl" />
          <button @click="next"
                  class="absolute right-4 text-white text-5xl hover:text-gray-300 leading-none">›</button>
          <div class="absolute bottom-4 text-white/60 text-sm">
            {{ activeIndex + 1 }} / {{ images.length }}
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  images: { type: Array, default: () => [] }
})

const activeIndex  = ref(0)
const lightboxOpen = ref(false)

const getUrl = (img) => img?.url ?? (img?.path ? `/storage/${img.path}` : '/images/room-placeholder.jpg')

const activeImage = computed(() => {
  const img = props.images[activeIndex.value]
  return img ? getUrl(img) : '/images/room-placeholder.jpg'
})

const prev = () => activeIndex.value = (activeIndex.value - 1 + props.images.length) % props.images.length
const next = () => activeIndex.value = (activeIndex.value + 1) % props.images.length
const openLightbox = (i) => { activeIndex.value = i; lightboxOpen.value = true }
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
