// frontend/src/stores/booking.js
import { defineStore } from 'pinia'
import api from '@/api/axios'

export const useBookingStore = defineStore('booking', {
  state: () => ({
    searchParams: {
      check_in: '',
      check_out: '',
      adults: 1,
      children: 0,
      room_type_id: null,
      max_price: null,
    },
    availableRooms: [],
    selectedRoom: null,
    selectedServices: [],
    promoCode: '',
    promoDiscount: 0,
    loading: false,
  }),

  getters: {
    nights: (state) => {
      if (!state.searchParams.check_in || !state.searchParams.check_out) return 0
      const d1 = new Date(state.searchParams.check_in)
      const d2 = new Date(state.searchParams.check_out)
      return Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24))
    },
    servicesTotal: (state) => state.selectedServices.reduce((sum, s) => sum + (s.price * s.quantity), 0),
    roomTotal: (state) => {
      const price = parseFloat(state.selectedRoom?.period_price ?? state.selectedRoom?.price_override ?? state.selectedRoom?.room_type?.base_price ?? 0)
      const nights = state.searchParams.check_in && state.searchParams.check_out 
        ? Math.ceil((new Date(state.searchParams.check_out) - new Date(state.searchParams.check_in)) / (1000 * 60 * 60 * 24)) 
        : 0
      return price * (nights > 0 ? nights : 1)
    },
    grandTotal: (state) => {
      const nights = state.searchParams.check_in && state.searchParams.check_out 
        ? Math.ceil((new Date(state.searchParams.check_out) - new Date(state.searchParams.check_in)) / (1000 * 60 * 60 * 24)) 
        : 0
      const basePrice = parseFloat(state.selectedRoom?.period_price ?? state.selectedRoom?.price_override ?? state.selectedRoom?.room_type?.base_price ?? 0)
      const subtotal = (basePrice * (nights > 0 ? nights : 1)) + 
        state.selectedServices.reduce((sum, s) => sum + (s.price * s.quantity), 0)
      return subtotal - state.promoDiscount
    },
  },

  actions: {
    async searchRooms() {
      this.loading = true
      try {
        const { data } = await api.get('/rooms/search', { params: this.searchParams })
        this.availableRooms = data.data
      } finally {
        this.loading = false
      }
    },

    selectRoom(room) {
      this.selectedRoom = room
      this.selectedServices = []
    },

    toggleService(service, quantity = 1) {
      const idx = this.selectedServices.findIndex(s => s.id === service.id)
      if (idx >= 0) {
        this.selectedServices.splice(idx, 1)
      } else {
        this.selectedServices.push({ ...service, quantity })
      }
    },

    async applyPromo(code) {
      const { data } = await api.post('/reservations/check-promo', { code })
      this.promoCode = code
      const subtotal = this.roomTotal + this.servicesTotal
      this.promoDiscount = data.type === 'percentage' 
        ? subtotal * (data.value / 100) 
        : data.value
      return data
    },

    async createReservation() {
      this.loading = true
      try {
        const { data } = await api.post('/reservations', {
          ...this.searchParams,
          room_id: this.selectedRoom.id,
          services: this.selectedServices.map(s => ({
            id: s.id, quantity: s.quantity, price: s.price
          })),
          promo_code: this.promoCode || undefined,
        })
        return data
      } finally {
        this.loading = false
      }
    },

    resetBooking() {
      this.selectedRoom = null
      this.selectedServices = []
      this.promoCode = ''
      this.promoDiscount = 0
    },
  },
})
