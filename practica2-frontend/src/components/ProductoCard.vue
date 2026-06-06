<template>
  <div class="producto-card">
    <h4>{{ producto.nombre }}</h4>
    <span>{{ producto.precio }}</span>
    <span>{{ producto.stock }}</span>
    <button data-test="btn-agregar" @click="$emit('agregar-carrito', producto)">
      Agregar al carrito
    </button>
  </div>
</template>

<script setup>
defineProps({
  producto: { type: Object, required: true },
})

defineEmits(['agregar-carrito'])
</script>
