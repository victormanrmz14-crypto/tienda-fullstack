import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/HomeView.vue'),
    },
    {
      path: '/catalogo',
      name: 'catalogo',
      component: () => import('@/views/CatalogoView.vue'),
    },
    {
      path: '/catalogo/:id',
      name: 'producto-detalle',
      component: () => import('@/views/ProductoDetalle.vue'),
      props: true,
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/LoginView.vue'),
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/RegisterView.vue'),
    },
    {
      path: '/logout',
      name: 'Logout',
      component: () => import('@/views/LogoutView.vue'),
    },
    {
      path: '/admin',
      component: () => import('@/layouts/AdminLayout.vue'),
      meta: { requiresAuth: true, roles: ['admin', 'editor'] },
      children: [
        {
          path: '',
          name: 'admin-dashboard',
          component: () => import('@/views/admin/Dashboard.vue'),
        },
        {
          path: 'productos',
          name: 'admin-productos',
          component: () => import('@/views/admin/Productos.vue'),
        },
      ],
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('@/views/NotFound.vue'),
    },
    {
      path: '/carrito',
      name: 'carrito',
      component: () => import('@/views/CartView.vue'),
    },
    {
      path: '/pedidos/:id/confirmacion',
      name: 'ConfirmacionPedido',
      component: () => import('@/views/ConfirmacionPedido.vue'),
      meta: { requiresAuth: true }
    },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  if (auth.token && !auth.user) {
    await auth.fetchUser()
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { path: '/login', query: { redirect: to.fullPath } }
  }

  // Control por rol: el cliente no puede entrar a rutas de staff (ej. /admin)
  if (to.meta.roles && !to.meta.roles.includes(auth.rol)) {
    return { path: '/' }
  }
})

export default router