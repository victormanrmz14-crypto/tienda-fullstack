<template>
  <div class="catalogo">
    <nav class="navbar">
      <RouterLink to="/">🛒 Tienda Full-Stack</RouterLink>
      <div class="nav-links">
        <RouterLink to="/catalogo">Catálogo</RouterLink>
        <RouterLink v-if="!auth.isAuthenticated" to="/login">Login</RouterLink>
        <RouterLink v-if="auth.isAuthenticated" to="/admin">Admin</RouterLink>
        <CartIcon />
      </div>
    </nav>

    <div class="content">
      <div class="header">
        <h2>Catálogo de Productos</h2>
        <input
            v-model="busqueda"
            type="text"
            placeholder="🔍 Buscar producto..."
            class="buscador"
        />
      </div>

      <div v-if="loading" class="loading">Cargando productos...</div>

      <div v-else-if="productosFiltrados.length === 0" class="empty">
        No se encontraron productos.
      </div>

      <div v-else class="productos-grid">
        <div
            v-for="producto in productosFiltrados"
            :key="producto.id"
            class="producto-card"
        >
          <img
              v-if="producto.imagen_url"
              :src="producto.imagen_url"
              :alt="producto.nombre"
              class="producto-imagen"
          />
          <div v-else class="producto-imagen-placeholder">📦</div>

          <div class="card-body">
            <h4>{{ producto.nombre }}</h4>
            <p class="descripcion">{{ producto.descripcion || 'Sin descripción' }}</p>
            <div class="card-footer">
              <span class="precio">${{ producto.precio }}</span>
              <span class="stock">Stock: {{ producto.stock }}</span>
            </div>
          </div>

          <button class="btn-agregar" @click="carrito.agregar(producto)">
            <template v-if="carrito.cantidadDeProducto(producto.id) > 0">
              En carrito ({{ carrito.cantidadDeProducto(producto.id) }})
            </template>
            <template v-else>
              🛒 Agregar al carrito
            </template>
          </button>

          <RouterLink :to="`/catalogo/${producto.id}`" class="btn-detalle">
            Ver detalle
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import { useCarritoStore } from '@/stores/carrito'
import CartIcon from '@/components/CartIcon.vue'

const auth      = useAuthStore()
const carrito   = useCarritoStore()
const loading   = ref(true)
const productos  = ref([])
const busqueda   = ref('')

const productosFiltrados = computed(() =>
    productos.value.filter(p =>
        p.nombre.toLowerCase().includes(busqueda.value.toLowerCase())
    )
)

onMounted(async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/productos')
    productos.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.catalogo { min-height: 100vh; background: #f0f2f5; }
.navbar {
  background: white;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.navbar a { text-decoration: none; color: #42b883; font-weight: bold; font-size: 1.1rem; }
.nav-links { display: flex; gap: 1.5rem; align-items: center; }
.nav-links a { color: #333; font-size: 1rem; }
.nav-links a:hover { color: #42b883; }
.content { padding: 2rem; max-width: 1100px; margin: 0 auto; }
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}
.header h2 { margin: 0; }
.buscador {
  padding: 0.6rem 1rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  width: 260px;
}
.productos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 1.5rem;
}
.producto-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}
.producto-imagen {
  width: 100%;
  height: 180px;
  object-fit: cover;
}
.producto-imagen-placeholder {
  width: 100%;
  height: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f0f2f5;
  font-size: 3rem;
}
.card-body { padding: 1.5rem; flex: 1; }
.card-body h4 { margin: 0 0 0.5rem; }
.descripcion { color: #666; font-size: 0.9rem; margin: 0 0 1rem; }
.card-footer { display: flex; justify-content: space-between; align-items: center; }
.precio { color: #42b883; font-weight: bold; font-size: 1.2rem; }
.stock { color: #999; font-size: 0.85rem; }
.btn-agregar {
  display: block;
  width: 100%;
  padding: 0.7rem;
  background: #35495e;
  color: white;
  border: none;
  cursor: pointer;
  font-size: 0.95rem;
}
.btn-agregar:hover { background: #2c3e50; }
.btn-detalle {
  display: block;
  text-align: center;
  padding: 0.8rem;
  background: #42b883;
  color: white;
  text-decoration: none;
  font-weight: 500;
}
.btn-detalle:hover { background: #35a070; }
.loading, .empty { text-align: center; padding: 3rem; color: #999; }
</style>