<template>
  <div class="cart-page">
    <AppNavbar />

    <div class="content">
      <header class="cart-head">
        <h1>Tu carrito</h1>
        <p v-if="carrito.items.length">{{ carrito.totalItems }} artículo(s) en tu pedido</p>
      </header>

      <!-- Vacío -->
      <div v-if="carrito.items.length === 0" class="empty">
        <div class="empty-icon">🛒</div>
        <h2>Tu carrito está vacío</h2>
        <p>Agrega productos desde el catálogo para empezar tu pedido.</p>
        <RouterLink to="/catalogo" class="btn-primary">Explorar catálogo</RouterLink>
      </div>

      <!-- Con items -->
      <div v-else class="checkout-layout">
        <!-- Lista de items -->
        <section class="items-col">
          <TransitionGroup name="item" tag="div" class="items">
            <article v-for="item in carrito.items" :key="item.id" class="item-card">
              <div class="item-thumb">
                <img v-if="item.imagen_url" :src="item.imagen_url" :alt="item.nombre" />
                <span v-else>🖼️</span>
              </div>

              <div class="item-info">
                <h4>{{ item.nombre }}</h4>
                <p class="precio-unit">{{ formato(item.precio) }} c/u</p>
              </div>

              <div class="item-qty">
                <button aria-label="Restar" @click="carrito.cambiarCantidad(item.id, item.cantidad - 1)">−</button>
                <span class="cantidad">{{ item.cantidad }}</span>
                <button aria-label="Sumar" @click="carrito.cambiarCantidad(item.id, item.cantidad + 1)">+</button>
              </div>

              <div class="item-subtotal">
                <strong>{{ formato(item.precio * item.cantidad) }}</strong>
                <button class="btn-quitar" aria-label="Quitar" @click="carrito.quitar(item.id)">🗑️</button>
              </div>
            </article>
          </TransitionGroup>

          <RouterLink to="/catalogo" class="seguir">← Seguir comprando</RouterLink>
        </section>

        <!-- Resumen del pedido -->
        <aside class="resumen-col">
          <div class="resumen">
            <h3>Resumen del pedido</h3>
            <div class="linea">
              <span>Subtotal</span>
              <span>{{ formato(carrito.totalPrecio) }}</span>
            </div>
            <div class="linea">
              <span>Envío</span>
              <span class="gratis">Gratis</span>
            </div>
            <div class="linea total">
              <span>Total</span>
              <span class="total-precio">{{ formato(carrito.totalPrecio) }}</span>
            </div>

            <p v-if="errorCompra" class="error-compra">⚠️ {{ errorCompra }}</p>

            <button class="btn-comprar" :disabled="procesando" @click="finalizarCompra">
              {{ procesando ? 'Procesando…' : 'Finalizar compra 🎉' }}
            </button>
            <button class="btn-vaciar" :disabled="procesando" @click="confirmarVaciar">Vaciar carrito</button>

            <p class="seguro">🔒 Pago 100% seguro y cifrado</p>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useCarritoStore } from '@/stores/carrito'
import { useAuthStore } from '@/stores/auth'
import AppNavbar from '@/components/AppNavbar.vue'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'
const carrito = useCarritoStore()
const auth    = useAuthStore()
const router  = useRouter()

const procesando = ref(false)
const errorCompra = ref('')

const formato = (precio) =>
  new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(precio)

const confirmarVaciar = () => {
  if (confirm('¿Seguro que quieres vaciar el carrito?')) {
    carrito.vaciar()
  }
}

const finalizarCompra = async () => {
  errorCompra.value = ''

  // Se necesita iniciar sesión para comprar
  if (!auth.isAuthenticated) {
    router.push({ path: '/login', query: { redirect: '/carrito' } })
    return
  }
  if (carrito.items.length === 0) return

  procesando.value = true
  try {
    const { data } = await axios.post(
      `${API_URL}/api/pedidos`,
      {
        items: carrito.items.map((i) => ({
          producto_id: i.id,
          cantidad:    i.cantidad,
          precio:      i.precio,
        })),
      },
      { headers: { Authorization: `Bearer ${auth.token}` } }
    )
    carrito.vaciar()
    router.push(`/pedidos/${data.pedido_id}/confirmacion`)
  } catch (e) {
    const errores = e.response?.data?.errors
    errorCompra.value = errores
      ? Object.values(errores)[0][0]
      : (e.response?.data?.message || 'No se pudo procesar la compra. Intenta de nuevo.')
  } finally {
    procesando.value = false
  }
}
</script>

<style scoped>
.cart-page { min-height: 100vh; background: var(--bg); }
.content { max-width: 1100px; margin: 0 auto; padding: 2rem 1.5rem 4rem; }
.cart-head { margin-bottom: 2rem; }
.cart-head h1 { margin: 0; font-size: 2rem; font-weight: 800; letter-spacing: -0.5px; }
.cart-head p { margin: 0.3rem 0 0; color: var(--text-muted); }

