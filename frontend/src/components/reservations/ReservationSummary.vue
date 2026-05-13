<template>
  <div class="bg-white rounded-xl shadow p-5 sticky top-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-3 border-b">Récapitulatif</h3>

    <div v-if="!bookingStore.selectedRoom" class="text-center text-gray-400 py-6">
      <p>Sélectionnez une chambre</p>
    </div>

    <template v-else>
      <!-- Chambre -->
      <div class="mb-4">
        <p class="text-sm font-semibold text-gray-600 mb-1">Chambre sélectionnée</p>
        <div class="flex justify-between items-start">
          <div>
            <p class="font-bold text-gray-800">N° {{ bookingStore.selectedRoom.number }}</p>
            <p class="text-sm text-gray-500">{{ bookingStore.selectedRoom.room_type?.name }}</p>
          </div>
          <p class="font-bold text-primary-600">{{ formatPrice(bookingStore.roomTotal) }} MAD</p>
        </div>
      </div>

      <!-- Dates -->
      <div class="bg-gray-50 rounded-lg p-3 mb-4 text-sm">
        <div class="flex justify-between mb-1">
          <span class="text-gray-500">Arrivée</span>
          <span class="font-medium">{{ formatDate(bookingStore.searchParams.check_in) }}</span>
        </div>
        <div class="flex justify-between mb-1">
          <span class="text-gray-500">Départ</span>
          <span class="font-medium">{{ formatDate(bookingStore.searchParams.check_out) }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Durée</span>
          <span class="font-medium">{{ bookingStore.nights }} nuit(s)</span>
        </div>
      </div>

      <!-- Services -->
      <div v-if="bookingStore.selectedServices.length" class="mb-4">
        <p class="text-sm font-semibold text-gray-600 mb-2">Services</p>
        <div v-for="s in bookingStore.selectedServices" :key="s.id"
             class="flex justify-between text-sm mb-1">
          <span class="text-gray-600">{{ s.icon }} {{ s.name }}</span>
          <span>{{ formatPrice(s.price * s.quantity) }} MAD</span>
        </div>
      </div>

      <!-- Réduction -->
      <div v-if="bookingStore.promoDiscount > 0"
           class="flex justify-between text-sm text-green-600 mb-3 font-medium">
        <span>🎟️ Réduction promo</span>
        <span>- {{ formatPrice(bookingStore.promoDiscount) }} MAD</span>
      </div>

      <!-- Séparateur + Total -->
      <div class="border-t pt-3 mt-3 flex justify-between items-center">
        <span class="font-bold text-gray-800 text-lg">Total</span>
        <span class="font-bold text-primary-600 text-xl">{{ formatPrice(bookingStore.grandTotal) }} MAD</span>
      </div>
    </template>
  </div>
</template>

<script setup>
import { useBookingStore } from '@/stores/booking'
const bookingStore = useBookingStore()
const formatPrice = (v) => new Intl.NumberFormat('fr-FR').format(v?.toFixed(2) ?? 0)
const formatDate  = (d) => d ? new Date(d).toLocaleDateString('fr-FR') : '—'
</script>
