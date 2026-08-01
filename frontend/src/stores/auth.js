import { defineStore } from 'pinia';
import { adminApi } from '../services/adminApi';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('femi_admin_user') || 'null'),
    token: localStorage.getItem('femi_admin_token'),
  }),
  getters: {
    isAuthenticated: (state) => Boolean(state.token && state.user),
  },
  actions: {
    async login(credentials) {
      const session = await adminApi.login(credentials);
      this.user = session.user;
      this.token = session.token;
      localStorage.setItem('femi_admin_user', JSON.stringify(session.user));
      localStorage.setItem('femi_admin_token', session.token);
      return session;
    },

    async logout() {
      try {
        if (this.token) {
          await adminApi.logout();
        }
      } finally {
        this.user = null;
        this.token = null;
        localStorage.removeItem('femi_admin_user');
        localStorage.removeItem('femi_admin_token');
      }
    },
  },
});
