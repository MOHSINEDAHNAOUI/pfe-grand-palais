<template>
  <div>
    <div v-if="!amenities?.length" class="text-gray-400 text-sm">Aucun équipement renseigné.</div>

    <div v-else>
      <!-- Par catégorie -->
      <div v-for="(group, category) in grouped" :key="category" class="mb-5">
        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">
          {{ categoryLabel(category) }}
        </h4>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
          <div
            v-for="amenity in group"
            :key="amenity.id"
            class="flex items-center gap-2.5 bg-gray-50 rounded-lg px-3 py-2.5"
          >
            <span class="text-primary-600 text-lg">✓</span>
            <span class="text-sm text-gray-700">{{ amenity.name }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  amenities: { type: Array, default: () => [] }
})

const grouped = computed(() => {
  return props.amenities.reduce((acc, a) => {
    const cat = a.category || 'other'
    if (!acc[cat]) acc[cat] = []
    acc[cat].push(a)
    return acc
  }, {})
})

const categoryLabel = (cat) => ({
  technology: '💻 Technologie',
  comfort:    '🛋️ Confort',
  bathroom:   '🛁 Salle de bain',
  other:      '📦 Autres',
}[cat] ?? cat)
</script>
