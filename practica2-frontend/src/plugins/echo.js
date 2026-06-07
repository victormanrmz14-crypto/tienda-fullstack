import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

let echoInstance = null

export const getEcho = (token) => {
  if (echoInstance) {
    echoInstance.disconnect()
  }
  echoInstance = new Echo({
    broadcaster:       'reverb',
    key:               import.meta.env.VITE_REVERB_APP_KEY,
    wsHost:            import.meta.env.VITE_REVERB_HOST,
    wsPort:            import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort:           import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS:          false,
    enabledTransports: ['ws', 'wss'],
    authEndpoint:      `${import.meta.env.VITE_API_URL || 'http://localhost:8000'}/api/broadcasting/auth`,
    auth: {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept:        'application/json',
      }
    }
  })
  return echoInstance
}

export default getEcho
