<script setup>
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { useCarritoStore } from '@/stores/carrito'
import { useFiltros } from '@/composables/useFiltros'
import AppNavbar from '@/components/AppNavbar.vue'
import FiltrosPanel from '@/components/FiltrosPanel.vue'
import PaginacionNav from '@/components/PaginacionNav.vue'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'
const carrito = useCarritoStore()
const route = useRoute()
const router = useRouter()

const { filtros, limpiar } = useFiltros()

const resultado = ref({ data: [], meta: {} })
const cargando = ref(false)
const recienAgregado = ref(null)
let agregadoTimer = null

const cargarProductos = async () => {
  cargando.value = true
  try {
    const { data } = await axios.get(`${API_URL}/api/productos`, {
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

const agregar = (producto) => {
  carrito.agregar(producto)
  recienAgregado.value = producto.id
  clearTimeout(agregadoTimer)
  agregadoTimer = setTimeout(() => (recienAgregado.value = null), 1100)
}

const regresar = () => {
  if (window.history.length > 1) router.back()
  else router.push('/')
}

const formatoPrecio = (precio) =>
  new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(precio)
</script>

<template>
  <div class="catalogo-page">
    <AppNavbar />

    <div class="catalogo">
      <header class="catalogo-header">
        <button class="btn-volver" title="Regresar" @click="regresar">←</button>
        <div>
          <h1>Catálogo</h1>
          <p class="subtitulo">Explora nuestros productos disponibles</p>
        </div>
        <span v-if="resultado.meta?.total" class="total-pill">
          {{ resultado.meta.total }} productos
        </span>
      </header>

      <div class="layout">
        <FiltrosPanel v-model="filtros" @limpiar="limpiar" />

        <section class="contenido">
          <div v-if="cargando" class="productos-grid">
            <div v-for="n in 6" :key="n" class="skeleton-card"></div>
          </div>

          <div v-else-if="resultado.data.length === 0" class="estado">
            <span class="estado-icono">🔍</span>
            <h3>No se encontraron productos</h3>
            <p>Prueba ajustando los filtros de búsqueda.</p>
            <button class="btn-limpiar-estado" @click="limpiar">Limpiar filtros</button>
          </div>

          <template v-else>
            <div class="productos-grid">
              <article
                v-for="(producto, i) in resultado.data"
                :key="producto.id"
                class="card"
                :style="{ animationDelay: `${i * 50}ms` }"
              >
                <div class="card-imagen">
                  <img
                    v-if="producto.imagen_url"
                    :src="producto.imagen_url"
                    :alt="producto.nombre"
                  />
                  <div v-else class="placeholder">🖼️</div>
                  <span v-if="producto.categoria" class="badge-categoria">
                    {{ producto.categoria.nombre }}
                  </span>
                  <span
                    :class="['badge-stock', producto.stock > 0 ? 'disponible' : 'agotado']"
                  >
                    {{ producto.stock > 0 ? `${producto.stock} en stock` : 'Agotado' }}
                  </span>
                </div>

                <div class="card-cuerpo">
                  <h3>{{ producto.nombre }}</h3>
                  <p class="descripcion">
                    {{ producto.descripcion || 'Sin descripción' }}
                  </p>

                  <div class="card-meta">
                    <strong class="precio">{{ formatoPrecio(producto.precio) }}</strong>
                    <RouterLink :to="`/catalogo/${producto.id}`" class="ver-detalle">
                      Ver detalle
                    </RouterLink>
                  </div>

                  <button
                    class="btn-agregar"
                    :class="{ agregado: recienAgregado === producto.id }"
                    :disabled="producto.stock <= 0"
                    @click="agregar(producto)"
                  >
                    <span class="btn-label">
                      <template v-if="recienAgregado === producto.id">✓ ¡Agregado!</template>
                      <template v-else>{{ producto.stock > 0 ? '🛒 Agregar al carrito' : 'No disponible' }}</template>
                    </span>
                    <span
                      v-if="carrito.cantidadDeProducto(producto.id) > 0"
                      class="contador"
                    >{{ carrito.cantidadDeProducto(producto.id) }}</span>
                  </button>
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
.catalogo-page { min-height: 100vh; background: var(--bg); }

.catalogo {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1.5rem 4rem;
}
.catalogo-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
}
.catalogo-header h1 { margin: 0; font-size: 2rem; font-weight: 800; letter-spacing: -0.5px; }
.subtitulo { margin: 0.2rem 0 0; color: var(--text-muted); font-size: 0.95rem; }
.total-pill {
  margin-left: auto;
  background: var(--primary-soft);
  color: var(--primary-dark);
  padding: 0.45rem 1rem;
  border-radius: var(--radius-pill);
  font-size: 0.85rem;
  font-weight: 700;
}
.btn-volver {
  width: 42px;
  height: 42px;
  flex-shrink: 0;
  background: #fff;
  border: 1px solid var(--border);
  color: var(--primary);
  font-size: 1.4rem;
  line-height: 1;
  cursor: pointer;
  border-radius: 50%;
  box-shadow: var(--shadow-sm);
  transition: var(--transition);
}
.btn-volver:hover { background: var(--primary); color: #fff; transform: translateX(-2px); }

/* Layout de dos columnas */
.layout {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: 1.8rem;
  align-items: start;
}

/* Estado vacío */
.estado {
  text-align: center;
  padding: 4rem 2rem;
  background: #fff;
  border-radius: var(--radius);
  border: 1px dashed var(--border);
}
.estado-icono { font-size: 3rem; display: block; margin-bottom: 0.5rem; }
.estado h3 { margin: 0 0 0.4rem; }
.estado p { margin: 0 0 1.5rem; color: var(--text-muted); }
.btn-limpiar-estado {
  background: var(--gradient-soft);
  color: #fff;
  border: none;
  padding: 0.7rem 1.6rem;
  border-radius: var(--radius-pill);
  font-weight: 700;
  cursor: pointer;
  box-shadow: var(--shadow-primary);
  transition: var(--transition);
}
.btn-limpiar-estado:hover { transform: translateY(-2px); }

/* Grid */
.productos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 1.5rem;
}
.card {
  background: #fff;
  border-radius: var(--radius);
  overflow: hidden;
  border: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
  display: flex;
  flex-direction: column;
  transition: var(--transition);
  animation: floatUp 0.5s ease both;
}
.card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-lg);
  border-color: transparent;
}
.card-imagen {
  position: relative;
  aspect-ratio: 4 / 3;
  background: linear-gradient(135deg, #f1f0ff, #ffe9ee);
  overflow: hidden;
}
.card-imagen img { width: 100%; height: 100%; object-fit: cover; transition: var(--transition); }
.card:hover .card-imagen img { transform: scale(1.06); }
.placeholder {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  font-size: 2.5rem; opacity: 0.4;
}
.badge-categoria {
  position: absolute;
  top: 0.7rem; left: 0.7rem;
  background: rgba(255, 255, 255, 0.92);
  color: var(--primary-dark);
  padding: 0.25rem 0.7rem;
  border-radius: var(--radius-pill);
  font-size: 0.72rem;
  font-weight: 700;
  backdrop-filter: blur(4px);
}
.badge-stock {
  position: absolute;
  top: 0.7rem; right: 0.7rem;
  padding: 0.22rem 0.6rem;
  border-radius: var(--radius-pill);
  font-size: 0.68rem;
  font-weight: 700;
  backdrop-filter: blur(4px);
}
.badge-stock.disponible { background: rgba(72, 187, 120, 0.92); color: #fff; }
.badge-stock.agotado    { background: rgba(245, 101, 101, 0.92); color: #fff; }

.card-cuerpo {
  padding: 1.1rem 1.2rem 1.3rem;
  display: flex;
  flex-direction: column;
  gap: 0.55rem;
  flex: 1;
}
.card-cuerpo h3 { margin: 0; font-size: 1.05rem; }
.descripcion {
  margin: 0;
  color: var(--text-muted);
  font-size: 0.85rem;
  line-height: 1.45;
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
.precio { color: var(--primary); font-size: 1.25rem; font-weight: 800; }
.ver-detalle {
  font-size: 0.85rem;
  color: var(--secondary);
  font-weight: 700;
  transition: var(--transition);
}
.ver-detalle:hover { color: var(--secondary-dark); }

.btn-agregar {
  margin-top: 0.4rem;
  padding: 0.7rem;
  background: var(--gradient-soft);
  color: #fff;
  border: none;
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  box-shadow: var(--shadow-primary);
  transition: var(--transition);
  overflow: hidden;
}
.btn-agregar:hover:not(:disabled) { transform: translateY(-2px); filter: brightness(1.05); }
.btn-agregar:active:not(:disabled) { transform: translateY(0) scale(0.98); }
.btn-agregar:disabled {
  background: #e2e8f0;
  color: var(--text-soft);
  cursor: not-allowed;
  box-shadow: none;
}
.btn-agregar.agregado { background: var(--success); box-shadow: 0 10px 28px rgba(72, 187, 120, 0.4); }
.btn-agregar .btn-label { display: inline-flex; align-items: center; gap: 0.35rem; }
.btn-agregar.agregado .btn-label { animation: pop 0.4s ease; }
.contador {
  background: rgba(255, 255, 255, 0.28);
  border-radius: var(--radius-pill);
  padding: 0.05rem 0.55rem;
  font-size: 0.8rem;
  font-weight: 700;
}

/* Skeletons */
.skeleton-card {
  height: 340px;
  border-radius: var(--radius);
  background: linear-gradient(100deg, #eef0f5 30%, #f7f8fc 50%, #eef0f5 70%);
  background-size: 200% 100%;
  animation: gradientShift 1.4s ease infinite;
}

@media (max-width: 820px) {
  .layout { grid-template-columns: 1fr; }
}
</style>
