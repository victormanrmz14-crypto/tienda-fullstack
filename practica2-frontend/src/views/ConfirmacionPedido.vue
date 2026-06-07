<template>
  <div class="confirmacion">
    <AppNavbar />

    <div class="content">
      <div class="success-card">
        <div class="check-circle">✓</div>
        <h2>¡Gracias por tu compra!</h2>
        <p class="sub">
          Tu pedido <strong>#{{ pedidoId }}</strong> fue registrado exitosamente.
        </p>

        <PedidoEstado :pedido-id="pedidoId" />

        <div class="acciones">
          <RouterLink to="/catalogo" class="btn-primary">Seguir comprando</RouterLink>
          <RouterLink to="/" class="btn-ghost">Ir al inicio</RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
import AppNavbar from '@/components/AppNavbar.vue'
import PedidoEstado from '@/components/PedidoEstado.vue'

const route    = useRoute()
const pedidoId = Number(route.params.id)
</script>

<style scoped>
.confirmacion { min-height: 100vh; background: var(--bg); }
.content {
  max-width: 560px;
  margin: 0 auto;
  padding: 3rem 1.5rem;
}
.success-card {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow);
  padding: 3rem 2rem;
  text-align: center;
  animation: floatUp 0.6s ease both;
}
.check-circle {
  width: 80px;
  height: 80px;
  margin: 0 auto 1.5rem;
  border-radius: 50%;
  background: var(--success);
  color: #fff;
  font-size: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 10px 28px rgba(72, 187, 120, 0.4);
  animation: pop 0.5s ease 0.2s both;
}
.success-card h2 { margin: 0 0 0.6rem; font-size: 1.8rem; font-weight: 800; }
.sub { color: var(--text-muted); margin: 0 0 0.5rem; }
.acciones { display: flex; gap: 0.8rem; justify-content: center; flex-wrap: wrap; margin-top: 1rem; }
.btn-primary {
  background: var(--gradient-soft);
  color: #fff;
  padding: 0.8rem 1.8rem;
  border-radius: var(--radius-pill);
  font-weight: 700;
  box-shadow: var(--shadow-primary);
  transition: var(--transition);
}
.btn-primary:hover { transform: translateY(-2px); }
.btn-ghost {
  background: #fff;
  color: var(--primary);
  border: 1.5px solid var(--border);
  padding: 0.8rem 1.8rem;
  border-radius: var(--radius-pill);
  font-weight: 700;
  transition: var(--transition);
}
.btn-ghost:hover { border-color: var(--primary); background: var(--primary-soft); }
</style>
