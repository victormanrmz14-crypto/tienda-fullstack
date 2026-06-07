<template>
  <div class="detalle-page">
    <AppNavbar />

    <div class="content">
      <button class="btn-volver" @click="router.back()">← Volver</button>

      <div v-if="loading" class="detalle-card cargando">
        <div class="skeleton-img"></div>
        <div class="skeleton-info">
          <div class="skeleton-line w-60"></div>
          <div class="skeleton-line w-90"></div>
          <div class="skeleton-line w-40"></div>
        </div>
      </div>

      <div v-else-if="!producto" class="estado">
        <span class="estado-icono">😕</span>
        <h3>Producto no encontrado</h3>
        <RouterLink to="/catalogo" class="btn-primary">Ir al catálogo</RouterLink>
      </div>

      <div v-else class="detalle-card">
        <div class="galeria">
          <img v-if="producto.imagen_url" :src="producto.imagen_url" :alt="producto.nombre" />
          <div v-else class="placeholder">🖼️</div>
        </div>

        <div class="info">
          <span v-if="producto.categoria" class="badge-cat">{{ producto.categoria.nombre }}</span>
          <h1>{{ producto.nombre }}</h1>
          <p class="descripcion">{{ producto.descripcion || 'Sin descripción' }}</p>

          <div class="precio-bloque">
            <span class="precio">{{ formato(producto.precio) }}</span>
            <span class="stock" :class="producto.stock > 0 ? 'ok' : 'agotado'">
              {{ producto.stock > 0 ? `${producto.stock} disponibles` : 'Agotado' }}
            </span>
          </div>

          <div class="meta">
            <div class="meta-item">
              <span class="meta-label">Agregado el</span>
              <span>{{ new Date(producto.created_at).toLocaleDateString('es-MX') }}</span>
            </div>
          </div>

          <button
            class="btn-agregar"
            :class="{ agregado }"
            :disabled="producto.stock <= 0"
            @click="agregarAlCarrito"
          >
            <template v-if="agregado">✓ ¡Agregado al carrito!</template>
            <template v-else>{{ producto.stock > 0 ? '🛒 Agregar al carrito' : 'No disponible' }}</template>
          </button>

          <RouterLink to="/carrito" class="ir-carrito">Ver mi carrito →</RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useCarritoStore } from '@/stores/carrito'
import AppNavbar from '@/components/AppNavbar.vue'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'
const props   = defineProps({ id: String })
const router  = useRouter()
const carrito = useCarritoStore()
const loading = ref(true)
const producto = ref(null)
const agregado = ref(false)
let agregadoTimer = null

const formato = (precio) =>
  new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(precio)

const agregarAlCarrito = () => {
  carrito.agregar(producto.value)
  agregado.value = true
  clearTimeout(agregadoTimer)
  agregadoTimer = setTimeout(() => (agregado.value = false), 1500)
}

