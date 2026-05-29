<template>
  <div class="admin-productos">
    <div class="page-header">
      <h2>Gestión de Productos</h2>
    </div>

    <div v-if="loading" class="loading">Cargando productos...</div>

    <div v-else>
      <table class="tabla">
        <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Precio</th>
          <th>Stock</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="producto in productos" :key="producto.id">
          <td>{{ producto.id }}</td>
          <td>{{ producto.nombre }}</td>
          <td>${{ producto.precio }}</td>
          <td>{{ producto.stock }}</td>
        </tr>
        </tbody>
      </table>

      <p v-if="productos.length === 0" class="empty">
        No hay productos registrados.
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const auth     = useAuthStore()
const loading  = ref(true)
const productos = ref([])

onMounted(async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/productos', {
      headers: { Authorization: `Bearer ${auth.token}` }
    })
    productos.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.admin-productos h2 { margin: 0 0 2rem; }
.tabla {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.tabla th {
  background: #35495e;
  color: white;
  padding: 1rem;
  text-align: left;
  font-weight: 500;
}
.tabla td {
  padding: 1rem;
  border-bottom: 1px solid #f0f0f0;
}
.tabla tr:last-child td { border-bottom: none; }
.tabla tr:hover td { background: #f8f9fa; }
.loading, .empty { text-align: center; padding: 3rem; color: #999; }
</style>