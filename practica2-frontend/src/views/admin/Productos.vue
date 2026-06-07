<template>
  <div class="admin-productos">
    <div class="page-header">
      <div class="page-title">
        <h2>Gestión de productos</h2>
        <span class="badge-rol" :class="`rol-${auth.user?.rol}`">{{ auth.user?.rol }}</span>
      </div>
      <button class="btn-nuevo" @click="abrirNuevo" v-can="'crear'">
        <span>＋</span> Nuevo producto
      </button>
    </div>

    <!-- Resumen rápido -->
    <div class="mini-stats">
      <div class="mini-stat">
        <span class="mini-icon">📦</span>
        <div><strong>{{ productos.length }}</strong><small>Productos</small></div>
      </div>
      <div class="mini-stat">
        <span class="mini-icon">🏷️</span>
        <div><strong>{{ categorias.length }}</strong><small>Categorías</small></div>
      </div>
      <div class="mini-stat">
        <span class="mini-icon">⚠️</span>
        <div><strong>{{ sinStock }}</strong><small>Sin stock</small></div>
      </div>
    </div>

    <!-- Tabla -->
    <div v-if="loadingProductos" class="tabla-wrap">
      <div v-for="n in 5" :key="n" class="skeleton-row"></div>
    </div>

    <div v-else class="tabla-wrap">
      <table class="tabla">
        <thead>
          <tr>
            <th>Producto</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Stock</th>
            <th class="col-acciones">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="producto in productos" :key="producto.id">
            <td>
              <div class="celda-producto">
                <div class="celda-thumb">
                  <img v-if="producto.imagen_url" :src="producto.imagen_url" alt="imagen" />
                  <span v-else>🖼️</span>
                </div>
                <div class="celda-nombre">
                  <strong>{{ producto.nombre }}</strong>
                  <small>{{ producto.descripcion || 'Sin descripción' }}</small>
                </div>
              </div>
            </td>
            <td>
              <span v-if="producto.categoria" class="badge-categoria">
                {{ producto.categoria.nombre }}
              </span>
              <span v-else class="texto-vacio">—</span>
            </td>
            <td><strong class="precio">{{ formato(producto.precio) }}</strong></td>
            <td>
              <span class="badge-stock" :class="estadoStock(producto.stock)">
                {{ producto.stock }} u.
              </span>
            </td>
            <td>
              <div class="acciones">
                <button class="btn-icono editar" title="Editar" @click="editar(producto)" v-can="'editar'">✏️</button>
                <button class="btn-icono eliminar" title="Eliminar" @click="eliminar(producto.id)" v-can="'eliminar'">🗑️</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="productos.length === 0" class="empty">No hay productos registrados.</p>
    </div>

    <!-- Drawer del formulario -->
    <Transition name="drawer">
      <div v-if="mostrarFormulario" class="drawer-overlay" @click.self="cancelar">
        <aside class="drawer">
          <header class="drawer-head">
            <h3>{{ editando ? '✏️ Editar producto' : '＋ Nuevo producto' }}</h3>
            <button class="btn-cerrar" @click="cancelar">✕</button>
          </header>

          <div class="drawer-body">
            <div v-if="mensaje" :class="['mensaje', tipoMensaje]">{{ mensaje }}</div>

            <InputField
              label="Nombre"
              name="nombre"
              v-model="nombre"
              placeholder="Nombre del producto"
              :error="errors.nombre || erroresServidor.nombre?.[0]"
            />

            <InputField
              label="Descripción"
              name="descripcion"
              v-model="descripcion"
              placeholder="Descripción del producto"
              :error="errors.descripcion || erroresServidor.descripcion?.[0]"
            />

            <div class="form-row">
              <InputField
                label="Precio"
                name="precio"
                type="number"
                v-model="precio"
                placeholder="0.00"
                :error="errors.precio || erroresServidor.precio?.[0]"
              />
              <InputField
                label="Stock"
                name="stock"
                type="number"
                v-model="stock"
                placeholder="0"
                :error="errors.stock || erroresServidor.stock?.[0]"
              />
            </div>

            <div class="form-group">
              <label>Categoría</label>
              <select v-model="categoria_id">
                <option value="">Sin categoría</option>
                <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                  {{ cat.nombre }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label>Imagen</label>
              <label class="file-drop">
                <input type="file" accept="image/*" @change="onImageChange" hidden />
                <template v-if="preview">
                  <img :src="preview" alt="Preview" class="file-preview" />
                </template>
                <template v-else>
                  <span class="file-icon">📤</span>
                  <span>Haz clic para subir una imagen</span>
                </template>
              </label>
            </div>
          </div>

          <footer class="drawer-foot">
            <button class="btn-cancelar" @click="cancelar">Cancelar</button>
            <button class="btn-guardar" @click="guardar" :disabled="loading">
              <span v-if="loading" class="spinner"></span>
              {{ loading ? 'Guardando...' : 'Guardar producto' }}
            </button>
          </footer>
        </aside>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useForm, useField } from 'vee-validate'
