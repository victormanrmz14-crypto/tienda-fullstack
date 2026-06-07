<template>
  <div class="admin-layout">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ abierto: sidebarAbierto }">
      <div class="sidebar-brand">
        <span class="logo">🛍️</span>
        <span class="logo-text">Tienda<small>Admin</small></span>
      </div>

      <nav class="sidebar-nav">
        <p class="nav-label">Principal</p>
        <RouterLink to="/admin" class="nav-item" @click="sidebarAbierto = false">
          <span class="nav-icon">📊</span> Dashboard
        </RouterLink>
        <RouterLink to="/admin/productos" class="nav-item" @click="sidebarAbierto = false">
          <span class="nav-icon">📦</span> Productos
        </RouterLink>

        <p class="nav-label">Tienda</p>
        <RouterLink to="/catalogo" class="nav-item" @click="sidebarAbierto = false">
          <span class="nav-icon">🛍️</span> Ver tienda
        </RouterLink>
        <RouterLink to="/" class="nav-item" @click="sidebarAbierto = false">
          <span class="nav-icon">🏠</span> Inicio
        </RouterLink>
      </nav>

      <div class="sidebar-card">
        <span class="sidebar-card-icon">💡</span>
        <p>Gestiona tu catálogo y recibe pedidos en tiempo real.</p>
      </div>
    </aside>

    <!-- Overlay móvil -->
    <div v-if="sidebarAbierto" class="overlay" @click="sidebarAbierto = false"></div>

    <!-- Contenido -->
    <div class="main">
      <header class="topbar">
        <div class="topbar-left">
          <button class="btn-menu" aria-label="Menú" @click="sidebarAbierto = !sidebarAbierto">☰</button>
          <nav class="breadcrumbs">
            <RouterLink to="/admin">🏠 Panel</RouterLink>
            <span class="sep">/</span>
            <span class="actual">{{ tituloActual }}</span>
          </nav>
        </div>

        <div class="topbar-right">
          <div class="user-menu">
            <button class="avatar-btn" @click="menuAbierto = !menuAbierto">
              <span class="avatar">{{ iniciales }}</span>
              <span class="avatar-name">
                <strong>{{ auth.user?.name || 'Usuario' }}</strong>
                <small>{{ auth.user?.rol }}</small>
              </span>
              <span class="chevron" :class="{ rotado: menuAbierto }">▾</span>
            </button>

            <Transition name="dropdown">
              <div v-if="menuAbierto" class="dropdown">
                <div class="dropdown-head">
                  <span class="avatar grande">{{ iniciales }}</span>
                  <div>
                    <strong>{{ auth.user?.name }}</strong>
                    <p>{{ auth.user?.email }}</p>
                    <span class="badge-rol">{{ auth.user?.rol }}</span>
                  </div>
                </div>
                <RouterLink to="/catalogo" class="dropdown-item" @click="menuAbierto = false">
                  🛍️ Ver tienda
                </RouterLink>
                <button class="dropdown-item logout" @click="handleLogout">
                  🚪 Cerrar sesión
                </button>
              </div>
            </Transition>
          </div>
        </div>
      </header>

      <main class="main-content">
        <RouterView />
      </main>
    </div>

    <!-- Backdrop para cerrar el menú del usuario -->
    <div v-if="menuAbierto" class="menu-backdrop" @click="menuAbierto = false"></div>

    <AdminNotificaciones v-if="auth.user?.rol === 'admin'" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminNotificaciones from '@/components/AdminNotificaciones.vue'

const auth   = useAuthStore()
const router = useRouter()
const route  = useRoute()

const sidebarAbierto = ref(false)
const menuAbierto    = ref(false)

const TITULOS = {
  'admin-dashboard': 'Dashboard',
  'admin-productos': 'Productos',
}
const tituloActual = computed(() => TITULOS[route.name] || 'Panel')

const iniciales = computed(() => {
  const nombre = auth.user?.name?.trim() || 'U'
  return nombre
    .split(' ')
    .slice(0, 2)
    .map((p) => p.charAt(0).toUpperCase())
    .join('')
})

const handleLogout = () => {
  menuAbierto.value = false
  router.push('/logout')
}
</script>

<style scoped>
.admin-layout { display: flex; min-height: 100vh; background: var(--bg); }

