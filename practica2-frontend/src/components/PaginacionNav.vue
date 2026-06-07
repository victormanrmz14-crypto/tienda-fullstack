<script setup>
import { computed } from 'vue'

const props = defineProps({
  meta: { type: Object, required: true },
})
const emit = defineEmits(['cambio-pagina'])

const actual = computed(() => props.meta.current_page ?? 1)
const ultima = computed(() => props.meta.last_page ?? 1)

// Rango de máximo 5 páginas centrado en la página actual
const paginas = computed(() => {
  const total = ultima.value
  const max = 5
  let inicio = Math.max(1, actual.value - Math.floor(max / 2))
  let fin = Math.min(total, inicio + max - 1)
  inicio = Math.max(1, fin - max + 1)

  const rango = []
  for (let i = inicio; i <= fin; i++) rango.push(i)
  return rango
})

const ir = (pagina) => {
  if (pagina < 1 || pagina > ultima.value || pagina === actual.value) return
  emit('cambio-pagina', pagina)
}
</script>

<template>
  <nav v-if="meta.last_page > 1" class="paginacion">
    <div class="botones">
      <button :disabled="actual === 1" @click="ir(1)" title="Primera">«</button>
      <button :disabled="actual === 1" @click="ir(actual - 1)" title="Anterior">‹</button>

      <button
        v-for="p in paginas"
        :key="p"
        :class="{ activo: p === actual }"
        @click="ir(p)"
      >
        {{ p }}
      </button>

      <button :disabled="actual === ultima" @click="ir(actual + 1)" title="Siguiente">›</button>
      <button :disabled="actual === ultima" @click="ir(ultima)" title="Última">»</button>
    </div>

    <p class="info">
      Página {{ actual }} de {{ ultima }} ({{ meta.total }} productos)
    </p>
  </nav>
</template>

<style scoped>
.paginacion {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.9rem;
  margin: 2.5rem 0 1rem;
}
.botones {
  display: flex;
  gap: 0.4rem;
  flex-wrap: wrap;
  justify-content: center;
}
.botones button {
  min-width: 42px;
  height: 42px;
  padding: 0 0.7rem;
  border: 1.5px solid var(--border);
  background: #fff;
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-size: 0.92rem;
  font-weight: 600;
  color: var(--text);
  transition: var(--transition);
}
.botones button:hover:not(:disabled):not(.activo) {
  border-color: var(--primary);
  color: var(--primary);
  transform: translateY(-2px);
}
.botones button.activo {
  background: var(--gradient-soft);
  border-color: transparent;
  color: #fff;
  font-weight: 700;
  box-shadow: var(--shadow-primary);
}
.botones button:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.info {
  margin: 0;
  font-size: 0.85rem;
  color: var(--text-muted);
}
</style>
