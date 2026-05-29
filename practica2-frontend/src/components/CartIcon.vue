<template>
  <RouterLink to="/carrito" class="cart-link">
    <span class="cart-icon">
      🛒
      <span v-if="carrito.totalItems > 0" class="badge">
        {{ carrito.totalItems }}
      </span>
    </span>
  </RouterLink>
</template>

<script setup>
import { useCarritoStore } from '@/stores/carrito'

const carrito = useCarritoStore()
</script>

<style scoped>
.cart-link { text-decoration: none; position: relative; }
.cart-icon { font-size: 1.5rem; position: relative; display: inline-block; }
.badge {
  position: absolute;
  top: -8px;
  right: -10px;
  background: #e74c3c;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  font-size: 0.7rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}
</style>