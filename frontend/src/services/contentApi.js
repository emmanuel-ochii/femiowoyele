import http from './http';

export const contentApi = {
  async get(path, params = {}) {
    const { data } = await http.get(path, { params });
    return data;
  },

  async post(path, payload) {
    const { data } = await http.post(path, payload);
    return data;
  },
};
