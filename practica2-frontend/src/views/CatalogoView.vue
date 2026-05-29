<template>
  <div class="home">
    <nav class="navbar">
      <h1>🛒 Tienda Full-Stack</h1>
      <div class="nav-links">
        <RouterLink to="/catalogo">Catálogo</RouterLink>
        <RouterLink v-if="!auth.isAuthenticated" to="/login">Login</RouterLink>
        <RouterLink v-if="auth.isAuthenticated" to="/admin">Admin</RouterLink>
      </div>
    </nav>

    <section class="hero">
      <h2>Bienvenido a la Tienda</h2>
      <p>Encuentra los mejores productos al mejor precio.</p>
      <RouterLink to="/catalogo" class="btn-primary">Ver Catálogo</RouterLink>
    </section>

    <section class="ultimos-productos">
      <h3>Últimos Productos</h3>
      <div v-if="loading" class="loading">Cargando...</div>
      <div v-else class="productos-grid">
        <div
            v-for="producto in ultimos"
            :key="producto.id"
            class="producto-card"
        >
          <h4>{{ producto.nombre }}</h4>
          <p class="precio">${{ producto.precio }}</p>
          <RouterLink :to="`/catalogo/${producto.id}`">Ver detalle →</RouterLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const auth    = useAuthStore()
const loading = ref(true)
const ultimos = ref([])

onMounted(async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/productos')
    ultimos.value = res.data.slice(0, 3)
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.home { min-height: 100vh; background: #f0f2f5; }
.navbar {
  background: white;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.navbar h1 { margin: 0; color: #42b883; font-size: 1.3rem; }
.nav-links { display: flex; gap: 1.5rem; }
.nav-links a { text-decoration: none; color: #333; font-weight: 500; }
.nav-links a:hover { color: #42b883; }
.hero {
  text-align: center;
  padding: 4rem 2rem;
  background: linear-gradient(135deg, #42b883, #35495e);
  color: white;
}
.hero h2 { font-size: 2.5rem; margin: 0 0 1rem; }
.hero p  { font-size: 1.2rem; margin: 0 0 2rem; opacity: 0.9; }
.btn-primary {
  background: white;
  color: #42b883;
  padding: 0.8rem 2rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
}
.ultimos-productos { padding: 3rem 2rem; max-width: 1000px; margin: 0 auto; }
.ultimos-productos h3 { font-size: 1.5rem; margin-bottom: 1.5rem; }
.productos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
}
.producto-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.producto-card h4 { margin: 0 0 0.5rem; }
.precio { color: #42b883; font-weight: bold; font-size: 1.2rem; margin: 0 0 1rem; }
.producto-card a { color: #42b883; text-decoration: none; font-size: 0.9rem; }
.loading { text-align: center; color: #999; padding: 2rem; }
</style>