import { useAuthStore } from '@/stores/auth'
import { productoSchema } from '@/schemas/productoSchema'
import InputField from '@/components/InputField.vue'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'

const auth             = useAuthStore()
const productos        = ref([])
const loadingProductos = ref(true)
const loading          = ref(false)
const mostrarFormulario = ref(false)
const editando         = ref(null)
const preview          = ref(null)
const imagen           = ref(null)
const mensaje          = ref('')
const tipoMensaje      = ref('success')
const categorias       = ref([])
const categoria_id     = ref('')
const erroresServidor  = ref({})

const sinStock = computed(() => productos.value.filter((p) => p.stock <= 0).length)

const { handleSubmit, errors, resetForm, setValues } = useForm({
  validationSchema: productoSchema,
  initialValues: { nombre: '', descripcion: '', precio: '', stock: '' }
})

const { value: nombre }      = useField('nombre')
const { value: descripcion } = useField('descripcion')
const { value: precio }      = useField('precio')
const { value: stock }       = useField('stock')

const headers = { Authorization: `Bearer ${auth.token}` }

const formato = (precio) =>
  new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(precio)

const estadoStock = (stock) => {
  if (stock <= 0) return 'agotado'
  if (stock < 10) return 'bajo'
  return 'ok'
}

const cargarProductos = async () => {
  loadingProductos.value = true
  try {
    const res = await axios.get(`${API_URL}/api/productos`)
    productos.value = res.data.data
  } catch (e) {
    console.error(e)
  } finally {
    loadingProductos.value = false
  }
}

const cargarCategorias = async () => {
  try {
    const res = await axios.get(`${API_URL}/api/categorias`)
    categorias.value = res.data.data
  } catch (e) {
    console.error(e)
  }
}

const onImageChange = (e) => {
  const file = e.target.files[0]
  if (!file) return
  imagen.value  = file
  preview.value = URL.createObjectURL(file)
}

const abrirNuevo = () => {
  cancelar()
  mostrarFormulario.value = true
}

