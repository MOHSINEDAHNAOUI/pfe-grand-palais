import api from './axios'

export const reservationsApi = {
  getMyReservations: (params = {}) => api.get('/reservations', { params }),
  getById:           (id)          => api.get(`/reservations/${id}`),
  create:            (data)        => api.post('/reservations', data),
  cancel:            (id, reason)  => api.post(`/reservations/${id}/cancel`, { reason }),
  checkPromo:        (code)        => api.post('/reservations/check-promo', { code }),
  addReview:         (id, data)    => api.post(`/reservations/${id}/review`, data),

  // Admin
  adminGetAll:  (params = {}) => api.get('/admin/reservations', { params }),
  adminConfirm: (id)          => api.post(`/admin/reservations/${id}/confirm`),
  adminCheckIn: (id)          => api.post(`/admin/reservations/${id}/check-in`),
  adminCheckOut:(id)          => api.post(`/admin/reservations/${id}/check-out`),
  adminNoShow:  (id)          => api.post(`/admin/reservations/${id}/no-show`),
}
