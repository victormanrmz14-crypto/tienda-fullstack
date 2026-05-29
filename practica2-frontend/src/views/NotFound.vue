<template>
  <div class="not-found">
    <div class="content">
      <h1>404</h1>
      <h2>Página no encontrada</h2>
      <p>La ruta que buscas no existe.</p>
      <RouterLink to="/" class="btn-home">Volver al inicio</RouterLink>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.not-found {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f0f2f5;
}
.content {
  text-align: center;
  background: white;
  padding: 3rem;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
h1 {
  font-size: 6rem;
  margin: 0;
  color: #42b883;
  line-height: 1;
}
h2 { margin: 0.5rem 0 1rem; color: #333; }
p  { color: #999; margin: 0 0 2rem; }
.btn-home {
  background: #42b883;
  color: white;
  padding: 0.8rem 2rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
}
.btn-home:hover { background: #35a070; }
</style>