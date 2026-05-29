<!-- src/App.vue -->
<script setup>
import { ref, onMounted } from 'vue'
import { getProductos, deleteProducto } from './services/productoService'
import ProductosList from './components/ProductosList.vue'
import ProductoForm from './components/ProductoForm.vue'

const productos = ref([])
const cargando = ref(false)
const error = ref('')
const productoEnEdicion = ref(null) // null = crear; objeto = editar

const cargarProductos = async () => {
  cargando.value = true
  error.value = ''
  try {
    const { data } = await getProductos()
    productos.value = data
  } catch (e) {
    error.value = 'No se pudieron cargar los productos. ¿Está corriendo Laravel?'
  } finally {
    cargando.value = false
  }
}

const onEditar = (producto) => {
  productoEnEdicion.value = { ...producto }
}

const onGuardado = () => {
  productoEnEdicion.value = null
  cargarProductos()
}

const onEliminar = async (id) => {
  if (!confirm('¿Eliminar este producto?')) return
  try {
    await deleteProducto(id)
    cargarProductos()
  } catch (e) {
    error.value = 'No se pudo eliminar el producto.'
  }
}

onMounted(cargarProductos) // carga la lista al abrir la página
</script>

<template>
  <main style="max-width: 900px; margin: 2rem auto; font-family: system-ui; padding: 0 1rem;">
    <h1>Catálogo de Productos</h1>

    <ProductoForm
        :producto="productoEnEdicion"
        @guardado="onGuardado"
        @cancelar="productoEnEdicion = null"
    />

    <p v-if="cargando">Cargando…</p>
    <p v-if="error" style="color: #c0392b;">{{ error }}</p>

    <ProductosList
        v-if="!cargando"
        :productos="productos"
        @editar="onEditar"
        @eliminar="onEliminar"
    />
  </main>
</template>