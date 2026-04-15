import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'http://clean-api.localhost',
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor - add auth token
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor - handle 401 errors
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default {
  // Auth methods
  login(email, password) {
    return apiClient.post('/auth/login', { email, password });
  },
  logout() {
    return apiClient.post('/auth/logout');
  },
  me() {
    return apiClient.get('/auth/me');
  },

  // User methods
  getUsers(params) {
    return apiClient.get('/user', { params });
  },
  getUser(id) {
    return apiClient.get(`/user/${id}`);
  },
  createUser(data) {
    return apiClient.post('/user', data);
  },
  updateUser(id, data) {
    return apiClient.put(`/user/${id}`, data);
  },
  deleteUser(id) {
    return apiClient.delete(`/user/${id}`);
  },
};
