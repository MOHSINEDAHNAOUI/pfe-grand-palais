import { defineStore } from 'pinia'
import api from '@/api/axios'

export const useAdminStore = defineStore('admin', {
  state: () => ({
    stats:           null,
    revenueChart:    [],
    todayReservations: [],
    loading:         false,
  }),

  actions: {
    async fetchOverview() {
      this.loading = true
      try {
        const [statsRes, chartRes, todayRes] = await Promise.all([
          api.get('/admin/dashboard'),
          api.get('/admin/dashboard/revenue-chart'),
          api.get('/admin/dashboard/today'),
        ])
        this.stats             = statsRes.data
        this.revenueChart      = chartRes.data
        this.todayReservations = todayRes.data
      } finally {
        this.loading = false
      }
    },

    async exportReport(type, format = 'pdf', params = {}) {
      const response = await api.get(`/admin/reports/export/${type}`, {
        params: { format, ...params },
        responseType: 'blob',
      })
      const url  = URL.createObjectURL(new Blob([response.data]))
      const link = document.createElement('a')
      link.href  = url
      link.setAttribute('download', `${type}-report.${format}`)
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
    },
  },
})
