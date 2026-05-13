import api from './axios'

export const roomsApi = {
  getAll:    (params = {}) => api.get('/rooms', { params }),
  search:    (params)      => api.get('/rooms/search', { params }),
  getTypes:  ()            => api.get('/rooms/types'),
  getById:   (id)          => api.get(`/rooms/${id}`),
  getReviews:(id)          => api.get(`/rooms/${id}/reviews`),

  // Admin
  adminGetAll:    (params = {}) => api.get('/admin/rooms', { params }),
  adminCreate:    (data)        => api.post('/admin/rooms', data),
  adminUpdate:    (id, data)    => api.put(`/admin/rooms/${id}`, data),
  adminDelete:    (id)          => api.delete(`/admin/rooms/${id}`),
  adminSetStatus: (id, status)  => api.post(`/admin/rooms/${id}/status`, { status }),
}