onMounted(async () => {
  try {
    const res = await axios.get(`${API_URL}/api/productos/${props.id}`)
    producto.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.detalle-page { min-height: 100vh; background: var(--bg); }
.content { max-width: 1000px; margin: 0 auto; padding: 2rem 1.5rem 4rem; }
.btn-volver {
  background: #fff;
  border: 1px solid var(--border);
  color: var(--primary);
  font-size: 0.92rem;
  font-weight: 700;
  cursor: pointer;
  margin-bottom: 1.5rem;
  padding: 0.55rem 1.1rem;
  border-radius: var(--radius-pill);
  box-shadow: var(--shadow-sm);
  transition: var(--transition);
}
.btn-volver:hover { background: var(--primary); color: #fff; transform: translateX(-3px); }

.detalle-card {
  background: #fff;
  border-radius: var(--radius-lg);
  border: 1px solid var(--border);
  box-shadow: var(--shadow);
  overflow: hidden;
  display: grid;
  grid-template-columns: 1fr 1fr;
}
.galeria {
  aspect-ratio: 1 / 1;
  background: linear-gradient(135deg, #f1f0ff, #ffe9ee);
  display: flex;
  align-items: center;
  justify-content: center;
}
.galeria img { width: 100%; height: 100%; object-fit: cover; }
.placeholder { font-size: 4rem; opacity: 0.4; }

.info { padding: 2.5rem; display: flex; flex-direction: column; }
.badge-cat {
  align-self: flex-start;
  background: var(--secondary-soft);
  color: var(--secondary-dark);
  padding: 0.3rem 0.9rem;
  border-radius: var(--radius-pill);
  font-size: 0.78rem;
  font-weight: 700;
  margin-bottom: 1rem;
}
.info h1 { margin: 0 0 1rem; font-size: 2rem; font-weight: 800; letter-spacing: -0.5px; }
.descripcion { color: var(--text-muted); line-height: 1.7; margin: 0 0 1.8rem; }
.precio-bloque {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.precio { font-size: 2.2rem; font-weight: 800; color: var(--primary); }
.stock {
  padding: 0.3rem 0.9rem;
  border-radius: var(--radius-pill);
  font-size: 0.82rem;
  font-weight: 700;
}
.stock.ok { background: var(--success-soft); color: #276749; }
.stock.agotado { background: var(--danger-soft); color: #c53030; }
.meta {
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
  padding: 1rem 0;
  margin-bottom: 1.8rem;
}
.meta-item { display: flex; justify-content: space-between; font-size: 0.9rem; }
.meta-label { color: var(--text-muted); }

.btn-agregar {
  padding: 1rem;
  background: var(--gradient-soft);
  color: #fff;
  border: none;
  border-radius: var(--radius-sm);
  font-size: 1.05rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow: var(--shadow-primary);
  transition: var(--transition);
  margin-top: auto;
}
.btn-agregar:hover:not(:disabled) { transform: translateY(-2px); filter: brightness(1.05); }
.btn-agregar:disabled { background: #e2e8f0; color: var(--text-soft); cursor: not-allowed; box-shadow: none; }
.btn-agregar.agregado { background: var(--success); box-shadow: 0 10px 28px rgba(72, 187, 120, 0.4); animation: pop 0.4s ease; }
.ir-carrito { text-align: center; margin-top: 1rem; color: var(--secondary); font-weight: 700; transition: var(--transition); }
.ir-carrito:hover { color: var(--secondary-dark); }

/* Estado / skeletons */
.estado {
  text-align: center;
  padding: 4rem 2rem;
  background: #fff;
  border-radius: var(--radius);
  border: 1px solid var(--border);
}
.estado-icono { font-size: 3rem; display: block; margin-bottom: 0.5rem; }
.estado h3 { margin: 0 0 1.5rem; }
.btn-primary {
  display: inline-block;
  background: var(--gradient-soft);
  color: #fff;
  padding: 0.8rem 2rem;
  border-radius: var(--radius-pill);
  font-weight: 700;
  box-shadow: var(--shadow-primary);
}
.cargando { grid-template-columns: 1fr 1fr; }
.skeleton-img {
  aspect-ratio: 1 / 1;
  background: linear-gradient(100deg, #eef0f5 30%, #f7f8fc 50%, #eef0f5 70%);
  background-size: 200% 100%;
  animation: gradientShift 1.4s ease infinite;
}
.skeleton-info { padding: 2.5rem; display: flex; flex-direction: column; gap: 1rem; }
.skeleton-line {
  height: 22px;
  border-radius: 6px;
  background: linear-gradient(100deg, #eef0f5 30%, #f7f8fc 50%, #eef0f5 70%);
  background-size: 200% 100%;
  animation: gradientShift 1.4s ease infinite;
}
.w-40 { width: 40%; } .w-60 { width: 60%; } .w-90 { width: 90%; }

@media (max-width: 720px) {
  .detalle-card, .cargando { grid-template-columns: 1fr; }
  .info { padding: 1.8rem; }
}
</style>
