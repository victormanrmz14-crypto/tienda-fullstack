<template>
  <RouterLink to="/carrito" class="cart-link" aria-label="Carrito">
    <span class="cart-icon">🛒</span>
    <Transition name="badge-pop">
      <span v-if="carrito.totalItems > 0" :key="carrito.totalItems" class="badge">
        {{ carrito.totalItems }}
      </span>
    </Transition>
  </RouterLink>
</template>

<script setup>
import { useCarritoStore } from '@/stores/carrito'

const carrito = useCarritoStore()
</script>

<style scoped>
.cart-link {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  border-radius: 50%;
  margin-left: 0.3rem;
  transition: var(--transition);
}
.cart-link:hover {
  background: var(--primary-soft);
  transform: translateY(-1px);
}
.cart-icon {
  font-size: 1.35rem;
  line-height: 1;
}
.badge {
  position: absolute;
  top: 2px;
  right: 0;
  min-width: 19px;
  height: 19px;
  padding: 0 5px;
  background: var(--gradient-soft);
  color: #fff;
  border-radius: var(--radius-pill);
  font-size: 0.7rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: var(--shadow-secondary);
  border: 2px solid #fff;
}
.badge-pop-enter-active { animation: pop 0.35s ease; }
</style>
