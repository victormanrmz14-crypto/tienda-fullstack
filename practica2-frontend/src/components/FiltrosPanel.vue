<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'

const props = defineProps({
  modelValue: { type: Object, required: true },
})
const emit = defineEmits(['update:modelValue', 'limpiar'])

const filtros = props.modelValue

// --- Búsqueda con debounce ---
const busquedaLocal = ref(filtros.busqueda)
let debounceId = null

const onBuscar = () => {
  clearTimeout(debounceId)
  debounceId = setTimeout(() => {
    filtros.busqueda = busquedaLocal.value
    filtros.pagina = 1
  }, 300)
}

// Mantiene el input sincronizado cuando los filtros se resetean externamente
watch(
  () => filtros.busqueda,
  (val) => {
    if (val !== busquedaLocal.value) busquedaLocal.value = val
  }
)

// --- Categorías ---
const categorias = ref([])
onMounted(async () => {
  try {
    const { data } = await axios.get(`${API_URL}/api/categorias`)
    categorias.value = data.data ?? data
  } catch (e) {
    console.error('Error cargando categorías', e)
  }
})

const onCategoria = () => {
  filtros.pagina = 1
}

const onPrecio = () => {
  filtros.pagina = 1
}

// --- Ordenamiento (combina orden|dir) ---
const ordenSeleccionado = computed({
  get: () => `${filtros.orden}|${filtros.dir}`,
  set: (valor) => {
    const [orden, dir] = valor.split('|')
    filtros.orden = orden
    filtros.dir = dir
    filtros.pagina = 1
  },
})
</script>

<template>
  <aside class="filtros-panel">
    <div class="panel-head">
      <h3>🎛️ Filtros</h3>
      <button class="btn-limpiar" @click="emit('limpiar')">Limpiar</button>
    </div>

    <div class="campo">
      <label>Buscar</label>
      <div class="input-icono">
        <span class="icono">🔍</span>
        <input
          v-model="busquedaLocal"
          type="text"
          placeholder="Nombre o descripción..."
          @input="onBuscar"
        />
      </div>
    </div>

    <div class="campo">
      <label>Categoría</label>
      <select v-model="filtros.categoria_id" @change="onCategoria">
        <option value="">Todas</option>
        <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
          {{ cat.nombre }}
        </option>
      </select>
    </div>

    <div class="campo">
      <label>Rango de precio</label>
      <div class="precio-rango">
        <input
          v-model="filtros.precio_min"
          type="number"
          min="0"
          placeholder="Mín"
          @change="onPrecio"
        />
        <span class="guion">—</span>
        <input
          v-model="filtros.precio_max"
          type="number"
          min="0"
          placeholder="Máx"
          @change="onPrecio"
        />
      </div>
    </div>

    <div class="campo">
      <label>Ordenar por</label>
      <select v-model="ordenSeleccionado">
        <option value="nombre|asc">Nombre (A-Z)</option>
        <option value="nombre|desc">Nombre (Z-A)</option>
        <option value="precio|asc">Precio (menor a mayor)</option>
        <option value="precio|desc">Precio (mayor a menor)</option>
      </select>
    </div>
  </aside>
</template>

<style scoped>
.filtros-panel {
  background: #fff;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
  padding: 1.4rem;
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
  position: sticky;
  top: 5.5rem;
}
.panel-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 0.9rem;
  border-bottom: 1px solid var(--border);
}
.panel-head h3 {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 800;
}
.btn-limpiar {
  background: none;
  border: none;
  color: var(--secondary);
  cursor: pointer;
  font-size: 0.82rem;
  font-weight: 700;
  padding: 0.2rem 0.4rem;
  border-radius: var(--radius-sm);
  transition: var(--transition);
}
.btn-limpiar:hover { background: var(--secondary-soft); }
.campo {
  display: flex;
  flex-direction: column;
  gap: 0.45rem;
}
.campo label {
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.3px;
}
.input-icono { position: relative; }
.input-icono .icono {
  position: absolute;
  left: 0.7rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 0.9rem;
  pointer-events: none;
}
.input-icono input { padding-left: 2.1rem; }
.campo input,
.campo select {
  padding: 0.6rem 0.7rem;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  font-size: 0.9rem;
  width: 100%;
  box-sizing: border-box;
  background: var(--light);
  color: var(--text);
  transition: var(--transition);
}
.campo input:focus,
.campo select:focus {
  outline: none;
  border-color: var(--primary);
  background: #fff;
  box-shadow: 0 0 0 3px var(--primary-soft);
}
.precio-rango {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.guion { color: var(--text-soft); }
</style>
