<script setup>
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { useCarritoStore } from '@/stores/carrito'
import { useAuthStore } from '@/stores/auth'
import { useFiltros } from '@/composables/useFiltros'
import FiltrosPanel from '@/components/FiltrosPanel.vue'
import PaginacionNav from '@/components/PaginacionNav.vue'
import CartIcon from '@/components/CartIcon.vue'

const carrito = useCarritoStore()
const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

const { filtros, limpiar } = useFiltros()

const resultado = ref({ data: [], meta: {} })
const cargando = ref(false)

const cargarProductos = async () => {
  cargando.value = true
  try {
    const { data } = await axios.get('http://localhost:8000/api/productos', {
      params: {
        busqueda: filtros.busqueda || undefined,
        categoria_id: filtros.categoria_id || undefined,
        precio_min: filtros.precio_min || undefined,
        precio_max: filtros.precio_max || undefined,
        orden: filtros.orden,
        dir: filtros.dir,
        page: filtros.pagina,
      },
    })
    resultado.value = data
  } finally {
    cargando.value = false
  }
}

// Recarga cuando cambia la URL (filtros sincronizados → URL → fetch)
watch(() => route.query, cargarProductos, { immediate: true })

const regresar = () => {
  if (window.history.length > 1) router.back()
  else router.push('/')
}

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
        <RouterLink v-if="!auth.isAuthenticated" to="/login">Login</RouterLink>
        <RouterLink v-if="auth.isAuthenticated" to="/admin">Admin</RouterLink>
        <CartIcon />
      </div>
    </nav>

    <div class="catalogo">
      <header class="catalogo-header">
        <div>
          <h2>Catálogo</h2>
          <p class="subtitulo">Explora nuestros productos disponibles</p>
        </div>
      </header>

      <div class="layout">
        <FiltrosPanel v-model="filtros" @limpiar="limpiar" />

        <section class="contenido">
          <div v-if="cargando" class="estado">Cargando productos...</div>
          <div v-else-if="resultado.data.length === 0" class="estado">
            No se encontraron productos.
          </div>

          <template v-else>
            <div class="productos-grid">
              <article
                v-for="producto in resultado.data"
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
                  <span v-if="producto.categoria" class="badge-categoria">
                    {{ producto.categoria.nombre }}
                  </span>
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

                  <RouterLink :to="`/catalogo/${producto.id}`" class="ver-detalle">
                    Ver detalle →
                  </RouterLink>
                </div>
              </article>
            </div>

            <PaginacionNav
              :meta="resultado.meta"
              @cambio-pagina="filtros.pagina = $event"
            />
          </template>
        </section>
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
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem;
}
.catalogo-header {
  margin-bottom: 1.5rem;
}
.catalogo-header h2 { margin: 0; }
.subtitulo { margin: 0.3rem 0 0; color: #777; font-size: 0.9rem; }

/* Layout de dos columnas */
.layout {
  display: grid;
  grid-template-columns: 240px 1fr;
  gap: 1.5rem;
  align-items: start;
}

/* Estados */
.estado { text-align: center; padding: 3rem; color: #999; }

/* Grid */
.productos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
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
  background: #42b883;
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
.ver-detalle {
  text-align: center;
  font-size: 0.85rem;
  color: #42b883;
  text-decoration: none;
  margin-top: 0.25rem;
}
.ver-detalle:hover { text-decoration: underline; }

@media (max-width: 720px) {
  .layout { grid-template-columns: 1fr; }
}
</style>
