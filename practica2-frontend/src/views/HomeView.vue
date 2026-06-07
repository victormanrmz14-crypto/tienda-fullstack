<template>
  <div class="home">
    <AppNavbar />

    <!-- HERO -->
    <section class="hero">
      <div class="hero-bg"></div>
      <div class="hero-content">
        <span class="hero-eyebrow">✨ Nueva colección disponible</span>
        <h1>
          Encuentra los <span class="highlight">mejores productos</span><br />
          al mejor precio
        </h1>
        <p>Miles de productos seleccionados para ti, con envío rápido y pagos seguros.</p>
        <div class="hero-actions">
          <RouterLink to="/catalogo" class="btn btn-primary">Explorar catálogo</RouterLink>
          <RouterLink to="/register" class="btn btn-ghost">Crear cuenta</RouterLink>
        </div>
        <div class="hero-stats">
          <div><strong>+500</strong><span>Productos</span></div>
          <div><strong>24/7</strong><span>Atención</span></div>
          <div><strong>100%</strong><span>Seguro</span></div>
        </div>
      </div>
    </section>

    <!-- CATEGORÍAS -->
    <section class="seccion categorias-seccion">
      <div class="seccion-head">
        <h2>Explora por categoría</h2>
        <p>Encuentra justo lo que buscas</p>
      </div>
      <div class="categorias-grid">
        <RouterLink
          v-for="(cat, i) in categorias"
          :key="cat.id"
          :to="`/catalogo?categoria_id=${cat.id}`"
          class="categoria-card"
          :style="{ animationDelay: `${i * 60}ms` }"
        >
          <span class="categoria-icono">{{ iconoCategoria(cat.nombre, i) }}</span>
          <span class="categoria-nombre">{{ cat.nombre }}</span>
          <span class="categoria-flecha">→</span>
        </RouterLink>
        <RouterLink v-if="!categorias.length && !loading" to="/catalogo" class="categoria-card">
          <span class="categoria-icono">🛍️</span>
          <span class="categoria-nombre">Ver todo</span>
          <span class="categoria-flecha">→</span>
        </RouterLink>
      </div>
    </section>

    <!-- PRODUCTOS DESTACADOS -->
    <section class="seccion destacados-seccion">
      <div class="seccion-head">
        <h2>Productos destacados</h2>
        <RouterLink to="/catalogo" class="ver-todos">Ver todos →</RouterLink>
      </div>

      <div v-if="loading" class="destacados-grid">
        <div v-for="n in 4" :key="n" class="skeleton-card"></div>
      </div>

      <div v-else class="destacados-grid">
        <RouterLink
          v-for="(producto, i) in destacados"
          :key="producto.id"
          :to="`/catalogo/${producto.id}`"
          class="producto-card"
          :style="{ animationDelay: `${i * 70}ms` }"
        >
          <div class="producto-imagen">
            <img v-if="producto.imagen_url" :src="producto.imagen_url" :alt="producto.nombre" />
            <div v-else class="placeholder">🖼️</div>
            <span v-if="producto.categoria" class="badge-cat">{{ producto.categoria.nombre }}</span>
          </div>
          <div class="producto-body">
            <h3>{{ producto.nombre }}</h3>
            <p class="producto-desc">{{ producto.descripcion || 'Sin descripción' }}</p>
            <div class="producto-foot">
              <strong class="precio">{{ formatoPrecio(producto.precio) }}</strong>
              <span class="ver-detalle">Ver →</span>
            </div>
          </div>
        </RouterLink>
      </div>
    </section>

    <!-- CTA FINAL -->
    <section class="cta-final">
      <div class="cta-card">
        <h2>¿List@ para comprar?</h2>
        <p>Descubre nuestro catálogo completo y arma tu pedido en minutos.</p>
        <RouterLink to="/catalogo" class="btn btn-primary">Ir al catálogo</RouterLink>
      </div>
    </section>

    <AppFooter />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import AppNavbar from '@/components/AppNavbar.vue'
import AppFooter from '@/components/AppFooter.vue'

const API_URL  = import.meta.env.VITE_API_URL || 'http://localhost:8000'
const loading   = ref(true)
const destacados = ref([])
const categorias = ref([])

