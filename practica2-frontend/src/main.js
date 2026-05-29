import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { useCarritoStore } from '@/stores/carrito'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Sincronizar carrito con localStorage automáticamente
const carrito = useCarritoStore()
carrito.$subscribe((mutation, state) => {
    localStorage.setItem('carrito', JSON.stringify(state.items))
})

app.mount('#app')