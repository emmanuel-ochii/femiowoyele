import { defineStore } from 'pinia';
import { contentApi } from '../services/contentApi';

export const useContentStore = defineStore('content', {
  state: () => ({
    cache: {},
    loading: false,
    error: null,
  }),
  actions: {
    async fetch(path, params = {}) {
      const key = `${path}:${JSON.stringify(params)}`;

      if (this.cache[key]) {
        return this.cache[key];
      }

      this.loading = true;
      this.error = null;

      try {
        const payload = await contentApi.get(path, params);
        this.cache[key] = payload;
        return payload;
      } catch (error) {
        this.error = error;
        throw error;
      } finally {
        this.loading = false;
      }
    },
  },
});
