<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import axios from 'axios'

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
    const { data } = await axios.get('http://localhost:8000/api/categorias')
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
    <h3>Filtros</h3>

    <div class="campo">
      <label>Buscar</label>
      <input
        v-model="busquedaLocal"
        type="text"
        placeholder="Nombre o descripción..."
        @input="onBuscar"
      />
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
        <span>—</span>
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

    <button class="btn-limpiar" @click="emit('limpiar')">
      Limpiar filtros
    </button>
  </aside>
</template>

<style scoped>
.filtros-panel {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
  position: sticky;
  top: 1rem;
}
.filtros-panel h3 {
  margin: 0;
  font-size: 1.1rem;
  color: #333;
}
.campo {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}
.campo label {
  font-size: 0.8rem;
  font-weight: 600;
  color: #555;
}
.campo input,
.campo select {
  padding: 0.5rem 0.65rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 0.9rem;
  width: 100%;
  box-sizing: border-box;
}
.campo input:focus,
.campo select:focus {
  outline: none;
  border-color: #42b883;
}
.precio-rango {
  display: flex;
  align-items: center;
  gap: 0.4rem;
}
.precio-rango span {
  color: #aaa;
}
.btn-limpiar {
  margin-top: 0.25rem;
  padding: 0.55rem;
  background: #f5f5f5;
  border: 1px solid #ddd;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.85rem;
  color: #555;
  transition: all 0.15s;
}
.btn-limpiar:hover {
  background: #fdecea;
  border-color: #e74c3c;
  color: #c0392b;
}
</style>
