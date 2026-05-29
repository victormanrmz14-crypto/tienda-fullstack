import { defineStore } from 'pinia'
import axios from 'axios'

const api = axios.create({
    baseURL: 'http://localhost:8000/api',
})

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user:  null,
        token: localStorage.getItem('token') || null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
    },

    actions: {
        async register(data) {
            const res = await api.post('/register', data)
            this.token = res.data.token
            this.user  = res.data.user
            localStorage.setItem('token', this.token)
        },

        async login(credentials) {
            const res = await api.post('/login', credentials)
            this.token = res.data.token
            this.user  = res.data.user
            localStorage.setItem('token', this.token)
        },

        async logout() {
            await api.post('/logout', {}, {
                headers: { Authorization: `Bearer ${this.token}` }
            })
            this.token = null
            this.user  = null
            localStorage.removeItem('token')
        },

        async fetchUser() {
            const res = await api.get('/me', {
                headers: { Authorization: `Bearer ${this.token}` }
            })
            this.user = res.data.user
        },
    },
})