<template>
  <div class="dashboard">
    <header class="dash-head">
      <h2>Dashboard</h2>
      <p>Resumen general de tu tienda</p>
    </header>

    <div class="stats-grid">
      <div class="stat-card stat-primary">
        <div class="stat-top">
          <span class="stat-icon">📦</span>
          <span class="stat-trend">Total</span>
        </div>
        <span class="stat-number">{{ totalProductos }}</span>
        <span class="stat-label">Productos</span>
      </div>

      <div class="stat-card stat-secondary">
        <div class="stat-top">
          <span class="stat-icon">💰</span>
          <span class="stat-trend">Promedio</span>
        </div>
        <span class="stat-number">{{ formato(precioPromedio) }}</span>
        <span class="stat-label">Precio promedio</span>
      </div>

      <div class="stat-card stat-success">
        <div class="stat-top">
          <span class="stat-icon">🏪</span>
          <span class="stat-trend">Inventario</span>
        </div>
        <span class="stat-number">{{ totalStock }}</span>
        <span class="stat-label">Unidades en stock</span>
      </div>
    </div>

    <div class="dash-grid">
      <div class="panel quick-actions">
        <h3>Acciones rápidas</h3>
        <div class="actions-grid">
          <RouterLink to="/admin/productos" class="action-card">
            <span class="action-icon">📋</span>
            <div><strong>Gestionar productos</strong><small>Crear, editar y eliminar</small></div>
            <span class="action-arrow">→</span>
          </RouterLink>
          <RouterLink to="/catalogo" class="action-card">
            <span class="action-icon">🛍️</span>
            <div><strong>Ver tienda</strong><small>Cómo lo ven tus clientes</small></div>
            <span class="action-arrow">→</span>
          </RouterLink>
        </div>
      </div>

      <div class="panel">
        <Suspense>
          <GraficaVentas />
          <template #fallback>
            <div class="grafica-loading">📊 Cargando gráfica...</div>
          </template>
        </Suspense>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, defineAsyncComponent } from 'vue'
import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'

const GraficaVentas = defineAsyncComponent({
  loader: () => import('@/components/GraficaVentas.vue'),
  delay: 200,
  timeout: 5000,
})

const productos = ref([])

const totalProductos = computed(() => productos.value.length)
const totalStock     = computed(() => productos.value.reduce((s, p) => s + p.stock, 0))
const precioPromedio = computed(() => {
  if (!productos.value.length) return 0
  return productos.value.reduce((s, p) => s + parseFloat(p.precio), 0) / productos.value.length
})

const formato = (precio) =>
  new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(precio)

onMounted(async () => {
  try {
    const res = await axios.get(`${API_URL}/api/productos`)
    productos.value = Array.isArray(res.data) ? res.data : res.data.data || []
  } catch (e) {
    console.error(e)
    productos.value = []
  }
})
</script>

<style scoped>
.dashboard { animation: fadeIn 0.4s ease; }
.dash-head { margin-bottom: 1.8rem; }
.dash-head h2 { margin: 0; font-size: 1.7rem; font-weight: 800; letter-spacing: -0.5px; }
.dash-head p { margin: 0.3rem 0 0; color: var(--text-muted); }

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.3rem;
  margin-bottom: 1.8rem;
}
.stat-card {
  position: relative;
  border-radius: var(--radius);
  padding: 1.5rem;
  color: #fff;
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: var(--transition);
}
.stat-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }
.stat-primary   { background: linear-gradient(135deg, #6C63FF, #8B85FF); }
.stat-secondary { background: linear-gradient(135deg, #FF6584, #FF8FA3); }
.stat-success   { background: linear-gradient(135deg, #48BB78, #68D391); }
.stat-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.6rem;
}
.stat-icon {
  font-size: 1.5rem;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.22);
  border-radius: var(--radius-sm);
}
.stat-trend {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  background: rgba(255, 255, 255, 0.2);
  padding: 0.25rem 0.7rem;
  border-radius: var(--radius-pill);
}
.stat-number { font-size: 2rem; font-weight: 800; line-height: 1.1; }
.stat-label { font-size: 0.9rem; opacity: 0.92; }

.dash-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.3rem;
}
.panel {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.5rem;
  box-shadow: var(--shadow-sm);
}
.quick-actions h3 { margin: 0 0 1.1rem; font-size: 1.15rem; font-weight: 800; }
.actions-grid { display: flex; flex-direction: column; gap: 0.8rem; }
.action-card {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  background: var(--light);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: 1rem 1.2rem;
  transition: var(--transition);
}
.action-card:hover {
  background: var(--primary-soft);
  border-color: var(--primary-light);
  transform: translateX(4px);
}
.action-icon {
  font-size: 1.4rem;
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  border-radius: var(--radius-sm);
  box-shadow: var(--shadow-sm);
}
.action-card div { display: flex; flex-direction: column; flex: 1; }
.action-card strong { font-size: 0.95rem; }
.action-card small { font-size: 0.8rem; color: var(--text-muted); }
.action-arrow { color: var(--primary); font-weight: 700; font-size: 1.1rem; }
.grafica-loading { padding: 2rem; text-align: center; color: var(--text-muted); }

@media (max-width: 820px) {
  .dash-grid { grid-template-columns: 1fr; }
}
</style>