const ICONOS = {
  electrónica: '💻', electronica: '💻', tecnología: '📱', tecnologia: '📱',
  ropa: '👕', moda: '👗', hogar: '🏠', muebles: '🛋️',
  deportes: '⚽', deporte: '⚽', libros: '📚', juguetes: '🧸',
  belleza: '💄', salud: '💊', comida: '🍔', alimentos: '🛒',
  música: '🎧', musica: '🎧', accesorios: '👜', zapatos: '👟', calzado: '👟',
  herramientas: '🔧', mascotas: '🐾', jardín: '🌱', jardin: '🌱',
}
const ICONOS_FALLBACK = ['🛍️', '🎁', '⭐', '🏷️', '💎', '🛒', '✨', '📦']

const iconoCategoria = (nombre, i) => {
  const clave = (nombre || '').trim().toLowerCase()
  return ICONOS[clave] || ICONOS_FALLBACK[i % ICONOS_FALLBACK.length]
}

const formatoPrecio = (precio) =>
  new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(precio)

onMounted(async () => {
  try {
    const [resProd, resCat] = await Promise.allSettled([
      axios.get(`${API_URL}/api/productos`),
      axios.get(`${API_URL}/api/categorias`),
    ])
    if (resProd.status === 'fulfilled') {
      const data = resProd.value.data?.data ?? resProd.value.data ?? []
      destacados.value = data.slice(0, 4)
    }
    if (resCat.status === 'fulfilled') {
      categorias.value = (resCat.value.data?.data ?? resCat.value.data ?? []).slice(0, 6)
    }
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.home {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--bg);
}

/* ===== HERO ===== */
.hero {
  position: relative;
  overflow: hidden;
  padding: 5.5rem 1.5rem 6rem;
  color: #fff;
}
.hero-bg {
  position: absolute;
  inset: -20%;
  background: linear-gradient(120deg, #6C63FF, #8B85FF, #FF6584, #6C63FF);
  background-size: 300% 300%;
  animation: gradientShift 12s ease infinite;
  z-index: 0;
}
.hero-bg::after {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.18), transparent 45%),
              radial-gradient(circle at 10% 90%, rgba(255, 255, 255, 0.12), transparent 40%);
}
.hero-content {
  position: relative;
  z-index: 1;
  max-width: 760px;
  margin: 0 auto;
  text-align: center;
  animation: floatUp 0.7s ease both;
}
.hero-eyebrow {
  display: inline-block;
  padding: 0.4rem 1rem;
  background: rgba(255, 255, 255, 0.18);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: var(--radius-pill);
  font-size: 0.85rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  backdrop-filter: blur(4px);
}
.hero h1 {
  font-size: clamp(2.1rem, 5vw, 3.4rem);
  line-height: 1.12;
  margin: 0 0 1.2rem;
  font-weight: 800;
  letter-spacing: -1px;
}
.highlight {
  background: linear-gradient(#fff, #fff) bottom / 100% 36% no-repeat;
  -webkit-background-clip: text;
  background-clip: text;
  color: #2D3748;
  padding: 0 0.2rem;
}
.hero p {
  font-size: 1.15rem;
  margin: 0 0 2.2rem;
  opacity: 0.92;
}
.hero-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
  margin-bottom: 3rem;
}
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.85rem 2rem;
  border-radius: var(--radius-pill);
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: var(--transition);
  border: none;
}
.btn-primary {
  background: #fff;
  color: var(--primary);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}
.btn-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 16px 38px rgba(0, 0, 0, 0.28);
}
.btn-ghost {
  background: rgba(255, 255, 255, 0.12);
  color: #fff;
  border: 1.5px solid rgba(255, 255, 255, 0.55);
}
.btn-ghost:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-3px);
}
.hero-stats {
  display: flex;
  gap: 2.5rem;
  justify-content: center;
}
.hero-stats div {
  display: flex;
  flex-direction: column;
}
.hero-stats strong {
  font-size: 1.7rem;
  font-weight: 800;
}
.hero-stats span {
  font-size: 0.85rem;
  opacity: 0.85;
}

