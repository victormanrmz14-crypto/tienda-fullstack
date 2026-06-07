import { defineStore } from 'pinia'
import axios from 'axios'

const api = axios.create({
    baseURL: `${import.meta.env.VITE_API_URL || 'http://localhost:8000'}/api`,
})

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user:       null,
        token:      localStorage.getItem('token') || null,
        permisos:   { crear: false, editar: false, eliminar: false },
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        rol:      (state) => state.user?.rol ?? null,
        esAdmin:  (state) => state.user?.rol === 'admin',
        // Personal con acceso al panel: admin o editor (el cliente NO)
        esStaff:  (state) => ['admin', 'editor'].includes(state.user?.rol),
    },

    actions: {
        async register(data) {
            const res = await api.post('/register', data)
            this.token = res.data.token
            localStorage.setItem('token', this.token)
            await this.fetchUser()
        },

        async login(credentials) {
            const res = await api.post('/login', credentials)
            this.token = res.data.token
            localStorage.setItem('token', this.token)
            await this.fetchUser()
        },

        async logout() {
            // Intenta invalidar el token en el servidor, pero pase lo que pase
            // limpiamos la sesión local para que el logout siempre funcione.
            try {
                await api.post('/logout', {}, {
                    headers: { Authorization: `Bearer ${this.token}` }
                })
            } catch {
                // token ya expirado / sin red: ignoramos y limpiamos igual
            } finally {
                this.token    = null
                this.user     = null
                this.permisos = { crear: false, editar: false, eliminar: false }
                localStorage.removeItem('token')
            }
        },

        async fetchUser() {
            try {
                const res = await api.get('/me', {
                    headers: { Authorization: `Bearer ${this.token}` }
                })
                this.user = res.data
                this.permisos = res.data.permisos ?? { crear: false, editar: false, eliminar: false }
            } catch {
                this.token = null
                this.user  = null
                localStorage.removeItem('token')
            }
        },
    },
})