<template>
  <div class="cart">
    <nav class="navbar">
      <RouterLink to="/">🛒 Tienda Full-Stack</RouterLink>
      <div class="nav-links">
        <RouterLink to="/catalogo">Catálogo</RouterLink>
        <CartIcon />
      </div>
    </nav>

    <div class="content">
      <h2>Tu Carrito</h2>

      <div v-if="carrito.items.length === 0" class="empty">
        <p>🛒 Tu carrito está vacío.</p>
        <RouterLink to="/catalogo" class="btn-primary">Ver Catálogo</RouterLink>
      </div>

      <div v-else>
        <div class="items">
          <div v-for="item in carrito.items" :key="item.id" class="item-card">
            <div class="item-info">
              <h4>{{ item.nombre }}</h4>
              <p class="precio-unit">${{ item.precio }} por unidad</p>
            </div>

            <div class="item-controls">
              <button @click="carrito.cambiarCantidad(item.id, item.cantidad - 1)">−</button>
              <span class="cantidad">{{ item.cantidad }}</span>
              <button @click="carrito.cambiarCantidad(item.id, item.cantidad + 1)">+</button>
            </div>

            <div class="item-subtotal">
              <span>${{ (item.precio * item.cantidad).toFixed(2) }}</span>
              <button class="btn-quitar" @click="carrito.quitar(item.id)">×</button>
            </div>
          </div>
        </div>

        <div class="resumen">
          <div class="total">
            <span>Total</span>
            <span class="total-precio">${{ carrito.totalPrecio.toFixed(2) }}</span>
          </div>
          <div class="acciones">
            <button class="btn-vaciar" @click="confirmarVaciar">
              Vaciar carrito
            </button>
            <button class="btn-comprar" @click="finalizarCompra">
              Finalizar compra
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCarritoStore } from '@/stores/carrito'
import CartIcon from '@/components/CartIcon.vue'

const carrito = useCarritoStore()

const confirmarVaciar = () => {
  if (confirm('¿Seguro que quieres vaciar el carrito?')) {
    carrito.vaciar()
  }
}

const finalizarCompra = () => {
  alert('¡Compra finalizada! Gracias por tu pedido 🎉')
  carrito.vaciar()
}
</script>

<style scoped>
.cart { min-height: 100vh; background: #f0f2f5; }
.navbar {
  background: white;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.navbar a { text-decoration: none; color: #42b883; font-weight: bold; font-size: 1.1rem; }
.nav-links { display: flex; gap: 1.5rem; align-items: center; }
.nav-links a { color: #333; font-size: 1rem; }
.content { padding: 2rem; max-width: 800px; margin: 0 auto; }
.content h2 { margin: 0 0 2rem; }
.empty { text-align: center; padding: 3rem; }
.empty p { font-size: 1.2rem; color: #999; margin-bottom: 1.5rem; }
.btn-primary {
  background: #42b883;
  color: white;
  padding: 0.8rem 2rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
}
.items { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem; }
.item-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  gap: 1rem;
}
.item-info { flex: 1; }
.item-info h4 { margin: 0 0 0.3rem; }
.precio-unit { margin: 0; color: #999; font-size: 0.9rem; }
.item-controls { display: flex; align-items: center; gap: 0.8rem; }
.item-controls button {
  width: 32px;
  height: 32px;
  border: 1px solid #ddd;
  background: white;
  border-radius: 6px;
  font-size: 1.2rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}
.item-controls button:hover { background: #f0f0f0; }
.cantidad { font-weight: bold; font-size: 1.1rem; min-width: 24px; text-align: center; }
.item-subtotal { display: flex; align-items: center; gap: 1rem; }
.item-subtotal span { font-weight: bold; color: #42b883; font-size: 1.1rem; }
.btn-quitar {
  background: none;
  border: none;
  color: #e74c3c;
  font-size: 1.4rem;
  cursor: pointer;
  padding: 0;
  line-height: 1;
}
.resumen {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
.total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  font-size: 1.1rem;
}
.total-precio { font-size: 1.5rem; font-weight: bold; color: #42b883; }
.acciones { display: flex; gap: 1rem; }
.btn-vaciar {
  flex: 1;
  padding: 0.8rem;
  border: 1px solid #e74c3c;
  background: white;
  color: #e74c3c;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
}
.btn-comprar {
  flex: 2;
  padding: 0.8rem;
  background: #42b883;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
}
</style>