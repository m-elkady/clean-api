import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'http://api.clean-api.me',
  headers: {
    'Content-Type': 'application/json',
  },
});

let isRefreshing = false;
let refreshSubscribers = [];

function subscribeTokenRefresh(callback) {
  refreshSubscribers.push(callback);
}

function onRefreshed(token) {
  refreshSubscribers.forEach(callback => callback(token));
  refreshSubscribers = [];
}

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

// Response interceptor - handle 401 errors and refresh tokens
apiClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    // If 401 and not already retrying
    if (error.response?.status === 401 && !originalRequest._retry) {
      // If we have a refresh token, try to refresh
      const refreshToken = localStorage.getItem('refresh_token');

      if (refreshToken && !isRefreshing) {
        isRefreshing = true;
        originalRequest._retry = true;

        try {
          const response = await axios.post('http://api.clean-api.me/auth/refresh', {
            refresh_token: refreshToken,
          });

          const { access_token, refresh_token: newRefreshToken } = response.data.data;

          localStorage.setItem('auth_token', access_token);
          localStorage.setItem('refresh_token', newRefreshToken);

          onRefreshed(access_token);
          isRefreshing = false;

          // Retry original request with new token
          originalRequest.headers.Authorization = `Bearer ${access_token}`;
          return apiClient(originalRequest);
        } catch (refreshError) {
          // Refresh failed, logout and redirect to login
          localStorage.removeItem('auth_token');
          localStorage.removeItem('refresh_token');
          localStorage.removeItem('user');
          window.location.href = '/login';
          return Promise.reject(refreshError);
        }
      } else if (refreshToken && isRefreshing) {
        // Wait for token refresh to complete
        return new Promise((resolve) => {
          subscribeTokenRefresh((token) => {
            originalRequest.headers.Authorization = `Bearer ${token}`;
            resolve(apiClient(originalRequest));
          });
        });
      } else {
        // No refresh token, logout and redirect to login
        localStorage.removeItem('auth_token');
        localStorage.removeItem('refresh_token');
        localStorage.removeItem('user');
        window.location.href = '/login';
      }
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
  refreshToken(refreshToken) {
    return apiClient.post('/auth/refresh', { refresh_token: refreshToken });
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
