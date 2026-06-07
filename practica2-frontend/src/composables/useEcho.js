import { ref, onMounted, onUnmounted } from 'vue'
import { getEcho } from '@/plugins/echo'
import { useAuthStore } from '@/stores/auth'

export function useAdminChannel() {
  const auth          = useAuthStore()
  const pedidosNuevos = ref([])
  const alertasStock  = ref([])
  let echo            = null

  const conectar = () => {
    echo = getEcho(auth.token)

    echo.private('admin-panel')
      .listen('.NuevoPedidoRecibido', (e) => {
        console.log('Evento recibido:', e)
        pedidosNuevos.value.unshift(e)
        setTimeout(() => {
          const idx = pedidosNuevos.value.indexOf(e)
          if (idx > -1) pedidosNuevos.value.splice(idx, 1)
        }, 10000)
      })
      .listen('.StockBajoAlerta', (e) => {
        console.log('Stock bajo:', e)
        alertasStock.value.unshift(e)
        setTimeout(() => {
          const idx = alertasStock.value.indexOf(e)
          if (idx > -1) alertasStock.value.splice(idx, 1)
        }, 15000)
      })
  }

  const desconectar = () => {
    if (echo) echo.leave('admin-panel')
  }

  onMounted(conectar)
  onUnmounted(desconectar)

  return { pedidosNuevos, alertasStock }
}
