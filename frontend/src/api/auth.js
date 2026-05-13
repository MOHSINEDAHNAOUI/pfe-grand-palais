// frontend/src/api/auth.js
import api from './axios'

export const authApi = {

  // ─── Authentification ─────────────────────────────────────

  /**
   * Inscription d'un nouvel utilisateur
   * @param {{ name, email, password, password_confirmation, phone }} data
   */
  register: (data) =>
    api.post('/auth/register', data),

  /**
   * Connexion avec email + mot de passe
   * @param {{ email, password }} credentials
   */
  login: (credentials) =>
    api.post('/auth/login', credentials),

  /**
   * Déconnexion (révoque le token courant)
   */
  logout: () =>
    api.post('/auth/logout'),

  /**
   * Récupérer l'utilisateur connecté avec ses rôles
   */
  me: () =>
    api.get('/auth/me'),

  // ─── OAuth Social ──────────────────────────────────────────

  /**
   * URL de redirection vers le provider OAuth
   * @param {'google'|'facebook'} provider
   */
  socialRedirectUrl: (provider) =>
    `${import.meta.env.VITE_API_URL?.replace('/api', '') ?? 'http://localhost:8000'}/api/auth/${provider}/redirect`,

  // ─── Profil ───────────────────────────────────────────────

  /**
   * Mettre à jour le profil utilisateur
   * @param {{ name, email, phone, city, address, country }} data
   */
  updateProfile: (data) =>
    api.put('/auth/profile', data),

  /**
   * Changer le mot de passe
   * @param {{ current_password, password, password_confirmation }} data
   */
  updatePassword: (data) =>
    api.put('/auth/password', data),

  /**
   * Uploader un nouvel avatar
   * @param {File} file
   */
  uploadAvatar: (file) => {
    const form = new FormData()
    form.append('avatar', file)
    return api.post('/auth/avatar', form, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
  },

  // ─── Vérification email ───────────────────────────────────

  /**
   * Renvoyer l'email de vérification
   */
  resendVerification: () =>
    api.post('/auth/email/resend'),

  // ─── Mot de passe oublié ──────────────────────────────────

  /**
   * Demander un lien de réinitialisation
   * @param {{ email }} data
   */
  forgotPassword: (data) =>
    api.post('/auth/forgot-password', data),

  /**
   * Réinitialiser le mot de passe avec le token reçu par email
   * @param {{ token, email, password, password_confirmation }} data
   */
  resetPassword: (data) =>
    api.post('/auth/reset-password', data),

  // ─── Notifications ────────────────────────────────────────

  /**
   * Récupérer les notifications de l'utilisateur connecté
   */
  getNotifications: () =>
    api.get('/auth/notifications'),

  /**
   * Marquer une notification comme lue
   * @param {string} id
   */
  markNotificationRead: (id) =>
    api.patch(`/auth/notifications/${id}/read`),

  /**
   * Marquer toutes les notifications comme lues
   */
  markAllNotificationsRead: () =>
    api.patch('/auth/notifications/read-all'),
}
