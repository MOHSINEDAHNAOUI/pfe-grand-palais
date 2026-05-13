<template>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="text-left text-gray-500 border-b">
          <th class="py-3 px-2 font-medium">Référence</th>
          <th class="py-3 px-2 font-medium">Client</th>
          <th class="py-3 px-2 font-medium">Chambre</th>
          <th class="py-3 px-2 font-medium">Arrivée</th>
          <th class="py-3 px-2 font-medium">Départ</th>
          <th class="py-3 px-2 font-medium">Statut</th>
          <th class="py-3 px-2 font-medium">Total</th>
          <th class="py-3 px-2 font-medium">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="r in reservations" :key="r.id"
            class="border-b hover:bg-gray-50 transition-colors">
          <td class="py-3 px-2 font-mono text-xs">{{ r.reference }}</td>
          <td class="py-3 px-2">
            <div class="font-medium text-gray-800">{{ r.user?.name }}</div>
            <div class="text-gray-400 text-xs">{{ r.user?.email }}</div>
          </td>
          <td class="py-3 px-2">{{ r.room?.number }}</td>
          <td class="py-3 px-2">{{ formatDate(r.check_in) }}</td>
          <td class="py-3 px-2">{{ formatDate(r.check_out) }}</td>
          <td class="py-3 px-2">
            <span :class="['text-xs font-semibold px-2 py-1 rounded-full', statusClass(r.status)]">
              {{ statusLabel(r.status) }}
            </span>
          </td>
          <td class="py-3 px-2 font-semibold">{{ r.total_price?.toFixed(2) }} MAD</td>
          <td class="py-3 px-2">
            <div class="flex gap-1">
              <button v-if="r.status === 'confirmed'"
                      @click="$emit('checkin', r)"
                      class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200">
                Check-in
              </button>
              <button v-if="r.status === 'checked_in'"
                      @click="$emit('checkout', r)"
                      class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded hover:bg-orange-200">
                Check-out
              </button>
              <button v-if="r.status === 'pending'"
                      @click="$emit('confirm', r)"
                      class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200">
                Confirmer
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-if="!reservations?.length" class="text-center py-10 text-gray-400">
      Aucune réservation
    </div>
  </div>
</template>

<script setup>
defineProps({ reservations: Array })
defineEmits(['checkin', 'checkout', 'confirm', 'refresh'])

const formatDate = (d) => d ? new Date(d).toLocaleDateString('fr-FR') : '—'

const statusLabel = (s) => ({
  pending:    'En attente',
  confirmed:  'Confirmée',
  checked_in: 'Présent',
  checked_out:'Parti',
  cancelled:  'Annulée',
  no_show:    'No-Show',
}[s] ?? s)

const statusClass = (s) => ({
  pending:    'bg-yellow-100 text-yellow-700',
  confirmed:  'bg-blue-100 text-blue-700',
  checked_in: 'bg-green-100 text-green-700',
  checked_out:'bg-gray-100 text-gray-600',
  cancelled:  'bg-red-100 text-red-700',
  no_show:    'bg-orange-100 text-orange-700',
}[s])
</script>
