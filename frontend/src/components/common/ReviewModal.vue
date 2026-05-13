<template>
  <Modal :model-value="modelValue" @update:modelValue="$emit('update:modelValue', $event)" title="Laisser un avis">
    <div class="space-y-6">
      <div v-if="reservation" class="p-4 bg-slate-50 rounded-xl flex items-center gap-4">
        <div class="w-12 h-12 bg-accent/20 rounded-lg flex items-center justify-center text-xl">🏨</div>
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Votre séjour</p>
          <p class="text-sm font-bold text-primary">{{ reservation.room?.room_type?.name }} — {{ reservation.reference }}</p>
        </div>
      </div>

      <form @submit.prevent="submitReview" class="space-y-6">
        <!-- Main Rating -->
        <div class="flex flex-col items-center py-4">
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Note Globale</p>
          <div class="flex gap-2">
            <button v-for="i in 5" :key="i" type="button" 
                    @click="form.rating = i"
                    class="text-3xl transition-transform hover:scale-110"
                    :class="i <= form.rating ? 'grayscale-0' : 'grayscale opacity-30'">
              ⭐
            </button>
          </div>
        </div>

        <!-- Sub Ratings -->
        <div class="grid grid-cols-1 gap-4">
          <div v-for="field in subRatings" :key="field.key" class="flex items-center justify-between p-3 border border-slate-100 rounded-xl">
            <span class="text-[11px] font-bold text-primary uppercase tracking-widest">{{ field.label }}</span>
            <div class="flex gap-1">
              <button v-for="i in 5" :key="i" type="button"
                      @click="form[field.key] = i"
                      class="w-6 h-6 flex items-center justify-center rounded-full transition-all text-[10px] font-bold"
                      :class="i <= form[field.key] ? 'bg-accent text-white' : 'bg-slate-100 text-slate-400'">
                {{ i }}
              </button>
            </div>
          </div>
        </div>

        <!-- Comment -->
        <div class="space-y-2">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Commentaire</label>
          <textarea v-model="form.comment" 
                    rows="3"
                    placeholder="Comment s'est passé votre séjour ?"
                    class="w-full bg-slate-50 border-none rounded-xl p-4 text-sm focus:ring-1 focus:ring-accent outline-none transition-all resize-none"></textarea>
        </div>

        <div v-if="error" class="p-3 bg-red-50 text-red-500 text-xs rounded-lg">{{ error }}</div>

        <button type="submit" 
                :disabled="loading || form.rating === 0"
                class="w-full py-4 bg-primary text-white rounded-xl font-bold text-[10px] uppercase tracking-[0.2em] hover:bg-accent transition-all duration-300 shadow-lg disabled:opacity-50">
          <span v-if="loading">Envoi en cours...</span>
          <span v-else>Publier mon avis</span>
        </button>
      </form>
    </div>
  </Modal>
</template>

<script setup>
import { ref, reactive } from 'vue'
import Modal from './Modal.vue'
import api from '@/api/axios'

const props = defineProps({
  modelValue: Boolean,
  reservation: Object
})

const emit = defineEmits(['update:modelValue', 'success'])

const loading = ref(false)
const error = ref(null)

const form = reactive({
  rating: 0,
  cleanliness_rating: 0,
  comfort_rating: 0,
  service_rating: 0,
  comment: ''
})

const subRatings = [
  { key: 'cleanliness_rating', label: 'Propreté' },
  { key: 'comfort_rating',     label: 'Confort' },
  { key: 'service_rating',     label: 'Service' }
]

const submitReview = async () => {
  loading.value = true
  error.value = null
  try {
    await api.post(`/reservations/${props.reservation.id}/review`, form)
    emit('success')
    emit('update:modelValue', false)
    // Reset form
    Object.assign(form, {
      rating: 0, cleanliness_rating: 0, comfort_rating: 0, service_rating: 0, comment: ''
    })
  } catch (err) {
    error.value = err.response?.data?.message || 'Une erreur est survenue'
  } finally {
    loading.value = false
  }
}
</script>
