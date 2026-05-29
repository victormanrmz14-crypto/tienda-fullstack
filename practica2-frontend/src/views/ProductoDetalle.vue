<template>
  <div class="detalle">
    <nav class="navbar">
      <RouterLink to="/">🛒 Tienda Full-Stack</RouterLink>
      <div class="nav-links">
        <RouterLink to="/catalogo">Catálogo</RouterLink>
        <RouterLink v-if="!auth.isAuthenticated" to="/login">Login</RouterLink>
        <RouterLink v-if="auth.isAuthenticated" to="/admin">Admin</RouterLink>
      </div>
    </nav>

    <div class="content">
      <button class="btn-volver" @click="router.back()">← Volver al catálogo</button>

      <div v-if="loading" class="loading">Cargando producto...</div>

      <div v-else-if="!producto" class="error">
        Producto no encontrado.
      </div>

      <div v-else class="producto-card">
        <div class="producto-info">
          <h2>{{ producto.nombre }}</h2>
          <p class="descripcion">{{ producto.descripcion || 'Sin descripción' }}</p>
          <div class="detalles">
            <div class="detalle-item">
              <span class="label">Precio</span>
              <span class="precio">${{ producto.precio }}</span>
            </div>
            <div class="detalle-item">
              <span class="label">Stock disponible</span>
              <span class="stock">{{ producto.stock }} unidades</span>
            </div>
            <div class="detalle-item">
              <span class="label">Agregado el</span>
              <span>{{ new Date(producto.created_at).toLocaleDateString() }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const props   = defineProps({ id: String })
const auth    = useAuthStore()
const router  = useRouter()
const loading = ref(true)
const producto = ref(null)

onMounted(async () => {
  try {
    const res = await axios.get(`http://localhost:8000/api/productos/${props.id}`)
    producto.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.detalle { min-height: 100vh; background: #f0f2f5; }
.navbar {
  background: white;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.navbar a { text-decoration: none; color: #42b883; font-weight: bold; font-size: 1.1rem; }
.nav-links { display: flex; gap: 1.5rem; }
.nav-links a { color: #333; font-size: 1rem; }
.nav-links a:hover { color: #42b883; }
.content { padding: 2rem; max-width: 700px; margin: 0 auto; }
.btn-volver {
  background: none;
  border: none;
  color: #42b883;
  font-size: 1rem;
  cursor: pointer;
  margin-bottom: 1.5rem;
  padding: 0;
}
.btn-volver:hover { text-decoration: underline; }
.producto-card {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.producto-info h2 { margin: 0 0 1rem; font-size: 1.8rem; }
.descripcion { color: #666; margin: 0 0 2rem; line-height: 1.6; }
.detalles { display: flex; flex-direction: column; gap: 1rem; }
.detalle-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
}
.label { color: #999; font-size: 0.85rem; text-transform: uppercase; }
.precio { color: #42b883; font-weight: bold; font-size: 1.3rem; }
.stock { font-weight: 500; }
.loading, .error { text-align: center; padding: 3rem; color: #999; }
</style>