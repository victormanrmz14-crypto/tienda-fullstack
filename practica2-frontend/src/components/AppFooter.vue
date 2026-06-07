<script setup>
const anio = new Date().getFullYear()
</script>

<template>
  <footer class="footer">
    <div class="footer-inner">
      <div class="footer-brand">
        <div class="brand">
          <span class="brand-logo">🛍️</span>
          <span class="brand-text">Tienda Full-Stack</span>
        </div>
        <p>Los mejores productos al mejor precio, con envíos a todo el país.</p>
      </div>

      <div class="footer-col">
        <h4>Tienda</h4>
        <RouterLink to="/catalogo">Catálogo</RouterLink>
        <RouterLink to="/carrito">Carrito</RouterLink>
        <RouterLink to="/login">Iniciar sesión</RouterLink>
      </div>

      <div class="footer-col">
        <h4>Ayuda</h4>
        <a href="#">Envíos y devoluciones</a>
        <a href="#">Preguntas frecuentes</a>
        <a href="#">Contacto</a>
      </div>

      <div class="footer-col">
        <h4>Síguenos</h4>
        <div class="social">
          <a href="#" aria-label="Facebook">📘</a>
          <a href="#" aria-label="Instagram">📷</a>
          <a href="#" aria-label="Twitter">🐦</a>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© {{ anio }} Tienda Full-Stack · Hecho con Vue 3</p>
    </div>
  </footer>
</template>

<style scoped>
.footer {
  background: var(--gradient-dark);
  color: rgba(255, 255, 255, 0.85);
  margin-top: auto;
}
.footer-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 3.5rem 1.5rem 2rem;
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 2.5rem;
}
.brand {
  display: flex;
  align-items: center;
  gap: 0.55rem;
  font-weight: 800;
  font-size: 1.2rem;
  color: #fff;
  margin-bottom: 0.8rem;
}
.footer-brand p {
  margin: 0;
  font-size: 0.92rem;
  max-width: 320px;
  line-height: 1.7;
  color: rgba(255, 255, 255, 0.6);
}
.footer-col h4 {
  margin: 0 0 1rem;
  font-size: 0.95rem;
  color: #fff;
}
.footer-col a {
  display: block;
  padding: 0.32rem 0;
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.65);
  transition: var(--transition);
}
.footer-col a:hover {
  color: var(--primary-light);
  transform: translateX(3px);
}
.social {
  display: flex;
  gap: 0.6rem;
}
.social a {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.08);
  border-radius: 50%;
  font-size: 1.1rem;
  transition: var(--transition);
}
.social a:hover {
  background: var(--primary);
  transform: translateY(-3px);
}
.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding: 1.4rem 1.5rem;
  text-align: center;
}
.footer-bottom p {
  margin: 0;
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.5);
}

@media (max-width: 720px) {
  .footer-inner {
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
  }
}
</style>
