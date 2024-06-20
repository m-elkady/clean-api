import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'http://jobleads.localhost', // Replace with your API URL
  headers: {
    'Content-Type': 'application/json',
  },
});

export default {
  getUsers(params) {
    return apiClient.get('/user', {params});
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