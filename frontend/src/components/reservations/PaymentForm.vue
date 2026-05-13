<template>
  <div class="bg-white rounded-xl border p-6">
    <div class="flex items-center gap-3 mb-6 text-green-600 font-medium">
      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
      </svg>
      Paiement sécurisé SSL
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">Nom sur la carte</label>
      <input v-model="form.cardName" type="text" placeholder="Jean Dupont"
             class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de carte</label>
      <input v-model="form.cardNumber" type="text" placeholder="4242 4242 4242 4242"
             maxlength="19" @input="formatCard"
             class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Expiration</label>
        <input v-model="form.expiry" type="text" placeholder="MM/AA" maxlength="5"
               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
        <input v-model="form.cvv" type="password" placeholder="123" maxlength="4"
               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
      </div>
    </div>

    <div v-if="error" class="mb-4 text-red-600 text-sm bg-red-50 p-3 rounded-lg">{{ error }}</div>

    <button @click="processPayment" :disabled="loading || !isValid"
            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-lg transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
      <span v-if="loading">Traitement en cours...</span>
      <span v-else>💳 Payer {{ total }} MAD</span>
    </button>

    <p class="text-center text-xs text-gray-400 mt-4">
      Vos données bancaires sont cryptées et sécurisées. Nous ne stockons aucune information de carte.
    </p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useBookingStore } from '@/stores/booking'
import api from '@/api/axios'

const emit = defineEmits(['paid'])
const bookingStore = useBookingStore()

const form = ref({ cardName: '', cardNumber: '', expiry: '', cvv: '' })
const loading = ref(false)
const error   = ref('')

const total    = computed(() => bookingStore.grandTotal?.toFixed(2))
const isValid  = computed(() => form.value.cardName && form.value.cardNumber.length === 19
  && form.value.expiry.length === 5 && form.value.cvv.length >= 3)

const formatCard = () => {
  let v = form.value.cardNumber.replace(/\D/g, '').substring(0, 16)
  form.value.cardNumber = v.match(/.{1,4}/g)?.join(' ') ?? v
}

const processPayment = async () => {
  loading.value = true
  error.value   = ''
  try {
    // 1. Créer la réservation
    const reservation = await bookingStore.createReservation()

    // 2. Simuler le paiement (en prod: utiliser Stripe)
    await api.post(`/payments/${reservation.id}/confirm`, {
      transaction_id: 'txn_' + Date.now(),
    })

    emit('paid', reservation)
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Erreur lors du paiement. Réessayez.'
  } finally {
    loading.value = false
  }
}
</script>
