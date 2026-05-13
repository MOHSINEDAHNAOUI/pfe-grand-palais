<template>
  <div class="flex items-center justify-between mt-6">
    <p class="text-sm text-gray-600">
      Page {{ meta.current_page }} sur {{ meta.last_page }}
      ({{ meta.total }} résultats)
    </p>
    <div class="flex gap-1">
      <button
        v-for="page in pages"
        :key="page"
        @click="$emit('change', page)"
        :class="[
          'px-3 py-1 rounded-lg text-sm font-medium transition-colors',
          page === meta.current_page
            ? 'bg-primary-600 text-white'
            : 'border border-gray-300 text-gray-600 hover:bg-gray-50'
        ]"
      >{{ page }}</button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({ meta: Object })
defineEmits(['change'])

const pages = computed(() => {
  const arr = []
  for (let i = 1; i <= props.meta.last_page; i++) arr.push(i)
  return arr
})
</script>
