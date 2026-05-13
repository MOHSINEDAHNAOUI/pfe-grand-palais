<!-- frontend/src/components/rooms/SearchForm.vue -->
<template>
  <div class="bg-white rounded-2xl shadow-xl p-6">
    <h2 class="text-2xl font-serif font-bold text-gray-800 mb-6">Rechercher une chambre</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Arrivée</label>
        <input
          v-model="form.check_in"
          type="date"
          :min="today"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Départ</label>
        <input
          v-model="form.check_out"
          type="date"
          :min="form.check_in || today"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Adultes</label>
        <select
          v-model="form.adults"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
        >
          <option v-for="n in 6" :key="n" :value="n">{{ n }}</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Enfants</label>
        <select
          v-model="form.children"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
        >
          <option v-for="n in [0,1,2,3,4]" :key="n" :value="n">{{ n }}</option>
        </select>
      </div>
    </div>
    <div class="mt-4">
      <button
        @click="search"
        :disabled="!isValid || loading"
        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span v-if="loading">Recherche en cours...</span>
        <span v-else>🔍 Rechercher</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { useBookingStore } from '@/stores/booking'
import { useRouter } from 'vue-router'

const bookingStore = useBookingStore()
const router = useRouter()

const today = new Date().toISOString().split('T')[0]
const loading = computed(() => bookingStore.loading)

const form = reactive({
  check_in: '',
  check_out: '',
  adults: 2,
  children: 0,
})

const isValid = computed(() => form.check_in && form.check_out && form.check_in < form.check_out)

const search = async () => {
  Object.assign(bookingStore.searchParams, form)
  await bookingStore.searchRooms()
  router.push({ name: 'rooms', query: form })
}
</script>
