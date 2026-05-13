<template>
  <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
    <div :class="['text-4xl p-3 rounded-xl', bgClass]">{{ icon }}</div>
    <div>
      <p class="text-sm text-gray-500 font-medium">{{ title }}</p>
      <p class="text-2xl font-bold text-gray-800 mt-1">
        <span v-if="value !== undefined && value !== null">{{ value }}</span>
        <span v-else class="text-gray-400 text-lg">—</span>
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: String,
  value: [String, Number],
  icon:  String,
  color: { type: String, default: 'blue' },
})

const bgClass = computed(() => ({
  blue:   'bg-blue-100',
  green:  'bg-green-100',
  orange: 'bg-orange-100',
  purple: 'bg-purple-100',
  red:    'bg-red-100',
}[props.color]))
</script>
