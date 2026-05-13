<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="modelValue"
           class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
           @click.self="$emit('update:modelValue', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-auto">
          <div class="flex items-center justify-between p-5 border-b">
            <h3 class="text-lg font-bold text-gray-800">{{ title }}</h3>
            <button @click="$emit('update:modelValue', false)"
                    class="text-gray-400 hover:text-gray-600 text-2xl leading-none">×</button>
          </div>
          <div class="p-5">
            <slot />
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
defineProps({ modelValue: Boolean, title: String })
defineEmits(['update:modelValue'])
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