/* ===== Sidebar ===== */
.sidebar {
  width: 250px;
  background: var(--gradient-dark);
  color: rgba(255, 255, 255, 0.85);
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  padding: 1.5rem 1.2rem;
  position: fixed;
  top: 0; left: 0;
  height: 100vh;
  z-index: 60;
}
.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.3rem 0.5rem 1.2rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.logo { font-size: 1.7rem; filter: drop-shadow(0 4px 8px rgba(108, 99, 255, 0.5)); }
.logo-text { display: flex; flex-direction: column; line-height: 1.1; font-weight: 800; font-size: 1.2rem; color: #fff; }
.logo-text small { font-size: 0.68rem; font-weight: 600; color: var(--primary-light); letter-spacing: 1px; text-transform: uppercase; }

.sidebar-nav { display: flex; flex-direction: column; gap: 0.25rem; flex: 1; }
.nav-label {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: rgba(255, 255, 255, 0.4);
  margin: 1rem 0.6rem 0.4rem;
  font-weight: 700;
}
.nav-label:first-child { margin-top: 0; }
.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: rgba(255, 255, 255, 0.8);
  padding: 0.75rem 0.9rem;
  border-radius: var(--radius-sm);
  font-size: 0.95rem;
  font-weight: 600;
  transition: var(--transition);
}
.nav-icon { font-size: 1.1rem; width: 22px; text-align: center; }
.nav-item:hover { background: rgba(255, 255, 255, 0.08); color: #fff; transform: translateX(3px); }
.nav-item.router-link-exact-active {
  background: var(--gradient-soft);
  color: #fff;
  box-shadow: var(--shadow-primary);
}
.sidebar-card {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: var(--radius-sm);
  padding: 1rem;
  text-align: center;
}
.sidebar-card-icon { font-size: 1.5rem; }
.sidebar-card p { margin: 0.5rem 0 0; font-size: 0.8rem; color: rgba(255, 255, 255, 0.6); line-height: 1.5; }

.overlay {
  position: fixed;
  inset: 0;
  background: rgba(45, 55, 72, 0.5);
  z-index: 55;
  display: none;
}

/* ===== Main ===== */
.main {
  flex: 1;
  margin-left: 250px;
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.topbar {
  position: sticky;
  top: 0;
  z-index: 50;
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--border);
  padding: 0.85rem 1.8rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}
.topbar-left { display: flex; align-items: center; gap: 1rem; }
.btn-menu {
  display: none;
  background: none;
  border: none;
  font-size: 1.4rem;
  cursor: pointer;
  color: var(--dark);
}
.breadcrumbs { display: flex; align-items: center; gap: 0.5rem; font-size: 0.95rem; }
.breadcrumbs a { color: var(--text-muted); font-weight: 600; transition: var(--transition); }
.breadcrumbs a:hover { color: var(--primary); }
.sep { color: var(--text-soft); }
.actual { color: var(--text); font-weight: 700; }

/* ===== Avatar + dropdown ===== */
.user-menu { position: relative; z-index: 70; }
.avatar-btn {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius-pill);
  padding: 0.35rem 0.8rem 0.35rem 0.35rem;
  cursor: pointer;
  transition: var(--transition);
}
.avatar-btn:hover { box-shadow: var(--shadow-sm); border-color: var(--primary-light); }
.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--gradient-soft);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.9rem;
  flex-shrink: 0;
}
.avatar.grande { width: 48px; height: 48px; font-size: 1.1rem; }
.avatar-name { display: flex; flex-direction: column; line-height: 1.15; text-align: left; }
.avatar-name strong { font-size: 0.88rem; }
.avatar-name small { font-size: 0.72rem; color: var(--text-muted); text-transform: capitalize; }
.chevron { font-size: 0.7rem; color: var(--text-muted); transition: var(--transition); }
.chevron.rotado { transform: rotate(180deg); }

.dropdown {
  position: absolute;
  top: calc(100% + 0.6rem);
  right: 0;
  width: 270px;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-lg);
  padding: 0.5rem;
  z-index: 80;
}
.dropdown-head {
  display: flex;
  gap: 0.8rem;
  padding: 0.8rem;
  border-bottom: 1px solid var(--border);
  margin-bottom: 0.4rem;
}
.dropdown-head strong { font-size: 0.95rem; }
.dropdown-head p { margin: 0.15rem 0 0.4rem; font-size: 0.8rem; color: var(--text-muted); word-break: break-all; }
.badge-rol {
  display: inline-block;
  background: var(--primary-soft);
  color: var(--primary-dark);
  padding: 0.15rem 0.6rem;
  border-radius: var(--radius-pill);
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
}
.dropdown-item {
  display: block;
  width: 100%;
  text-align: left;
  padding: 0.7rem 0.8rem;
  border: none;
  background: none;
  border-radius: var(--radius-sm);
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text);
  cursor: pointer;
  transition: var(--transition);
}
.dropdown-item:hover { background: var(--light); }
.dropdown-item.logout { color: var(--danger); }
.dropdown-item.logout:hover { background: var(--danger-soft); }

.dropdown-enter-active, .dropdown-leave-active { transition: all 0.18s ease; }
.dropdown-enter-from, .dropdown-leave-to { opacity: 0; transform: translateY(-8px); }
.menu-backdrop { position: fixed; inset: 0; z-index: 65; }

.main-content { padding: 2rem 1.8rem; flex: 1; }

@media (max-width: 900px) {
  .sidebar { transform: translateX(-100%); transition: var(--transition); }
  .sidebar.abierto { transform: translateX(0); box-shadow: var(--shadow-lg); }
  .overlay { display: block; }
  .main { margin-left: 0; }
  .btn-menu { display: block; }
  .avatar-name { display: none; }
}
</style>
