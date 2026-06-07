<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import CartIcon from '@/components/CartIcon.vue'

const auth = useAuthStore()
const router = useRouter()
const abierto = ref(false)

const cerrarSesion = async () => {
  abierto.value = false
  await auth.logout()
  router.push('/')
}
</script>

<template>
  <header class="navbar">
    <div class="navbar-inner">
      <RouterLink to="/" class="brand" @click="abierto = false">
        <span class="brand-logo">🛍️</span>
        <span class="brand-text">Tienda</span>
      </RouterLink>

      <button
        class="hamburguesa"
        :class="{ activa: abierto }"
        aria-label="Menú"
        @click="abierto = !abierto"
      >
        <span></span><span></span><span></span>
      </button>

      <nav class="nav-links" :class="{ abierto }" @click="abierto = false">
        <RouterLink to="/" class="link">Inicio</RouterLink>
        <RouterLink to="/catalogo" class="link">Catálogo</RouterLink>
        <RouterLink v-if="!auth.isAuthenticated" to="/login" class="link">Login</RouterLink>
        <RouterLink v-if="!auth.isAuthenticated" to="/register" class="link link-registro">Crear cuenta</RouterLink>
        <RouterLink v-if="auth.esStaff" to="/admin" class="link link-panel">Panel admin</RouterLink>
        <button v-if="auth.isAuthenticated" class="link link-logout" @click.stop="cerrarSesion">Cerrar sesión</button>
        <CartIcon />
      </nav>
    </div>
  </header>
</template>

<style scoped>
.navbar {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(255, 255, 255, 0.82);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--border);
}
.navbar-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0.85rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}
.brand {
  display: flex;
  align-items: center;
  gap: 0.55rem;
  font-weight: 800;
  font-size: 1.3rem;
}
.brand-logo {
  font-size: 1.5rem;
  filter: drop-shadow(0 4px 8px rgba(108, 99, 255, 0.35));
}
.brand-text {
  background: var(--gradient-soft);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  letter-spacing: -0.5px;
}
.nav-links {
  display: flex;
  align-items: center;
  gap: 0.4rem;
}
.link {
  padding: 0.5rem 0.9rem;
  border-radius: var(--radius-pill);
  color: var(--text-muted);
  font-weight: 600;
  font-size: 0.95rem;
  transition: var(--transition);
}
.link:hover {
  color: var(--primary);
  background: var(--primary-soft);
}
.link.router-link-active:not(.link-panel):not(.link-registro) {
  color: var(--primary);
}
/* Botón "Panel admin": morado sólido que se oscurece en hover */
.link-panel {
  background: #6C63FF;
  color: #fff;
  border: none;
  border-radius: 20px;
  padding: 0.4rem 1.2rem;
  transition: background 0.2s ease;
}
.link-panel:hover {
  background: #5a52d5;
  color: #fff;
}
/* Botón "Crear cuenta": contorno que se invierte al hover */
.link-registro {
  border: 2px solid #6C63FF;
  color: #6C63FF;
  background: transparent;
  border-radius: 20px;
  padding: 0.4rem 1.2rem;
}
.link-registro:hover {
  background: #6C63FF;
  color: #fff;
}
/* Botón "Cerrar sesión": contorno coral que se invierte en hover */
.link-logout {
  background: transparent;
  color: #FF6584;
  border: 2px solid #FF6584;
  border-radius: 20px;
  padding: 0.4rem 1.2rem;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.2s ease;
}
.link-logout:hover {
  background: #FF6584;
  color: #fff;
}

/* Hamburguesa móvil */
.hamburguesa {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.4rem;
}
.hamburguesa span {
  width: 24px;
  height: 2px;
  background: var(--dark);
  border-radius: 2px;
  transition: var(--transition);
}
.hamburguesa.activa span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.hamburguesa.activa span:nth-child(2) { opacity: 0; }
.hamburguesa.activa span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

@media (max-width: 720px) {
  .hamburguesa { display: flex; }
  .nav-links {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    flex-direction: column;
    align-items: stretch;
    gap: 0.3rem;
    padding: 1rem 1.5rem 1.5rem;
    background: #fff;
    border-bottom: 1px solid var(--border);
    box-shadow: var(--shadow);
    clip-path: inset(0 0 100% 0);
    opacity: 0;
    pointer-events: none;
    transition: var(--transition);
  }
  .nav-links.abierto {
    clip-path: inset(0 0 0 0);
    opacity: 1;
    pointer-events: auto;
  }
  .link { padding: 0.7rem 0.9rem; }
  .link-cta { text-align: center; }
}
</style>
