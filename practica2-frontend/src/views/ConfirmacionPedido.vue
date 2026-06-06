<template>
  <div class="confirmacion">
    <nav class="navbar">
      <RouterLink to="/">🛒 Mi Tienda</RouterLink>
      <RouterLink to="/catalogo">Seguir comprando</RouterLink>
    </nav>
    <div class="content">
      <h2>¡Gracias por tu compra!</h2>
      <p>Tu pedido <strong>#{{ pedidoId }}</strong> fue registrado exitosamente.</p>
      <PedidoEstado :pedido-id="pedidoId" />
    </div>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
import PedidoEstado from '@/components/PedidoEstado.vue'

const route    = useRoute()
const pedidoId = Number(route.params.id)
</script>

<style scoped>
.navbar {
  background: white;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.navbar a { text-decoration: none; color: #42b883; font-weight: bold; }
.content { max-width: 600px; margin: 4rem auto; text-align: center; }
h2 { font-size: 2rem; margin-bottom: 1rem; }
</style>
