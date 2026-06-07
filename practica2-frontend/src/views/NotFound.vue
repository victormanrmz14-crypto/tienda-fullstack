<template>
  <div class="not-found">
    <div class="nf-bg"></div>
    <div class="content">
      <h1>404</h1>
      <h2>Página no encontrada</h2>
      <p>Lo sentimos, la ruta que buscas no existe o fue movida.</p>
      <RouterLink to="/" class="btn-home">← Volver al inicio</RouterLink>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.not-found {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  padding: 1.5rem;
}
.nf-bg {
  position: absolute;
  inset: -20%;
  background: linear-gradient(135deg, #6C63FF, #8B85FF, #FF6584, #6C63FF);
  background-size: 300% 300%;
  animation: gradientShift 12s ease infinite;
}
.content {
  position: relative;
  z-index: 1;
  text-align: center;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  padding: 3.5rem 3rem;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-lg);
  animation: floatUp 0.6s ease both;
}
h1 {
  font-size: 6.5rem;
  margin: 0;
  line-height: 1;
  font-weight: 800;
  background: var(--gradient-soft);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}
h2 { margin: 0.5rem 0 1rem; color: var(--dark); font-weight: 800; }
p  { color: var(--text-muted); margin: 0 0 2rem; }
.btn-home {
  display: inline-block;
  background: var(--gradient-soft);
  color: #fff;
  padding: 0.85rem 2rem;
  border-radius: var(--radius-pill);
  font-weight: 700;
  box-shadow: var(--shadow-primary);
  transition: var(--transition);
}
.btn-home:hover { transform: translateY(-3px); }
</style>