const guardar = handleSubmit(async (values) => {
  loading.value = true
  mensaje.value = ''
  erroresServidor.value = {}
  try {
    const fd = new FormData()
    fd.append('nombre',       values.nombre)
    fd.append('descripcion',  values.descripcion || '')
    fd.append('precio',       values.precio)
    fd.append('stock',        values.stock)
    fd.append('categoria_id', categoria_id.value)
    if (imagen.value) fd.append('imagen', imagen.value)

    if (editando.value) {
      fd.append('_method', 'PUT')
      await axios.post(`${API_URL}/api/productos/${editando.value}`, fd, {
        headers: { ...headers, 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await axios.post(`${API_URL}/api/productos`, fd, {
        headers: { ...headers, 'Content-Type': 'multipart/form-data' }
      })
    }
    tipoMensaje.value = 'success'
    mensaje.value = editando.value ? 'Producto actualizado.' : 'Producto creado.'
    await cargarProductos()
    setTimeout(() => cancelar(), 1500)
  } catch (e) {
    if (e.response?.status === 422) {
      erroresServidor.value = e.response.data.errors
      tipoMensaje.value = 'error'
      mensaje.value = 'Corrige los errores del formulario.'
    } else {
      tipoMensaje.value = 'error'
      mensaje.value = e.response?.data?.message || 'Error al guardar.'
    }
  } finally {
    loading.value = false
  }
})

const editar = (producto) => {
  editando.value = producto.id
  setValues({
    nombre:      producto.nombre,
    descripcion: producto.descripcion || '',
    precio:      producto.precio,
    stock:       producto.stock,
  })
  categoria_id.value      = producto.categoria_id || ''
  preview.value           = producto.imagen_url || null
  mostrarFormulario.value = true
}

const eliminar = async (id) => {
  if (!confirm('¿Eliminar este producto?')) return
  try {
    await axios.delete(`${API_URL}/api/productos/${id}`, { headers })
    await cargarProductos()
  } catch (e) {
    console.error(e)
  }
}

const cancelar = () => {
  mostrarFormulario.value = false
  editando.value        = null
  imagen.value          = null
  preview.value         = null
  mensaje.value         = ''
  categoria_id.value    = ''
  erroresServidor.value = {}
  resetForm()
}

onMounted(async () => {
  await cargarProductos()
  await cargarCategorias()
})
</script>

<style scoped>
.admin-productos { animation: fadeIn 0.4s ease; }
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.8rem;
}
.page-title { display: flex; align-items: center; gap: 0.9rem; }
.page-title h2 { margin: 0; font-size: 1.7rem; font-weight: 800; letter-spacing: -0.5px; }
.badge-rol {
  padding: 0.25rem 0.8rem;
  border-radius: var(--radius-pill);
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  color: #fff;
  background: var(--primary);
}
.badge-rol.rol-admin { background: var(--gradient-soft); }
.badge-rol.rol-editor { background: var(--warning); }
.badge-rol.rol-vendedor { background: var(--success); }
.btn-nuevo {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--gradient-soft);
  color: #fff;
  border: none;
  padding: 0.75rem 1.4rem;
  border-radius: var(--radius-pill);
  cursor: pointer;
  font-size: 0.95rem;
  font-weight: 700;
  box-shadow: var(--shadow-primary);
  transition: var(--transition);
}
.btn-nuevo span { font-size: 1.2rem; line-height: 1; }
.btn-nuevo:hover { transform: translateY(-2px); filter: brightness(1.05); }

/* Mini stats */
.mini-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 1rem;
  margin-bottom: 1.8rem;
}
.mini-stat {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.1rem 1.3rem;
  display: flex;
  align-items: center;
  gap: 0.9rem;
  box-shadow: var(--shadow-sm);
}
.mini-icon {
  font-size: 1.5rem;
  width: 46px;
  height: 46px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--primary-soft);
  border-radius: var(--radius-sm);
}
.mini-stat div { display: flex; flex-direction: column; }
.mini-stat strong { font-size: 1.4rem; font-weight: 800; }
.mini-stat small { font-size: 0.8rem; color: var(--text-muted); }

