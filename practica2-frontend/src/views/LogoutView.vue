<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth   = useAuthStore()
const router = useRouter()

onMounted(() => {
  setTimeout(async () => {
    await auth.logout()
    router.push('/')
  }, 1500)
})
</script>

<template>
  <div class="logout-screen">
    <div class="logout-card">
      <span class="spinner"></span>
      <p class="logout-text">Cerrando sesión...</p>
    </div>
  </div>
</template>

<style scoped>
.logout-screen {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--gradient);
  padding: 1.5rem;
}
.logout-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
}
.spinner {
  width: 60px;
  height: 60px;
  border: 4px solid rgba(255, 255, 255, 0.35);
  border-top-color: #6C63FF;
  border-radius: 50%;
  animation: girar 0.9s linear infinite;
}
.logout-text {
  margin: 0;
  color: #fff;
  font-size: 1.1rem;
  font-weight: 700;
  letter-spacing: 0.3px;
}
@keyframes girar {
  to { transform: rotate(360deg); }
}
</style>
