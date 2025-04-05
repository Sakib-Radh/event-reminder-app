import { createStore } from "vuex";

const store = createStore({
  state() {
    return {
      token: localStorage.getItem("token") || null,
      user: localStorage.getItem("user") ? JSON.parse(localStorage.getItem("user")) : null,
    };
  },
  mutations: {
    setToken(state, token) {
      state.token = token;
      localStorage.setItem("token", token);
    },
    setUser(state, user) {
      if (user) {
        state.user = user;
        localStorage.setItem("user", JSON.stringify(user));
      } else {
        console.warn("Attempted to store undefined user!");
      }
    },
    clearAuth(state) {
      state.token = null;
      state.user = null;
      localStorage.removeItem("token");
      localStorage.removeItem("user");
    },
  },
  actions: {
    login({ commit }, { token, user }) {
      commit("setToken", token);
      commit("setUser", user);
    },
    logout({ commit }) {
      commit("clearAuth");
    },
  },
  getters: {
    isAuthenticated(state) {
      return !!state.token;
    },
    getUser(state) {
      return state.user;
    },
    getToken(state) {
      return state.token;
    },
  },
});

export default store;
