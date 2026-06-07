<template>
  <div class="campo">
    <label>{{ label }}</label>
    <input
      :type="type"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :class="{ 'input-error': error }"
      :placeholder="placeholder"
    />
    <span class="error-msg" v-if="error">{{ error }}</span>
  </div>
</template>

<script setup>
defineProps({
  label:       { type: String,  required: true },
  name:        { type: String,  required: true },
  modelValue:  { default: '' },
  error:       { type: String,  default: '' },
  type:        { type: String,  default: 'text' },
  placeholder: { type: String,  default: '' },
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
.campo { display: flex; flex-direction: column; gap: 0.45rem; margin-bottom: 1rem; }
label { font-size: 0.85rem; font-weight: 600; color: var(--text); }
input {
  padding: 0.65rem 0.8rem;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  font-size: 1rem;
  background: #fff;
  color: var(--text);
  transition: var(--transition);
}
input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-soft); }
input.input-error { border-color: var(--danger); }
input.input-error:focus { box-shadow: 0 0 0 3px var(--danger-soft); }
.error-msg { font-size: 0.8rem; color: var(--danger); font-weight: 600; }
</style>
