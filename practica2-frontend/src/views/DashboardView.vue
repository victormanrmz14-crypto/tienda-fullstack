<template>
  <div class="dashboard-container">
    <nav class="navbar">
      <h1>🛒 Tienda Full-Stack</h1>
      <div class="user-info">
        <span>👤 {{ auth.user?.name || 'Usuario' }}</span>
        <button @click="handleLogout" :disabled="loading">
          {{ loading ? 'Saliendo...' : 'Cerrar sesión' }}
        </button>
      </div>
    </nav>

    <main class="content">
      <div class="welcome-card">
        <h2>¡Bienvenido, {{ auth.user?.name }}! 🎉</h2>
        <p>Has iniciado sesión correctamente.</p>
        <div class="info-grid">
          <div class="info-item">
            <span class="label">Email</span>
            <span class="value">{{ auth.user?.email }}</span>
          </div>
          <div class="info-item">
            <span class="label">Token activo</span>
            <span class="value token">{{ auth.token?.slice(0, 20) }}...</span>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth    = useAuthStore()
const router  = useRouter()
const loading = ref(false)

const handleLogout = async () => {
  loading.value = true
  try {
    await auth.logout()
    router.push('/login')
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  background: #f0f2f5;
}
.navbar {
  background: white;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.navbar h1 { margin: 0; font-size: 1.3rem; color: #42b883; }
.user-info { display: flex; align-items: center; gap: 1rem; }
.user-info span { font-weight: 500; }
button {
  padding: 0.5rem 1rem;
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.9rem;
}
button:disabled { opacity: 0.6; cursor: not-allowed; }
.content { padding: 2rem; max-width: 800px; margin: 0 auto; }
.welcome-card {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.welcome-card h2 { margin: 0 0 0.5rem; }
.welcome-card p  { color: #666; margin: 0 0 1.5rem; }
.info-grid { display: flex; flex-direction: column; gap: 1rem; }
.info-item {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
}
.label { font-size: 0.8rem; color: #999; text-transform: uppercase; }
.value { font-weight: 500; }
.token { font-family: monospace; font-size: 0.9rem; color: #42b883; }
</style>