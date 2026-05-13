<template>
  <div>
    <div v-for="(categoryServices, category) in services" :key="category" class="mb-6">
      <h3 class="text-lg font-semibold text-gray-700 mb-3 capitalize">
        {{ category === 'basic' ? '⭐ Services de base' : '💎 Services premium' }}
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div
          v-for="service in categoryServices"
          :key="service.id"
          @click="toggleService(service)"
          :class="[
            'border-2 rounded-xl p-4 cursor-pointer transition-all',
            isSelected(service.id)
              ? 'border-primary-500 bg-primary-50'
              : 'border-gray-200 hover:border-gray-300'
          ]"
        >
          <div class="flex justify-between items-start">
            <div class="flex items-start gap-3">
              <span class="text-2xl">{{ service.icon }}</span>
              <div>
                <p class="font-semibold text-gray-800">{{ service.name }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ service.description }}</p>
              </div>
            </div>
            <div class="text-right ml-2 shrink-0">
              <p class="font-bold text-primary-600">{{ service.price }} MAD</p>
              <p class="text-xs text-gray-400">{{ priceTypeLabel(service.price_type) }}</p>
            </div>
          </div>
          <div v-if="isSelected(service.id)" class="mt-3 flex items-center gap-2">
            <span class="text-sm text-gray-600">Quantité :</span>
            <div class="flex items-center gap-2">
              <button @click.stop="decreaseQty(service)"
                      class="w-7 h-7 bg-gray-200 rounded-full text-sm font-bold hover:bg-gray-300">−</button>
              <span class="font-semibold">{{ getQty(service.id) }}</span>
              <button @click.stop="increaseQty(service)"
                      class="w-7 h-7 bg-primary-600 text-white rounded-full text-sm font-bold hover:bg-primary-700">+</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useBookingStore } from '@/stores/booking'
import api from '@/api/axios'

const bookingStore = useBookingStore()
const services = ref({})

onMounted(async () => {
  const { data } = await api.get('/services')
  services.value = data
})

const isSelected = (id) => bookingStore.selectedServices.some(s => s.id === id)
const getQty     = (id) => bookingStore.selectedServices.find(s => s.id === id)?.quantity ?? 1

const toggleService = (service) => {
  bookingStore.toggleService(service)
}

const increaseQty = (service) => {
  const s = bookingStore.selectedServices.find(s => s.id === service.id)
  if (s) s.quantity++
}

const decreaseQty = (service) => {
  const s = bookingStore.selectedServices.find(s => s.id === service.id)
  if (s && s.quantity > 1) s.quantity--
  else bookingStore.toggleService(service)
}

const priceTypeLabel = (type) => ({
  per_night:  'par nuit',
  per_person: 'par personne',
  one_time:   'forfait',
}[type] ?? type)
</script>
