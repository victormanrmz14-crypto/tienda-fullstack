<template>
  <div class="admin-layout">
    <aside class="sidebar">
      <div class="sidebar-header">
        <h2>⚙️ Admin</h2>
        <p class="user-name">{{ auth.user?.name }}</p>
      </div>

      <nav class="sidebar-nav">
        <RouterLink to="/admin">Dashboard</RouterLink>
        <RouterLink to="/admin/productos">Productos</RouterLink>
        <RouterLink to="/catalogo">Ver tienda</RouterLink>
      </nav>

      <button class="btn-logout" @click="handleLogout">
        Cerrar sesión
      </button>
    </aside>

    <main class="main-content">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth   = useAuthStore()
const router = useRouter()

const handleLogout = async () => {
  await auth.logout()
  router.push('/login')
}
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
}
.sidebar {
  width: 240px;
  background: #35495e;
  color: white;
  display: flex;
  flex-direction: column;
  padding: 1.5rem;
  gap: 1rem;
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
}
.sidebar-header { border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem; }
.sidebar-header h2 { margin: 0 0 0.3rem; font-size: 1.2rem; }
.user-name { margin: 0; font-size: 0.85rem; opacity: 0.7; }
.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  flex: 1;
}
.sidebar-nav a {
  color: rgba(255,255,255,0.8);
  text-decoration: none;
  padding: 0.7rem 1rem;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: background 0.2s;
}
.sidebar-nav a:hover,
.sidebar-nav a.router-link-active {
  background: rgba(255,255,255,0.1);
  color: white;
}
.btn-logout {
  background: rgba(231,76,60,0.8);
  color: white;
  border: none;
  padding: 0.7rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.95rem;
}
.btn-logout:hover { background: #e74c3c; }
.main-content {
  margin-left: 240px;
  flex: 1;
  background: #f0f2f5;
  padding: 2rem;
}
</style>