<template>
  <div class="admin-productos">
    <div class="page-header">
      <h2>Gestión de Productos</h2>
      <button class="btn-nuevo" @click="mostrarFormulario = true">
        + Nuevo Producto
      </button>
    </div>

    <!-- Formulario -->
    <div v-if="mostrarFormulario" class="formulario-card">
      <h3>{{ editando ? 'Editar Producto' : 'Nuevo Producto' }}</h3>

      <div v-if="mensaje" :class="['mensaje', tipoMensaje]">{{ mensaje }}</div>

      <div class="form-group">
        <label>Nombre</label>
        <input v-model="form.nombre" type="text" placeholder="Nombre del producto" />
      </div>
      <div class="form-group">
        <label>Descripción</label>
        <textarea v-model="form.descripcion" placeholder="Descripción"></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Precio</label>
          <input v-model="form.precio" type="number" placeholder="0.00" />
        </div>
        <div class="form-group">
          <label>Stock</label>
          <input v-model="form.stock" type="number" placeholder="0" />
        </div>
      </div>

      <div class="form-group">
        <label>Imagen</label>
        <input type="file" accept="image/*" @change="onImageChange" />
        <div v-if="preview" class="preview">
          <img :src="preview" alt="Preview" />
        </div>
      </div>

      <div class="form-acciones">
        <button class="btn-cancelar" @click="cancelar">Cancelar</button>
        <button class="btn-guardar" @click="guardar" :disabled="loading">
          {{ loading ? 'Guardando...' : 'Guardar' }}
        </button>
      </div>
    </div>

    <!-- Tabla -->
    <div v-if="loadingProductos" class="loading">Cargando productos...</div>

    <div v-else>
      <table class="tabla">
        <thead>
        <tr>
          <th>Imagen</th>
          <th>Nombre</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="producto in productos" :key="producto.id">
          <td>
            <img
                v-if="producto.imagen_url"
                :src="producto.imagen_url"
                class="tabla-imagen"
                alt="imagen"
            />
            <span v-else class="sin-imagen">Sin imagen</span>
          </td>
          <td>{{ producto.nombre }}</td>
          <td>${{ producto.precio }}</td>
          <td>{{ producto.stock }}</td>
          <td>
            <button class="btn-editar" @click="editar(producto)">✏️</button>
            <button class="btn-eliminar" @click="eliminar(producto.id)">🗑️</button>
          </td>
        </tr>
        </tbody>
      </table>
      <p v-if="productos.length === 0" class="empty">No hay productos registrados.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const auth            = useAuthStore()
const productos       = ref([])
const loadingProductos = ref(true)
const loading         = ref(false)
const mostrarFormulario = ref(false)
const editando        = ref(null)
const preview         = ref(null)
const imagen          = ref(null)
const mensaje         = ref('')
const tipoMensaje     = ref('success')

const form = ref({
  nombre: '', descripcion: '', precio: '', stock: ''
})

const headers = { Authorization: `Bearer ${auth.token}` }

const cargarProductos = async () => {
  loadingProductos.value = true
  try {
    const res = await axios.get('http://localhost:8000/api/productos')
    productos.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loadingProductos.value = false
  }
}

const onImageChange = (e) => {
  const file = e.target.files[0]
  if (!file) return
  imagen.value   = file
  preview.value  = URL.createObjectURL(file)
}

const guardar = async () => {
  loading.value = true
  mensaje.value = ''
  try {
    const fd = new FormData()
    fd.append('nombre',      form.value.nombre)
    fd.append('descripcion', form.value.descripcion)
    fd.append('precio',      form.value.precio)
    fd.append('stock',       form.value.stock)
    if (imagen.value) fd.append('imagen', imagen.value)

    if (editando.value) {
      fd.append('_method', 'PUT')
      await axios.post(`http://localhost:8000/api/productos/${editando.value}`, fd, {
        headers: { ...headers, 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await axios.post('http://localhost:8000/api/productos', fd, {
        headers: { ...headers, 'Content-Type': 'multipart/form-data' }
      })
    }

    tipoMensaje.value = 'success'
    mensaje.value = editando.value ? 'Producto actualizado.' : 'Producto creado.'
    await cargarProductos()
    setTimeout(() => cancelar(), 1500)
  } catch (e) {
    tipoMensaje.value = 'error'
    mensaje.value = e.response?.data?.message || 'Error al guardar.'
  } finally {
    loading.value = false
  }
}

const editar = (producto) => {
  editando.value = producto.id
  form.value = {
    nombre:      producto.nombre,
    descripcion: producto.descripcion || '',
    precio:      producto.precio,
    stock:       producto.stock,
  }
  preview.value        = producto.imagen_url || null
  mostrarFormulario.value = true
}

const eliminar = async (id) => {
  if (!confirm('¿Eliminar este producto?')) return
  try {
    await axios.delete(`http://localhost:8000/api/productos/${id}`, { headers })
    await cargarProductos()
  } catch (e) {
    console.error(e)
  }
}

const cancelar = () => {
  mostrarFormulario.value = false
  editando.value  = null
  imagen.value    = null
  preview.value   = null
  mensaje.value   = ''
  form.value = { nombre: '', descripcion: '', precio: '', stock: '' }
}

onMounted(cargarProductos)
</script>

<style scoped>
.admin-productos h2 { margin: 0 0 2rem; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.page-header h2 { margin: 0; }
.btn-nuevo {
  background: #42b883;
  color: white;
  border: none;
  padding: 0.7rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.95rem;
}
.formulario-card {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.formulario-card h3 { margin: 0 0 1.5rem; }
.form-group { display: flex; flex-direction: column; gap: 0.4rem; margin-bottom: 1rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
input, textarea {
  padding: 0.6rem 0.8rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
}
textarea { resize: vertical; min-height: 80px; }
.preview img { margin-top: 0.5rem; max-width: 150px; border-radius: 8px; }
.form-acciones { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem; }
.btn-cancelar {
  padding: 0.7rem 1.5rem;
  border: 1px solid #ddd;
  background: white;
  border-radius: 8px;
  cursor: pointer;
}
.btn-guardar {
  padding: 0.7rem 1.5rem;
  background: #42b883;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}
.btn-guardar:disabled { opacity: 0.6; cursor: not-allowed; }
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
.tabla td { padding: 0.8rem 1rem; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
.tabla tr:last-child td { border-bottom: none; }
.tabla tr:hover td { background: #f8f9fa; }
.tabla-imagen { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; }
.sin-imagen { color: #999; font-size: 0.8rem; }
.btn-editar, .btn-eliminar {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.1rem;
  padding: 0.3rem;
}
.mensaje {
  padding: 0.7rem 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
  font-size: 0.9rem;
}
.success { background: #d4edda; color: #155724; }
.error   { background: #f8d7da; color: #721c24; }
.loading, .empty { text-align: center; padding: 3rem; color: #999; }
</style>