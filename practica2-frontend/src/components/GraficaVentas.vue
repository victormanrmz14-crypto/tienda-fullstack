<template>
  <div class="grafica-placeholder">
    <h3>📊 Gráfica de Ventas</h3>
    <p>Componente cargado de forma lazy (defineAsyncComponent)</p>
    <div class="barras">
      <div v-for="mes in meses" :key="mes.nombre" class="barra-wrapper">
        <div class="barra" :style="{ height: mes.valor + 'px' }"></div>
        <span>{{ mes.nombre }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
const meses = [
  { nombre: 'Ene', valor: 80 },
  { nombre: 'Feb', valor: 120 },
  { nombre: 'Mar', valor: 95 },
  { nombre: 'Abr', valor: 150 },
  { nombre: 'May', valor: 200 },
  { nombre: 'Jun', valor: 175 },
]
</script>

<style scoped>
.grafica-placeholder {
  background: transparent;
}
.grafica-placeholder h3 { margin: 0 0 0.3rem; font-size: 1.15rem; font-weight: 800; }
.grafica-placeholder > p { margin: 0; font-size: 0.82rem; color: var(--text-muted); }
.barras {
  display: flex;
  align-items: flex-end;
  gap: 1rem;
  height: 220px;
  margin-top: 1rem;
}
.barra-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.3rem;
  flex: 1;
}
.barra {
  width: 100%;
  background: var(--gradient);
  border-radius: 6px 6px 0 0;
  transition: height 0.5s ease;
}
.barra:hover { filter: brightness(1.08); }
span { font-size: 0.75rem; color: var(--text-muted); }
</style>