/* Tabla */
.tabla-wrap {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}
.tabla { width: 100%; border-collapse: collapse; }
.tabla th {
  background: var(--light);
  color: var(--text-muted);
  padding: 0.9rem 1.2rem;
  text-align: left;
  font-weight: 700;
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  border-bottom: 1px solid var(--border);
}
.col-acciones { text-align: right; }
.tabla td { padding: 0.85rem 1.2rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
.tabla tbody tr { transition: var(--transition); }
.tabla tbody tr:last-child td { border-bottom: none; }
.tabla tbody tr:hover { background: var(--primary-soft); }

.celda-producto { display: flex; align-items: center; gap: 0.8rem; }
.celda-thumb {
  width: 46px;
  height: 46px;
  flex-shrink: 0;
  border-radius: var(--radius-sm);
  background: linear-gradient(135deg, #f1f0ff, #ffe9ee);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.celda-thumb img { width: 100%; height: 100%; object-fit: cover; }
.celda-nombre { display: flex; flex-direction: column; min-width: 0; }
.celda-nombre strong { font-size: 0.95rem; }
.celda-nombre small {
  color: var(--text-muted);
  font-size: 0.8rem;
  max-width: 280px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.precio { color: var(--primary); font-weight: 800; }
.texto-vacio { color: var(--text-soft); }
.badge-categoria {
  background: var(--secondary-soft);
  color: var(--secondary-dark);
  padding: 0.25rem 0.7rem;
  border-radius: var(--radius-pill);
  font-size: 0.78rem;
  font-weight: 700;
}
.badge-stock {
  padding: 0.25rem 0.7rem;
  border-radius: var(--radius-pill);
  font-size: 0.78rem;
  font-weight: 700;
}
.badge-stock.ok { background: var(--success-soft); color: #276749; }
.badge-stock.bajo { background: var(--warning-soft); color: #9c4221; }
.badge-stock.agotado { background: var(--danger-soft); color: #c53030; }

.acciones { display: flex; gap: 0.4rem; justify-content: flex-end; }
.btn-icono {
  width: 36px;
  height: 36px;
  border: 1px solid var(--border);
  background: #fff;
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-size: 1rem;
  transition: var(--transition);
}
.btn-icono.editar:hover { background: var(--primary-soft); border-color: var(--primary); transform: translateY(-2px); }
.btn-icono.eliminar:hover { background: var(--danger-soft); border-color: var(--danger); transform: translateY(-2px); }

.empty { text-align: center; padding: 3rem; color: var(--text-muted); }
.skeleton-row {
  height: 64px;
  margin: 0.6rem;
  border-radius: var(--radius-sm);
  background: linear-gradient(100deg, #eef0f5 30%, #f7f8fc 50%, #eef0f5 70%);
  background-size: 200% 100%;
  animation: gradientShift 1.4s ease infinite;
}

/* ===== Drawer ===== */
.drawer-overlay {
  position: fixed;
  inset: 0;
  background: rgba(45, 55, 72, 0.45);
  backdrop-filter: blur(2px);
  z-index: 200;
  display: flex;
  justify-content: flex-end;
}
.drawer {
  width: 460px;
  max-width: 92vw;
  height: 100%;
  background: #fff;
  display: flex;
  flex-direction: column;
  box-shadow: var(--shadow-lg);
}
.drawer-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.3rem 1.5rem;
  border-bottom: 1px solid var(--border);
}
.drawer-head h3 { margin: 0; font-size: 1.2rem; font-weight: 800; }
.btn-cerrar {
  width: 36px;
  height: 36px;
  border: none;
  background: var(--light);
  border-radius: 50%;
  cursor: pointer;
  font-size: 1rem;
  color: var(--text-muted);
  transition: var(--transition);
}
.btn-cerrar:hover { background: var(--danger-soft); color: var(--danger); }
.drawer-body { padding: 1.5rem; overflow-y: auto; flex: 1; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.45rem; margin-bottom: 1rem; }
.form-group label { font-size: 0.85rem; font-weight: 600; color: var(--text); }
select {
  padding: 0.65rem 0.8rem;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  font-size: 1rem;
  background: #fff;
  cursor: pointer;
  transition: var(--transition);
}
select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-soft); }
.file-drop {
  border: 2px dashed var(--border);
  border-radius: var(--radius-sm);
  padding: 1.4rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  color: var(--text-muted);
  font-size: 0.88rem;
  transition: var(--transition);
}
.file-drop:hover { border-color: var(--primary); background: var(--primary-soft); color: var(--primary-dark); }
.file-icon { font-size: 1.6rem; }
.file-preview { max-width: 140px; border-radius: var(--radius-sm); }
.drawer-foot {
  display: flex;
  gap: 0.8rem;
  padding: 1.2rem 1.5rem;
  border-top: 1px solid var(--border);
}
.btn-cancelar {
  flex: 1;
  padding: 0.8rem;
  border: 1.5px solid var(--border);
  background: #fff;
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-weight: 600;
  transition: var(--transition);
}
.btn-cancelar:hover { background: var(--light); }
.btn-guardar {
  flex: 2;
  padding: 0.8rem;
  background: var(--gradient-soft);
  color: #fff;
  border: none;
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-weight: 700;
  box-shadow: var(--shadow-primary);
  transition: var(--transition);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}
.btn-guardar:hover:not(:disabled) { transform: translateY(-2px); filter: brightness(1.05); }
.btn-guardar:disabled { opacity: 0.7; cursor: not-allowed; }
.spinner {
  width: 15px; height: 15px;
  border: 2px solid rgba(255, 255, 255, 0.4);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
.mensaje { padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1.2rem; font-size: 0.9rem; font-weight: 600; }
.mensaje.success { background: var(--success-soft); color: #276749; }
.mensaje.error   { background: var(--danger-soft); color: #c53030; }

/* Transición del drawer */
.drawer-enter-active, .drawer-leave-active { transition: opacity 0.3s ease; }
.drawer-enter-active .drawer, .drawer-leave-active .drawer { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.drawer-enter-from, .drawer-leave-to { opacity: 0; }
.drawer-enter-from .drawer, .drawer-leave-to .drawer { transform: translateX(100%); }

@media (max-width: 720px) {
  .celda-nombre small { max-width: 140px; }
  .tabla th:nth-child(2), .tabla td:nth-child(2) { display: none; }
}
</style>
