import { reactive, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const DEFAULTS = {
  busqueda: '',
  categoria_id: '',
  precio_min: '',
  precio_max: '',
  orden: 'nombre',
  dir: 'asc',
  pagina: 1,
}

export function useFiltros() {
  const route = useRoute()
  const router = useRouter()

  // Valores iniciales desde la URL (para enlaces compartibles)
  const q = route.query
  const filtros = reactive({
    busqueda: q.busqueda ?? DEFAULTS.busqueda,
    categoria_id: q.categoria_id ?? DEFAULTS.categoria_id,
    precio_min: q.precio_min ?? DEFAULTS.precio_min,
    precio_max: q.precio_max ?? DEFAULTS.precio_max,
    orden: q.orden ?? DEFAULTS.orden,
    dir: q.dir ?? DEFAULTS.dir,
    pagina: q.page ? Number(q.page) : DEFAULTS.pagina,
  })

  // Omite valores vacíos o iguales al default para mantener la URL limpia
  const limpiarValor = (valor, def) =>
    valor === '' || valor === null || valor === undefined || valor === def
      ? undefined
      : valor

  // Sincroniza filtros → URL
  watch(
    filtros,
    () => {
      router.push({
        query: {
          busqueda: limpiarValor(filtros.busqueda, DEFAULTS.busqueda),
          categoria_id: limpiarValor(filtros.categoria_id, DEFAULTS.categoria_id),
          precio_min: limpiarValor(filtros.precio_min, DEFAULTS.precio_min),
          precio_max: limpiarValor(filtros.precio_max, DEFAULTS.precio_max),
          orden: limpiarValor(filtros.orden, DEFAULTS.orden),
          dir: limpiarValor(filtros.dir, DEFAULTS.dir),
          page: limpiarValor(filtros.pagina, DEFAULTS.pagina),
        },
      })
    },
    { deep: true }
  )

  // Restablece todos los campos a sus valores por defecto
  const limpiar = () => {
    Object.assign(filtros, DEFAULTS)
  }

  return { filtros, limpiar }
}
