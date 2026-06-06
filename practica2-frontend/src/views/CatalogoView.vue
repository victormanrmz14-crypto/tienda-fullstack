<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useCarritoStore } from '@/stores/carrito'
import CartIcon from '@/components/CartIcon.vue'

const carrito = useCarritoStore()
const router  = useRouter()

const regresar = () => {
  if (window.history.length > 1) router.back()
  else router.push('/')
}

const categorias      = ref([])
const categoriaActiva = ref(null)
const productos       = ref([])
const cargando        = ref(false)
const busqueda        = ref('')

const cargarTodos = async () => {
  cargando.value = true
  try {
    const { data } = await axios.get('http://localhost:8000/api/productos')
    productos.value = data
  } finally {
    cargando.value = false
  }
}

onMounted(async () => {
  const { data } = await axios.get('http://localhost:8000/api/categorias')
  categorias.value = data.data
  await cargarTodos()
})

const filtrarPorCategoria = async (cat) => {
  categoriaActiva.value = cat
  cargando.value = true
  try {
    const { data } = await axios.get(`http://localhost:8000/api/categorias/${cat.id}/productos`)
    productos.value = data.data
  } finally {
    cargando.value = false
  }
}

const verTodos = async () => {
  categoriaActiva.value = null
  await cargarTodos()
}

const productosFiltrados = computed(() => {
  const q = busqueda.value.trim().toLowerCase()
  if (!q) return productos.value
  return productos.value.filter(p =>
    p.nombre.toLowerCase().includes(q) ||
    (p.descripcion || '').toLowerCase().includes(q)
  )
})

const formatoPrecio = (precio) =>
  new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(precio)
</script>

<template>
  <div class="catalogo-page">
    <nav class="navbar">
      <div class="nav-left">
        <button class="btn-volver" title="Regresar" @click="regresar">←</button>
        <RouterLink to="/" class="brand">🛒 Tienda Full-Stack</RouterLink>
      </div>
      <div class="nav-links">
        <RouterLink to="/">Inicio</RouterLink>
        <CartIcon />
      </div>
    </nav>

    <div class="catalogo">
    <header class="catalogo-header">
      <div>
        <h2>Catálogo</h2>
        <p class="subtitulo">Explora nuestros productos disponibles</p>
      </div>
      <div class="buscador">
        <input
          v-model="busqueda"
          type="search"
          placeholder="Buscar producto..."
        />
      </div>
    </header>

    <!-- Tabs de categorías -->
    <div class="tabs">
      <button :class="{ activo: categoriaActiva === null }" @click="verTodos">
        Todos
      </button>
      <button
        v-for="cat in categorias"
        :key="cat.id"
        :class="{ activo: categoriaActiva?.id === cat.id }"
        @click="filtrarPorCategoria(cat)"
      >
        {{ cat.nombre }}
      </button>
    </div>

    <!-- Estados -->
    <div v-if="cargando" class="estado">Cargando productos...</div>
    <div v-else-if="productosFiltrados.length === 0" class="estado">
      No se encontraron productos.
    </div>

    <!-- Grid de productos -->
    <div v-else class="productos-grid">
      <article
        v-for="producto in productosFiltrados"
        :key="producto.id"
        class="card"
      >
        <div class="card-imagen">
          <img
            v-if="producto.imagen_url"
            :src="producto.imagen_url"
            :alt="producto.nombre"
          />
          <div v-else class="placeholder">Sin imagen</div>
          <span
            v-if="producto.categoria"
            class="badge-categoria"
          >{{ producto.categoria.nombre }}</span>
        </div>

        <div class="card-cuerpo">
          <h3>{{ producto.nombre }}</h3>
          <p class="descripcion">
            {{ producto.descripcion || 'Sin descripción' }}
          </p>

          <div class="card-meta">
            <strong class="precio">{{ formatoPrecio(producto.precio) }}</strong>
            <span
              :class="['stock', producto.stock > 0 ? 'disponible' : 'agotado']"
            >
              {{ producto.stock > 0 ? `${producto.stock} en stock` : 'Agotado' }}
            </span>
          </div>

          <button
            class="btn-agregar"
            :disabled="producto.stock <= 0"
            @click="carrito.agregar(producto)"
          >
            {{ producto.stock > 0 ? 'Agregar al carrito' : 'No disponible' }}
            <span
              v-if="carrito.cantidadDeProducto(producto.id) > 0"
              class="contador"
            >{{ carrito.cantidadDeProducto(producto.id) }}</span>
          </button>
        </div>
      </article>
    </div>
    </div>
  </div>
