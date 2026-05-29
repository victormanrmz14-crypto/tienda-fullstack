<template>
  <div class="auth-container">
    <div class="auth-card">
      <h2>Crear Cuenta</h2>

      <div v-if="error" class="error">{{ error }}</div>

      <div class="form-group">
        <label>Nombre</label>
        <input v-model="form.name" type="text" placeholder="Tu nombre" />
      </div>

      <div class="form-group">
        <label>Email</label>
        <input v-model="form.email" type="email" placeholder="correo@ejemplo.com" />
      </div>

      <div class="form-group">
        <label>Contraseña</label>
        <input v-model="form.password" type="password" placeholder="••••••••" />
      </div>

      <div class="form-group">
        <label>Confirmar Contraseña</label>
        <input v-model="form.password_confirmation" type="password" placeholder="••••••••" />
      </div>

      <button @click="handleRegister" :disabled="loading">
        {{ loading ? 'Registrando...' : 'Registrarse' }}
      </button>

      <p>¿Ya tienes cuenta? <RouterLink to="/login">Inicia sesión</RouterLink></p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth    = useAuthStore()
const router  = useRouter()
const loading = ref(false)
const error   = ref('')

const form = reactive({
  name:                  '',
  email:                 '',
  password:              '',
  password_confirmation: '',
})

const handleRegister = async () => {
  error.value   = ''
  loading.value = true
  try {
    await auth.register(form)
    router.push('/dashboard')
  } catch (e) {
    error.value = e.response?.data?.message || 'Error al registrarse'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f0f2f5;
}
.auth-card {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
h2 { text-align: center; margin: 0; }
.form-group { display: flex; flex-direction: column; gap: 0.4rem; }
input {
  padding: 0.6rem 0.8rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
}
button {
  padding: 0.7rem;
  background: #42b883;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
}
button:disabled { opacity: 0.6; cursor: not-allowed; }
.error {
  background: #fee;
  color: #c00;
  padding: 0.6rem;
  border-radius: 8px;
  font-size: 0.9rem;
}
p { text-align: center; margin: 0; font-size: 0.9rem; }
</style>