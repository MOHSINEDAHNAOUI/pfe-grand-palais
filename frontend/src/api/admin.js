import api from './axios'

export const adminApi = {
  getDashboard:    ()            => api.get('/admin/dashboard'),
  getRevenueChart: (params = {}) => api.get('/admin/dashboard/revenue-chart', { params }),
  getTodayRes:     ()            => api.get('/admin/dashboard/today'),

  getOccupancyReport: (params = {}) => api.get('/admin/reports/occupancy', { params }),
  getRevenueReport:   (params = {}) => api.get('/admin/reports/revenue', { params }),
  exportReport:       (type, params = {}) => api.get(`/admin/reports/export/${type}`, {
    params, responseType: 'blob'
  }),

  getUsers:    (params = {}) => api.get('/admin/users', { params }),
  getUserById: (id)          => api.get(`/admin/users/${id}`),
  updateUser:  (id, data)    => api.put(`/admin/users/${id}`, data),
  toggleUser:  (id)          => api.patch(`/admin/users/${id}/toggle-status`),
}