/* ===== SECCIONES ===== */
.seccion {
  max-width: 1200px;
  margin: 0 auto;
  padding: 4rem 1.5rem;
  width: 100%;
}
.seccion-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 2.2rem;
}
.seccion-head h2 {
  font-size: 1.9rem;
  font-weight: 800;
  margin: 0;
  letter-spacing: -0.5px;
}
.seccion-head p {
  margin: 0.3rem 0 0;
  color: var(--text-muted);
}
.ver-todos {
  color: var(--primary);
  font-weight: 700;
  transition: var(--transition);
}
.ver-todos:hover { color: var(--primary-dark); transform: translateX(3px); }

/* ===== CATEGORÍAS ===== */
.categorias-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
  gap: 1.2rem;
}
.categoria-card {
  position: relative;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.6rem 1.4rem;
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
  box-shadow: var(--shadow-sm);
  transition: var(--transition);
  overflow: hidden;
  animation: floatUp 0.5s ease both;
}
.categoria-card::before {
  content: '';
  position: absolute;
  inset: 0;
  background: var(--gradient-soft);
  opacity: 0;
  transition: var(--transition);
  z-index: 0;
}
.categoria-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-lg);
  border-color: transparent;
}
.categoria-card:hover::before { opacity: 1; }
.categoria-card > * { position: relative; z-index: 1; transition: var(--transition); }
.categoria-card:hover .categoria-nombre,
.categoria-card:hover .categoria-flecha { color: #fff; }
.categoria-icono {
  font-size: 2.4rem;
  width: 58px;
  height: 58px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--primary-soft);
  border-radius: var(--radius-sm);
}
.categoria-card:hover .categoria-icono {
  background: rgba(255, 255, 255, 0.25);
}
.categoria-nombre {
  font-weight: 700;
  font-size: 1.05rem;
}
.categoria-flecha {
  font-weight: 700;
  color: var(--primary);
  align-self: flex-end;
  margin-top: -1.6rem;
}

/* ===== DESTACADOS ===== */
.destacados-seccion { padding-top: 0; }
.destacados-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 1.5rem;
}
.producto-card {
  background: #fff;
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  transition: var(--transition);
  animation: floatUp 0.5s ease both;
}
.producto-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-lg);
}
.producto-imagen {
  position: relative;
  aspect-ratio: 4 / 3;
  background: linear-gradient(135deg, #f1f0ff, #ffe9ee);
}
.producto-imagen img { width: 100%; height: 100%; object-fit: cover; }
.placeholder {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  font-size: 2.5rem; opacity: 0.4;
}
.badge-cat {
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
.producto-body { padding: 1.1rem 1.2rem 1.3rem; display: flex; flex-direction: column; flex: 1; }
.producto-body h3 { margin: 0 0 0.4rem; font-size: 1.05rem; }
.producto-desc {
  margin: 0 0 1rem;
  color: var(--text-muted);
  font-size: 0.85rem;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.producto-foot {
  margin-top: auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.precio { color: var(--primary); font-size: 1.2rem; font-weight: 800; }
.ver-detalle { color: var(--secondary); font-weight: 700; font-size: 0.9rem; }

/* Skeletons */
.skeleton-card {
  height: 320px;
  border-radius: var(--radius);
  background: linear-gradient(100deg, #eef0f5 30%, #f7f8fc 50%, #eef0f5 70%);
  background-size: 200% 100%;
  animation: gradientShift 1.4s ease infinite;
}

/* ===== CTA FINAL ===== */
.cta-final { padding: 1rem 1.5rem 5rem; max-width: 1200px; margin: 0 auto; width: 100%; }
.cta-card {
  background: var(--gradient);
  background-size: 200% 200%;
  animation: gradientShift 10s ease infinite;
  border-radius: var(--radius-lg);
  padding: 3.5rem 2rem;
  text-align: center;
  color: #fff;
  box-shadow: var(--shadow-primary);
}
.cta-card h2 { font-size: 2rem; margin: 0 0 0.7rem; font-weight: 800; }
.cta-card p { margin: 0 0 1.8rem; opacity: 0.92; font-size: 1.05rem; }

@media (max-width: 600px) {
  .hero-stats { gap: 1.5rem; }
  .hero { padding: 4rem 1.5rem; }
}
</style>
