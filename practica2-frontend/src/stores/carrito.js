import { defineStore } from 'pinia'

export const useCarritoStore = defineStore('carrito', {
    state: () => ({
        items: JSON.parse(localStorage.getItem('carrito') || '[]')
    }),

    getters: {
        totalItems:  (state) => state.items.reduce((s, i) => s + i.cantidad, 0),
        totalPrecio: (state) => state.items.reduce((s, i) => s + i.precio * i.cantidad, 0),
        cantidadDeProducto: (state) => (id) =>
            state.items.find(i => i.id === id)?.cantidad || 0,
    },

    actions: {
        agregar(producto) {
            const existente = this.items.find(i => i.id === producto.id)
            if (existente) {
                existente.cantidad++
            } else {
                this.items.push({ ...producto, cantidad: 1 })
            }
        },

        quitar(id) {
            this.items = this.items.filter(i => i.id !== id)
        },

        cambiarCantidad(id, cantidad) {
            if (cantidad <= 0) {
                this.quitar(id)
                return
            }
            const item = this.items.find(i => i.id === id)
            if (item) item.cantidad = cantidad
        },

        vaciar() {
            this.items = []
        },
    }
})