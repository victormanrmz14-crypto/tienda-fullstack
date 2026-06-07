<template>
  <div class="pedido-estado">
    <div v-if="!emailListo" class="estado procesando">
      <span>⏳</span> Procesando tu pedido...
    </div>
    <div v-else class="estado listo">
      ✅ ¡Pedido confirmado! Revisa tu correo.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const props      = defineProps({ pedidoId: { type: Number, required: true } })
const auth       = useAuthStore()
const emailListo = ref(false)
let intervalo    = null

onMounted(() => {
  intervalo = setInterval(async () => {
    try {
      const { data } = await axios.get(
        `${import.meta.env.VITE_API_URL || 'http://localhost:8000'}/api/pedidos/${props.pedidoId}`,
        { headers: { Authorization: `Bearer ${auth.token}` } }
      )
      emailListo.value = !!data.email_enviado_at
      if (emailListo.value) clearInterval(intervalo)
    } catch (e) {
      console.error(e)
    }
  }, 3000)
})

onUnmounted(() => clearInterval(intervalo))
</script>

<style scoped>
.pedido-estado { text-align: center; padding: 2rem; }
.estado {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem 2rem;
  border-radius: var(--radius);
  font-size: 1.1rem;
  font-weight: 600;
}
.procesando { background: var(--warning-soft); color: #9c4221; }
.listo      { background: var(--success-soft); color: #276749; }
</style>
