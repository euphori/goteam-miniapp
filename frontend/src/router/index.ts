import { createRouter, createWebHistory } from 'vue-router'
import Boards from '@/views/Boards.vue'
import BoardView from '@/views/BoardView.vue'
const routes = [
  { path: '/', component: Boards },
  { path: '/boards/:id', component: BoardView, props: true },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
