<template>
  <div class="dashboard">
    <h2>Dashboard</h2>

    <div class="stats-grid">
      <div class="stat-card">
        <span class="stat-icon">📦</span>
        <div class="stat-info">
          <span class="stat-number">{{ totalProductos }}</span>
          <span class="stat-label">Productos</span>
        </div>
      </div>

      <div class="stat-card">
        <span class="stat-icon">💰</span>
        <div class="stat-info">
          <span class="stat-number">${{ precioPromedio }}</span>
          <span class="stat-label">Precio promedio</span>
        </div>
      </div>

      <div class="stat-card">
        <span class="stat-icon">🏪</span>
        <div class="stat-info">
          <span class="stat-number">{{ totalStock }}</span>
          <span class="stat-label">Unidades en stock</span>
        </div>
      </div>
    </div>

    <div class="quick-actions">
      <h3>Acciones rápidas</h3>
      <div class="actions-grid">
        <RouterLink to="/admin/productos" class="action-card">
          <span>📋</span>
          <p>Ver productos</p>
        </RouterLink>
        <RouterLink to="/catalogo" class="action-card">
          <span>🛍️</span>
          <p>Ver tienda</p>
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const productos = ref([])

const totalProductos = computed(() => productos.value.length)
const totalStock     = computed(() => productos.value.reduce((s, p) => s + p.stock, 0))
const precioPromedio = computed(() => {
  if (!productos.value.length) return 0
  const avg = productos.value.reduce((s, p) => s + parseFloat(p.precio), 0) / productos.value.length
  return avg.toFixed(2)
})

onMounted(async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/productos')
    productos.value = res.data
  } catch (e) {
    console.error(e)
  }
})
</script>

<style scoped>
.dashboard h2 { margin: 0 0 2rem; }
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}
.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.stat-icon { font-size: 2.5rem; }
.stat-info { display: flex; flex-direction: column; }
.stat-number { font-size: 1.8rem; font-weight: bold; color: #42b883; }
.stat-label  { font-size: 0.85rem; color: #999; }
.quick-actions h3 { margin: 0 0 1rem; }
.actions-grid {
  display: flex;
  gap: 1rem;
}
.action-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem 2rem;
  text-align: center;
  text-decoration: none;
  color: #333;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transition: transform 0.2s;
}
.action-card:hover { transform: translateY(-2px); }
.action-card span { font-size: 2rem; }
.action-card p { margin: 0.5rem 0 0; font-weight: 500; }
</style>