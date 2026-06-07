import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { useAuthStore } from '@/stores/auth'

window.Pusher = Pusher

const createEcho = () => {
  const auth = useAuthStore()
  return new Echo({
    broadcaster:       'reverb',
    key:               import.meta.env.VITE_REVERB_APP_KEY,
    wsHost:            import.meta.env.VITE_REVERB_HOST,
    wsPort:            import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort:           import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS:          false,
    enabledTransports: ['ws', 'wss'],
    authEndpoint:      'http://localhost:8000/api/broadcasting/auth',
    auth: {
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept:        'application/json',
      }
    }
  })
}

export default createEcho()
