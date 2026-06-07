import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const API_VERSION = import.meta.env.VITE_API_VERSION || 'v1'
const API_URL     = import.meta.env.VITE_API_URL     || 'http://localhost:8000'

const api = axios.create({
  baseURL: `${API_URL}/api/${API_VERSION}`,
})

api.interceptors.request.use((config) => {
  const auth = useAuthStore()
  if (auth.token) {
    config.headers.Authorization = `Bearer ${auth.token}`
  }
  return config
})

export default api
