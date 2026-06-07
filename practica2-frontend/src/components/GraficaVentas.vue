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
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
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
  background: linear-gradient(to top, #42b883, #35495e);
  border-radius: 4px 4px 0 0;
  transition: height 0.5s ease;
}
span { font-size: 0.75rem; color: #666; }
</style>
