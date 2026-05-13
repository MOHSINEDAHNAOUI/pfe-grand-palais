<template>
  <div class="flex items-center justify-center">
    <div class="animate-spin rounded-full border-4 border-gray-200 border-t-primary-600"
         :class="sizeClass" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({ size: { type: String, default: 'md' } })
const sizeClass = computed(() => ({
  sm: 'h-6 w-6',
  md: 'h-10 w-10',
  lg: 'h-16 w-16',
}[props.size]))
</script>
