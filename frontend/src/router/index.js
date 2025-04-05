import { createRouter, createWebHistory } from "vue-router";
import store from "@/store";
import LoginView from "../views/LoginView";
import RegisterView from "../views/RegisterView";
import EventList from "../views/EventList"

const routes = [
  { path: "/login", component: LoginView,
    meta: { requiresGuest: true }
  },
  { path: "/register", component: RegisterView,
    meta: { requiresGuest: true }
  },
  {
    path: "/events",
    component: EventList,
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation Guard
router.beforeEach((to, from, next) => {
  const isAuthenticated = store.getters.isAuthenticated;
  if (to.meta.requiresAuth && !isAuthenticated) {
    next("/login");
  } else if (to.meta.requiresGuest && isAuthenticated) {
    next("/events");
  } else {
    next();
  }
});

export default router;
