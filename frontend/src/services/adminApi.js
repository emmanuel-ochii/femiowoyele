import http from './http';

const filenameFromDisposition = (disposition) => {
  const match = disposition?.match(/filename="?([^"]+)"?/i);
  return match?.[1] || null;
};

export const adminApi = {
  async login(payload) {
    const { data } = await http.post('/auth/login', payload);
    return data.data;
  },

  async me() {
    const { data } = await http.get('/auth/me');
    return data.data;
  },

  async logout() {
    await http.post('/auth/logout');
  },

  async overview() {
    const { data } = await http.get('/admin/overview');
    return data;
  },

  async list(resource, params = {}) {
    const { data } = await http.get(`/admin/${resource}`, { params });
    return data;
  },

  async create(resource, payload) {
    const { data } = await http.post(`/admin/${resource}`, payload);
    return data.data;
  },

  async update(resource, id, payload) {
    const { data } = await http.patch(`/admin/${resource}/${id}`, payload);
    return data.data;
  },

  async destroy(resource, id) {
    const { data } = await http.delete(`/admin/${resource}/${id}`);
    return data.data;
  },

  async download(endpoint) {
    const response = await http.get(endpoint, { responseType: 'blob' });
    return {
      blob: response.data,
      filename: filenameFromDisposition(response.headers['content-disposition']) || 'download.xls',
    };
  },
};
