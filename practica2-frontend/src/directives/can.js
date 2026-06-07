import { useAuthStore } from '@/stores/auth'
import { watch } from 'vue'

export const vCan = {
  mounted(el, binding) {
    const auth    = useAuthStore()
    const permiso = binding.value

    const updateVisibility = () => {
      el.style.display = auth.permisos[permiso] ? '' : 'none'
    }

    updateVisibility()

    // Actualizar cuando los permisos cambien
    watch(() => auth.permisos, updateVisibility, { deep: true })
  }
}
