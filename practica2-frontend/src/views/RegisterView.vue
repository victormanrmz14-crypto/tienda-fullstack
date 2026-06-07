<template>
  <div class="auth-split">
    <!-- Panel decorativo -->
    <aside class="brand-panel">
      <div class="brand-bg"></div>
      <RouterLink to="/" class="brand-top">🛍️ Tienda</RouterLink>
      <div class="brand-content">
        <h1>Crea tu<br />cuenta 🚀</h1>
        <p>Únete a nuestra plataforma y empieza a gestionar tu tienda en minutos. Es rápido y gratuito.</p>
        <ul class="brand-features">
          <li><span>✓</span> Registro en segundos</li>
          <li><span>✓</span> Acceso al panel de control</li>
          <li><span>✓</span> Soporte en tiempo real</li>
        </ul>
      </div>
      <p class="brand-foot">© Tienda Full-Stack</p>
    </aside>

    <!-- Formulario -->
    <main class="form-panel">
      <form class="auth-card" @submit.prevent="handleRegister">
        <RouterLink to="/" class="form-brand-mobile">🛍️ Tienda</RouterLink>
        <h2>Crear cuenta</h2>
        <p class="form-sub">Completa tus datos para empezar</p>

        <Transition name="shake">
          <div v-if="error" class="error">⚠️ {{ error }}</div>
        </Transition>

        <div class="float-field">
          <input id="name" v-model="form.name" type="text" placeholder=" " required />
          <label for="name">Nombre completo</label>
        </div>

        <div class="float-field">
          <input id="email" v-model="form.email" type="email" placeholder=" " required />
          <label for="email">Correo electrónico</label>
        </div>

        <div class="float-row">
          <div class="float-field">
            <input id="password" v-model="form.password" type="password" placeholder=" " required />
            <label for="password">Contraseña</label>
          </div>
          <div class="float-field">
            <input id="password_confirmation" v-model="form.password_confirmation" type="password" placeholder=" " required />
            <label for="password_confirmation">Confirmar</label>
          </div>
        </div>

        <button type="submit" class="btn-submit" :disabled="loading">
          <span v-if="loading" class="spinner"></span>
          {{ loading ? 'Registrando...' : 'Crear cuenta' }}
        </button>

        <p class="alt">¿Ya tienes cuenta? <RouterLink to="/login">Inicia sesión</RouterLink></p>
      </form>
    </main>
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
    // Un usuario recién registrado es cliente → va a la tienda, no al panel
    router.push('/')
  } catch (e) {
    // Muestra el primer error de validación de Laravel si lo hay
    const errores = e.response?.data?.errors
    error.value = errores
      ? Object.values(errores)[0][0]
      : (e.response?.data?.message || 'Error al registrarse')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.auth-split {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1fr 1fr;
}

/* ===== Panel decorativo ===== */
.brand-panel {
  position: relative;
  overflow: hidden;
  color: #fff;
  padding: 2.5rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.brand-bg {
  position: absolute;
  inset: -20%;
  background: linear-gradient(135deg, #FF6584, #8B85FF, #6C63FF, #FF6584);
  background-size: 300% 300%;
  animation: gradientShift 12s ease infinite;
  z-index: 0;
}
.brand-bg::after {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 75% 25%, rgba(255, 255, 255, 0.2), transparent 45%),
              radial-gradient(circle at 15% 85%, rgba(255, 255, 255, 0.12), transparent 40%);
}
.brand-top,
.brand-content,
.brand-foot { position: relative; z-index: 1; }
.brand-top { font-weight: 800; font-size: 1.3rem; }
.brand-content { animation: floatUp 0.7s ease both; }
.brand-content h1 {
  font-size: 2.6rem;
  line-height: 1.12;
  margin: 0 0 1.2rem;
  font-weight: 800;
  letter-spacing: -1px;
}
.brand-content p {
  font-size: 1.05rem;
  opacity: 0.92;
  margin: 0 0 2rem;
  max-width: 420px;
  line-height: 1.7;
}
.brand-features { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.9rem; }
.brand-features li { display: flex; align-items: center; gap: 0.7rem; font-size: 0.98rem; }
.brand-features span {
  width: 26px; height: 26px;
  display: flex; align-items: center; justify-content: center;
  background: rgba(255, 255, 255, 0.22);
  border-radius: 50%;
  font-size: 0.8rem;
  flex-shrink: 0;
}
.brand-foot { font-size: 0.85rem; opacity: 0.7; }

/* ===== Formulario ===== */
.form-panel {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2.5rem 1.5rem;
  background: var(--bg);
}
.auth-card {
  width: 100%;
  max-width: 430px;
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
  animation: floatUp 0.6s ease both;
}
.form-brand-mobile { display: none; font-weight: 800; font-size: 1.3rem; color: var(--primary); }
.auth-card h2 { margin: 0; font-size: 1.9rem; font-weight: 800; letter-spacing: -0.5px; }
.form-sub { margin: -0.5rem 0 0.5rem; color: var(--text-muted); }

/* Floating labels */
.float-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.float-field { position: relative; }
.float-field input {
  width: 100%;
  padding: 1.15rem 1rem 0.5rem;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  font-size: 1rem;
  background: #fff;
  color: var(--text);
  box-sizing: border-box;
  transition: var(--transition);
}
.float-field label {
  position: absolute;
  left: 1rem;
  top: 0.95rem;
  color: var(--text-soft);
  font-size: 1rem;
  pointer-events: none;
  transition: var(--transition);
  white-space: nowrap;
  overflow: hidden;
  max-width: calc(100% - 2rem);
  text-overflow: ellipsis;
}
.float-field input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 4px var(--primary-soft);
}
.float-field input:focus + label,
.float-field input:not(:placeholder-shown) + label {
  top: 0.4rem;
  font-size: 0.72rem;
  font-weight: 700;
  color: var(--primary);
  text-transform: uppercase;
  letter-spacing: 0.4px;
}

.btn-submit {
  margin-top: 0.4rem;
  padding: 0.95rem;
  background: var(--gradient-soft);
  color: #fff;
  border: none;
  border-radius: var(--radius-sm);
  font-size: 1.02rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow: var(--shadow-primary);
  transition: var(--transition);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
}
.btn-submit:hover:not(:disabled) { transform: translateY(-2px); filter: brightness(1.05); }
.btn-submit:disabled { opacity: 0.75; cursor: not-allowed; }
.spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.4);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
.error {
  background: var(--danger-soft);
  color: #c53030;
  padding: 0.75rem 1rem;
  border-radius: var(--radius-sm);
  font-size: 0.9rem;
  font-weight: 600;
  border-left: 3px solid var(--danger);
}
.alt { text-align: center; margin: 0.5rem 0 0; font-size: 0.92rem; color: var(--text-muted); }
.alt a { color: var(--primary); font-weight: 700; }
.alt a:hover { text-decoration: underline; }

.shake-enter-active { animation: shakeX 0.4s ease; }
@keyframes shakeX {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-6px); }
  75% { transform: translateX(6px); }
}

@media (max-width: 860px) {
  .auth-split { grid-template-columns: 1fr; }
  .brand-panel { display: none; }
  .form-brand-mobile { display: block; margin-bottom: 0.5rem; }
}
</style>
