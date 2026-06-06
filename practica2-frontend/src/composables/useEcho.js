import { ref, onMounted, onUnmounted } from 'vue'
import echo from '@/plugins/echo'
import { useAuthStore } from '@/stores/auth'

export function useAdminChannel() {
  const auth          = useAuthStore()
  const pedidosNuevos = ref([])
  const alertasStock  = ref([])
  let channel         = null

  const conectar = () => {
    // Actualiza el token en Echo antes de conectar
    if (echo.options.auth) {
      echo.options.auth.headers = {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json'
      }
    }

    channel = echo.private('admin-panel')
      .listen('NuevoPedidoRecibido', (e) => {
        pedidosNuevos.value.unshift(e)
        setTimeout(() => {
          const idx = pedidosNuevos.value.indexOf(e)
          if (idx > -1) pedidosNuevos.value.splice(idx, 1)
        }, 10000)
      })
      .listen('StockBajoAlerta', (e) => {
        alertasStock.value.unshift(e)
        setTimeout(() => {
          const idx = alertasStock.value.indexOf(e)
          if (idx > -1) alertasStock.value.splice(idx, 1)
        }, 15000)
      })
  }

  const desconectar = () => {
    if (channel) {
      echo.leave('admin-panel')
    }
  }

  onMounted(conectar)
  onUnmounted(desconectar)

  return { pedidosNuevos, alertasStock }
}
