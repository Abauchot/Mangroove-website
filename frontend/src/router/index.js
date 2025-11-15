import { createRouter, createWebHistory } from "vue-router";

const routes = [
  {
    path: "/",
    name: "Home",
    component: () => import("@/views/HomeView.vue"),
  },
  {
    path: "/creations",
    name: "Creations",
    component: () => import("@/views/CreationsView.vue"),
  },
  {
    path: "/reglement",
    name: "Reglement",
    component: () => import("@/views/ReglementView.vue"),
  },
  {
    path: "/jams",
    name: "Jams",
    component: () => import("@/views/JamsView.vue"),
  },
  {
    path: "/a-propos",
    name: "A Propos",
    component: () => import("@/views/AboutView.vue"),
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

export default router;