</template>

<style scoped>
.catalogo-page { min-height: 100vh; }
.navbar {
  background: white;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  margin-bottom: 1rem;
}
.nav-left { display: flex; align-items: center; gap: 1rem; }
.brand { text-decoration: none; color: #42b883; font-weight: bold; font-size: 1.1rem; }
.nav-links { display: flex; gap: 1.5rem; align-items: center; }
.nav-links a { text-decoration: none; color: #333; font-size: 1rem; }
.nav-links a:hover { color: #42b883; }
.btn-volver {
  background: none;
  border: none;
  color: #42b883;
  font-size: 1.6rem;
  line-height: 1;
  cursor: pointer;
  padding: 0 0.4rem;
  border-radius: 6px;
}
.btn-volver:hover { background: #f0f0f0; }

.catalogo {
  max-width: 1100px;
  margin: 0 auto;
  padding: 1rem;
}
.catalogo-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.catalogo-header h2 { margin: 0; }
.subtitulo { margin: 0.3rem 0 0; color: #777; font-size: 0.9rem; }
.buscador input {
  padding: 0.6rem 1rem;
  border: 1px solid #ddd;
  border-radius: 999px;
  font-size: 0.95rem;
  min-width: 240px;
}

/* Tabs */
.tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 2rem;
}
.tabs button {
  padding: 0.5rem 1.2rem;
  border: 1px solid #ddd;
  background: white;
  border-radius: 999px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.15s;
}
.tabs button:hover { border-color: #42b883; color: #42b883; }
.tabs button.activo {
  background: #42b883;
  border-color: #42b883;
  color: white;
}

/* Estados */
.estado { text-align: center; padding: 3rem; color: #999; }

/* Grid */
.productos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 1.5rem;
}
.card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  display: flex;
  flex-direction: column;
  transition: transform 0.15s, box-shadow 0.15s;
}
.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}
.card-imagen {
  position: relative;
  aspect-ratio: 4 / 3;
  background: #f5f5f5;
}
.card-imagen img { width: 100%; height: 100%; object-fit: cover; }
.placeholder {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  color: #bbb; font-size: 0.85rem;
}
.badge-categoria {
  position: absolute;
  top: 0.6rem; left: 0.6rem;
  background: rgba(53,73,94,0.9);
  color: white;
  padding: 0.2rem 0.6rem;
  border-radius: 999px;
  font-size: 0.72rem;
}

.card-cuerpo {
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  flex: 1;
}
.card-cuerpo h3 { margin: 0; font-size: 1.05rem; }
.descripcion {
  margin: 0;
  color: #777;
  font-size: 0.85rem;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.card-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}
.precio { color: #2e7d32; font-size: 1.15rem; }
.stock { font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 999px; }
.stock.disponible { background: #e8f5e9; color: #2e7d32; }
.stock.agotado    { background: #fdecea; color: #c0392b; }

.btn-agregar {
  margin-top: 0.5rem;
  padding: 0.6rem;
  background: #42b883;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: background 0.15s;
}
.btn-agregar:hover:not(:disabled) { background: #369870; }
.btn-agregar:disabled { background: #ccc; cursor: not-allowed; }
.contador {
  background: rgba(255,255,255,0.3);
  border-radius: 999px;
  padding: 0 0.5rem;
  font-size: 0.8rem;
}
</style>
