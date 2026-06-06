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
.campo { display: flex; flex-direction: column; gap: 0.4rem; margin-bottom: 1rem; }
label { font-size: 0.85rem; font-weight: 500; color: #444; }
input {
  padding: 0.6rem 0.8rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.2s;
}
input:focus { outline: none; border-color: #42b883; }
input.input-error { border-color: #e53e3e; }
.error-msg { font-size: 0.8rem; color: #e53e3e; }
</style>
