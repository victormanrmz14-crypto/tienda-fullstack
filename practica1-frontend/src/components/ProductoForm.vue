<!-- src/components/ProductoForm.vue -->
<script setup>
import { ref, reactive, watch, computed } from 'vue'
import { createProducto, updateProducto } from '../services/productoService'

const props = defineProps({ producto: { type: Object, default: null } })
const emit = defineEmits(['guardado', 'cancelar'])

const vacio = () => ({ nombre: '', descripcion: '', precio: '', stock: 0 })
const form = reactive(vacio())
const mensaje = ref('')
const enviando = ref(false)

const modoEdicion = computed(() => props.producto !== null)

// Si el padre pasa un producto → llena el form; si pasa null → lo vacía
watch(() => props.producto, (nuevo) => {
  Object.assign(form, nuevo ?? vacio())
}, { immediate: true })

const guardar = async () => {
  enviando.value = true
  mensaje.value = ''
  try {
    if (modoEdicion.value) {
      await updateProducto(props.producto.id, form)
      mensaje.value = '✅ Producto actualizado'
    } else {
      await createProducto(form)
      mensaje.value = '✅ Producto creado'
    }
    Object.assign(form, vacio())
    emit('guardado')
  } catch (e) {
    if (e.response?.status === 422) {
      mensaje.value = '❌ ' + Object.values(e.response.data.errors).flat().join(', ')
    } else {
      mensaje.value = '❌ Error al guardar.'
    }
  } finally {
    enviando.value = false
  }
}
</script>

<template>
  <form @submit.prevent="guardar"
        style="display: grid; gap: .5rem; margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #ddd; border-radius: 8px;">
    <h2>{{ modoEdicion ? 'Editar producto' : 'Nuevo producto' }}</h2>

    <input v-model="form.nombre" placeholder="Nombre" required />
    <textarea v-model="form.descripcion" placeholder="Descripción"></textarea>
    <input v-model.number="form.precio" type="number" step="0.01" placeholder="Precio" required />
    <input v-model.number="form.stock" type="number" placeholder="Stock" required />

    <div>
      <button type="submit" :disabled="enviando">
        {{ enviando ? 'Guardando…' : (modoEdicion ? 'Actualizar' : 'Crear') }}
      </button>
      <button v-if="modoEdicion" type="button" @click="emit('cancelar')">Cancelar</button>
    </div>

    <p v-if="mensaje">{{ mensaje }}</p>
  </form>
</template>