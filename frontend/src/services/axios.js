import axios from 'axios';
import store from '@/store';
import router from '@/router';

const axiosInstance = axios.create({
  baseURL: process.env.VUE_APP_BASE_URL
});

axiosInstance.interceptors.request.use(
  (config) => {
    const token = store.getters.getToken;
    if (token) {
      config.headers["Authorization"] = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

axiosInstance.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response && error.response.status === 401) {
      await store.dispatch("logout");

      if (router.currentRoute.value.path !== "/login") {
        router.push("/login");
      }
    }
    return Promise.reject(error);
  }
);

export default axiosInstance;
