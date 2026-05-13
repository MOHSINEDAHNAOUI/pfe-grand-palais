// frontend/src/stores/auth.js
import { defineStore } from 'pinia'
import api from '@/api/axios'
 
export const useAuthStore = defineStore('auth', {
  state: () => ({
    user:          null,
    token:         localStorage.getItem('token') || null,
    emailVerified: false,
    loading:       false,
  }),
 
  getters: {
    isLoggedIn:     (state) => !!state.token && !!state.user,
    isAdmin:        (state) => state.user?.roles?.some(r => r.name === 'admin'),
    isManager:      (state) => state.user?.roles?.some(r => ['admin', 'manager'].includes(r.name)),
    isReceptionist: (state) => state.user?.roles?.some(r => ['admin', 'manager', 'receptionist'].includes(r.name)),
  },
 
  actions: {
 
    async login(credentials) {
      const { data } = await api.post('/auth/login', credentials)
      this.token         = data.token
      this.user          = data.user
      this.emailVerified = data.email_verified ?? false
      localStorage.setItem('token', data.token)
      return data
    },
 
    async register(formData) {
      const { data } = await api.post('/auth/register', formData)
      this.token         = data.token
      this.user          = data.user
      this.emailVerified = false
      localStorage.setItem('token', data.token)
      return data
    },
 
    async fetchUser() {
      if (!this.token) return null
      try {
        const { data } = await api.get('/auth/me')
        // Handle both { user, email_verified } and direct user object
        this.user          = data.user ?? data
        this.emailVerified = data.email_verified ?? !!this.user?.email_verified_at
        return this.user
      } catch (e) {
        // Token invalid — clear everything
        this.token = null
        this.user  = null
        this.emailVerified = false
        localStorage.removeItem('token')
        return null
      }
    },
 
    async logout() {
      try {
        await api.post('/auth/logout')
      } catch (e) {
        // Token invalide — on continue quand même
      } finally {
        this.token         = null
        this.user          = null
        this.emailVerified = false
        localStorage.removeItem('token')
      }
    },

    async forgotPassword(email) {
      const { data } = await api.post('/auth/forgot-password', { email })
      return data
    },

    async resetPassword(formData) {
      const { data } = await api.post('/auth/reset-password', formData)
      return data
    },
  },
})
