<!-- frontend/src/components/rooms/RoomCard.vue -->
<template>
  <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
    <div class="relative">
      <img
        :src="primaryImage"
        @error="handleImageError"
        :alt="room.room_type?.name"
        class="w-full h-48 object-cover"
      />
      <div class="absolute top-3 right-3 bg-primary-600 text-white text-sm font-semibold px-3 py-1 rounded-full">
        {{ room.room_type?.name }}
      </div>
    </div>
    <div class="p-5">
      <div class="flex justify-between items-start mb-3">
        <div>
          <h3 class="text-lg font-bold text-gray-800">Chambre {{ room.number }}</h3>
          <p class="text-sm text-gray-500">Étage {{ room.floor }} • {{ room.surface }} m²</p>
        </div>
        <div class="text-right">
          <div v-if="room.period_price" class="text-xl font-bold text-primary-600">
            {{ formatPrice(room.period_price) }} MAD
          </div>
          <div v-else class="text-xl font-bold text-primary-600">
            {{ formatPrice(room.room_type?.base_price) }} MAD/nuit
          </div>
          <p v-if="room.nights" class="text-xs text-gray-500">pour {{ room.nights }} nuit(s)</p>
        </div>
      </div>

      <div v-if="room.view" class="text-sm text-gray-600 mb-3">
        🌅 Vue {{ room.view }}
      </div>

      <div class="flex flex-wrap gap-1 mb-4">
        <span
          v-for="amenity in room.amenities?.slice(0, 4)"
          :key="amenity.id"
          class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full"
        >
          {{ amenity.name }}
        </span>
        <span
          v-if="room.amenities?.length > 4"
          class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full"
        >
          +{{ room.amenities.length - 4 }}
        </span>
      </div>

      <div class="flex gap-2">
        <router-link
          :to="{ name: 'room-detail', params: { id: room.id } }"
          class="flex-1 text-center border border-primary-600 text-primary-600 hover:bg-primary-50 py-2 px-4 rounded-lg text-sm font-medium transition-colors"
        >
          Voir détails
        </router-link>
        <button
          @click="$emit('select', room)"
          class="flex-1 bg-primary-600 hover:bg-primary-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors"
        >
          Réserver
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({ room: { type: Object, required: true } })
defineEmits(['select'])

const fallbackImage = (room) => {
  const slug = room?.room_type?.slug || 'suite'
  const slugImages = {
    simple:    'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?w=800&q=90',
    double:    'https://images.unsplash.com/photo-1591088398332-8a77d397e805?w=800&q=90',
    suite:     'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=800&q=90',
    familiale: 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=800&q=90',
  }
  return slugImages[slug] || slugImages.suite
}

const primaryImage = computed(() => {
  const img = props.room.images?.find(i => i.is_primary) || props.room.images?.[0]
  if (img?.path) {
    // Check if it's a full URL already
    if (img.path.startsWith('http')) return img.path
    return `http://localhost:8000/storage/${img.path}`
  }
  return fallbackImage(props.room)
})

const handleImageError = (e) => {
  e.target.src = fallbackImage(props.room)
}

const formatPrice = (price) => new Intl.NumberFormat('fr-FR').format(price)
</script>
