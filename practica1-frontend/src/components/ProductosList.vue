<!-- src/components/ProductosList.vue -->
<script setup>
defineProps({ productos: { type: Array, default: () => [] } })
defineEmits(['editar', 'eliminar'])
</script>

<template>
  <table border="1" cellpadding="8" style="border-collapse: collapse; width: 100%; margin-top: 1rem;">
    <thead>
    <tr>
      <th>ID</th><th>Nombre</th><th>Descripción</th>
      <th>Precio</th><th>Stock</th><th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    <tr v-if="productos.length === 0">
      <td colspan="6" style="text-align: center;">No hay productos.</td>
    </tr>
    <tr v-for="p in productos" :key="p.id">
      <td>{{ p.id }}</td>
      <td>{{ p.nombre }}</td>
      <td>{{ p.descripcion }}</td>
      <td>${{ Number(p.precio).toFixed(2) }}</td>
      <td>{{ p.stock }}</td>
      <td>
        <button @click="$emit('editar', p)">Editar</button>
        <button @click="$emit('eliminar', p.id)">Eliminar</button>
      </td>
    </tr>
    </tbody>
  </table>
</template>