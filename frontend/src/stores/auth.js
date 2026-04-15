import { defineStore } from 'pinia';
import api from '@/services/axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('auth_token') || null,
    refreshToken: localStorage.getItem('refresh_token') || null,
    user: JSON.parse(localStorage.getItem('user') || 'null'),
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
  },

  actions: {
    async login(email, password) {
      try {
        const response = await api.login(email, password);
        const data = response.data.data;

        if (data.access_token) {
          this.token = data.access_token;
          this.refreshToken = data.refresh_token;
          localStorage.setItem('auth_token', data.access_token);
          localStorage.setItem('refresh_token', data.refresh_token);

          // Fetch user info after login
          await this.fetchUser();
          return true;
        }
        return false;
      } catch (error) {
        console.error('Login failed:', error.response?.data?.errors || error.message);
        throw error;
      }
    },

    async logout() {
      try {
        await api.logout();
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        this.token = null;
        this.refreshToken = null;
        this.user = null;
        localStorage.removeItem('auth_token');
        localStorage.removeItem('refresh_token');
        localStorage.removeItem('user');
      }
    },

    async fetchUser() {
      try {
        const response = await api.me();
        const data = response.data;

        if (data.success && data.data) {
          this.user = data.data;
          localStorage.setItem('user', JSON.stringify(data.data));
        }
      } catch (error) {
        console.error('Failed to fetch user:', error);
        this.logout();
      }
    },

    async initAuth() {
      if (this.token) {
        await this.fetchUser();
      }
    },
  },
});
