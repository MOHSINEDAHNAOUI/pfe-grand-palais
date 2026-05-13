// frontend/src/stores/rooms.js
import { defineStore } from 'pinia'
import api from '@/api/axios'

export const useRoomsStore = defineStore('rooms', {
  state: () => ({
    rooms:        [],
    roomTypes:    [],
    amenities:    [],
    currentRoom:  null,
    loading:      false,
    error:        null,
    pagination: {
      current_page: 1,
      last_page:    1,
      total:        0,
      per_page:     12,
    },
  }),

  getters: {
    availableRooms: (state) =>
      state.rooms.filter(r => r.status === 'available'),

    roomsByType: (state) => (typeId) =>
      state.rooms.filter(r => r.room_type_id === typeId),

    getRoomById: (state) => (id) =>
      state.rooms.find(r => r.id === id) ?? null,

    hasNextPage: (state) =>
      state.pagination.current_page < state.pagination.last_page,
  },

  actions: {
    // ─── Rooms ───────────────────────────────────────────────

    async fetchRooms(params = {}) {
      this.loading = true
      this.error   = null
      try {
        const { data } = await api.get('/rooms', { params })
        this.rooms      = data.data
        this.pagination = {
          current_page: data.meta?.current_page ?? 1,
          last_page:    data.meta?.last_page    ?? 1,
          total:        data.meta?.total        ?? data.data.length,
          per_page:     data.meta?.per_page     ?? 12,
        }
      } catch (e) {
        this.error = e.response?.data?.message ?? 'Erreur lors du chargement des chambres'
      } finally {
        this.loading = false
      }
    },

    async fetchRoomById(id) {
      this.loading     = true
      this.error       = null
      this.currentRoom = null
      try {
        const { data }   = await api.get(`/rooms/${id}`)
        this.currentRoom = data
        return data
      } catch (e) {
        this.error = e.response?.data?.message ?? 'Chambre introuvable'
        return null
      } finally {
        this.loading = false
      }
    },

    async searchRooms(params = {}) {
      this.loading = true
      this.error   = null
      try {
        const { data } = await api.get('/rooms/search', { params })
        this.rooms     = data.data ?? data
        return this.rooms
      } catch (e) {
        this.error = e.response?.data?.message ?? 'Erreur lors de la recherche'
        return []
      } finally {
        this.loading = false
      }
    },

    // ─── Room Types ───────────────────────────────────────────

    async fetchRoomTypes() {
      if (this.roomTypes.length) return this.roomTypes // cache
      try {
        const { data }  = await api.get('/rooms/types')
        this.roomTypes  = data
        return data
      } catch (e) {
        this.error = e.response?.data?.message ?? 'Erreur types de chambres'
        return []
      }
    },

    // ─── Amenities ────────────────────────────────────────────

    async fetchAmenities() {
      if (this.amenities.length) return this.amenities // cache
      try {
        const { data } = await api.get('/amenities')
        this.amenities = data
        return data
      } catch (e) {
        return []
      }
    },

    // ─── Admin actions ────────────────────────────────────────

    async adminFetchRooms(params = {}) {
      this.loading = true
      try {
        const { data } = await api.get('/admin/rooms', { params })
        this.rooms      = data.data
        this.pagination = {
          current_page: data.meta?.current_page ?? 1,
          last_page:    data.meta?.last_page    ?? 1,
          total:        data.meta?.total        ?? data.data.length,
          per_page:     data.meta?.per_page     ?? 20,
        }
        return data
      } finally {
        this.loading = false
      }
    },

    async adminCreateRoom(formData) {
      const { data } = await api.post('/admin/rooms', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      this.rooms.unshift(data)
      return data
    },

    async adminUpdateRoom(id, formData) {
      const { data } = await api.put(`/admin/rooms/${id}`, formData)
      const idx = this.rooms.findIndex(r => r.id === id)
      if (idx !== -1) this.rooms[idx] = data
      return data
    },

    async adminDeleteRoom(id) {
      await api.delete(`/admin/rooms/${id}`)
      this.rooms = this.rooms.filter(r => r.id !== id)
    },

    async adminUpdateRoomStatus(id, status) {
      const { data } = await api.post(`/admin/rooms/${id}/status`, { status })
      const idx = this.rooms.findIndex(r => r.id === id)
      if (idx !== -1) this.rooms[idx].status = status
      return data
    },

    // ─── Helpers ──────────────────────────────────────────────

    clearCurrentRoom() {
      this.currentRoom = null
    },

    clearError() {
      this.error = null
    },
  },
})