/* Vacío */
.empty {
  text-align: center;
  padding: 4rem 2rem;
  background: #fff;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
}
.empty-icon { font-size: 4rem; margin-bottom: 0.5rem; animation: floatUp 0.6s ease both; }
.empty h2 { margin: 0 0 0.5rem; }
.empty p { color: var(--text-muted); margin: 0 0 1.8rem; }
.btn-primary {
  display: inline-block;
  background: var(--gradient-soft);
  color: #fff;
  padding: 0.85rem 2rem;
  border-radius: var(--radius-pill);
  font-weight: 700;
  box-shadow: var(--shadow-primary);
  transition: var(--transition);
}
.btn-primary:hover { transform: translateY(-3px); }

/* Checkout */
.checkout-layout {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 1.8rem;
  align-items: start;
}
.items { display: flex; flex-direction: column; gap: 1rem; }
.item-card {
  background: #fff;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  padding: 1rem 1.2rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: var(--shadow-sm);
  transition: var(--transition);
}
.item-card:hover { box-shadow: var(--shadow); }
.item-thumb {
  width: 64px;
  height: 64px;
  flex-shrink: 0;
  border-radius: var(--radius-sm);
  background: linear-gradient(135deg, #f1f0ff, #ffe9ee);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.6rem;
  overflow: hidden;
}
.item-thumb img { width: 100%; height: 100%; object-fit: cover; }
.item-info { flex: 1; min-width: 0; }
.item-info h4 { margin: 0 0 0.25rem; font-size: 1rem; }
.precio-unit { margin: 0; color: var(--text-muted); font-size: 0.85rem; }
.item-qty {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  background: var(--light);
  border-radius: var(--radius-pill);
  padding: 0.25rem;
}
.item-qty button {
  width: 30px;
  height: 30px;
  border: none;
  background: #fff;
  border-radius: 50%;
  font-size: 1.1rem;
  cursor: pointer;
  color: var(--primary);
  box-shadow: var(--shadow-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: var(--transition);
}
.item-qty button:hover { background: var(--primary); color: #fff; }
.cantidad { font-weight: 700; min-width: 26px; text-align: center; }
.item-subtotal { display: flex; flex-direction: column; align-items: flex-end; gap: 0.4rem; }
.item-subtotal strong { color: var(--primary); font-size: 1.05rem; }
.btn-quitar {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  opacity: 0.6;
  transition: var(--transition);
  padding: 0;
}
.btn-quitar:hover { opacity: 1; transform: scale(1.15); }
.seguir {
  display: inline-block;
  margin-top: 1.2rem;
  color: var(--primary);
  font-weight: 700;
  transition: var(--transition);
}
.seguir:hover { transform: translateX(-3px); }

/* Resumen */
.resumen {
  background: #fff;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  padding: 1.6rem;
  box-shadow: var(--shadow);
  position: sticky;
  top: 5.5rem;
}
.resumen h3 { margin: 0 0 1.2rem; font-size: 1.2rem; font-weight: 800; }
.linea {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.55rem 0;
  color: var(--text-muted);
  font-size: 0.95rem;
}
.gratis { color: var(--success); font-weight: 700; }
.linea.total {
  border-top: 1px dashed var(--border);
  margin-top: 0.5rem;
  padding-top: 1rem;
  color: var(--text);
  font-weight: 700;
  font-size: 1.05rem;
}
.total-precio { font-size: 1.5rem; font-weight: 800; color: var(--primary); }
.btn-comprar {
  width: 100%;
  margin-top: 1.2rem;
  padding: 0.95rem;
  background: var(--gradient-soft);
  color: #fff;
  border: none;
  border-radius: var(--radius-sm);
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow: var(--shadow-primary);
  transition: var(--transition);
}
.btn-comprar:hover:not(:disabled) { transform: translateY(-2px); filter: brightness(1.05); }
.btn-comprar:disabled { opacity: 0.7; cursor: not-allowed; }
.error-compra {
  background: var(--danger-soft);
  color: #c53030;
  padding: 0.7rem 0.9rem;
  border-radius: var(--radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  margin: 1rem 0 0;
  border-left: 3px solid var(--danger);
}
.btn-vaciar {
  width: 100%;
  margin-top: 0.7rem;
  padding: 0.75rem;
  background: #fff;
  color: var(--danger);
  border: 1.5px solid var(--danger-soft);
  border-radius: var(--radius-sm);
  font-size: 0.92rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
}
.btn-vaciar:hover { background: var(--danger-soft); border-color: var(--danger); }
.seguro { text-align: center; margin: 1rem 0 0; font-size: 0.8rem; color: var(--text-soft); }

/* Animaciones de items */
.item-enter-active { transition: all 0.4s ease; }
.item-leave-active { transition: all 0.4s ease; position: absolute; width: 100%; }
.item-enter-from { opacity: 0; transform: translateX(-30px); }
.item-leave-to { opacity: 0; transform: translateX(40px); }
.item-move { transition: transform 0.4s ease; }

@media (max-width: 820px) {
  .checkout-layout { grid-template-columns: 1fr; }
  .resumen { position: static; }
}
</style>
