<template>
  <div class="notificaciones-container">

    <!-- Toasts de nuevos pedidos -->
    <TransitionGroup name="toast" tag="div" class="toasts-wrapper">
      <div
        v-for="pedido in pedidosNuevos"
        :key="pedido.id"
        class="toast toast-pedido"
      >
        <span class="toast-icon">🛒</span>
        <div class="toast-body">
          <strong>Nuevo pedido #{{ pedido.id }}</strong>
          <p>{{ pedido.cliente }} — ${{ pedido.total }}</p>
          <small>{{ pedido.created_at }}</small>
        </div>
      </div>
    </TransitionGroup>

    <!-- Alertas de stock bajo -->
    <TransitionGroup name="toast" tag="div" class="alertas-wrapper">
      <div
        v-for="alerta in alertasStock"
        :key="alerta.producto_id"
        class="toast toast-stock"
      >
        <span class="toast-icon">⚠️</span>
        <div class="toast-body">
          <strong>Stock bajo</strong>
          <p>{{ alerta.nombre }}</p>
          <small>Solo {{ alerta.stock_actual }} unidades restantes</small>
        </div>
      </div>
    </TransitionGroup>

  </div>
</template>

<script setup>
import { useAdminChannel } from '@/composables/useEcho'
const { pedidosNuevos, alertasStock } = useAdminChannel()
</script>

<style scoped>
.notificaciones-container {
  position: fixed;
  top: 1rem;
  right: 1rem;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  max-width: 320px;
}

.toasts-wrapper,
.alertas-wrapper {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.toast {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 1rem;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  animation: slideIn 0.3s ease;
}

.toast-pedido { background: #e8f5e9; border-left: 4px solid #42b883; }
.toast-stock  { background: #fff3cd; border-left: 4px solid #f59e0b; }

.toast-icon { font-size: 1.5rem; }
.toast-body { flex: 1; }
.toast-body strong { display: block; font-size: 0.95rem; margin-bottom: 0.2rem; }
.toast-body p { margin: 0; font-size: 0.85rem; color: #555; }
.toast-body small { font-size: 0.75rem; color: #999; }

/* Animaciones TransitionGroup */
.toast-enter-active { transition: all 0.3s ease; }
.toast-leave-active { transition: all 0.5s ease; }
.toast-enter-from  { opacity: 0; transform: translateX(100%); }
.toast-leave-to    { opacity: 0; transform: translateX(100%); }
</style>
