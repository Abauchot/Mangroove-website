<template>
  <button class="base-button" :class="[variantClass, { 'full-width': fullWidth }]" :disabled="disabled"
    @click="$emit('click')">
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'default',
  },
  fullWidth: Boolean,
  disabled: Boolean,
})
defineEmits(['click'])

const variantClass = computed(() => {
  return {
    default: 'btn-default',
    dark: 'btn-dark',
    ghost: 'btn-ghost',
  }[props.variant] || 'btn-default'
})
</script>

<style scoped>
.base-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  height: 36px;
  padding: 0 16px;
  font-size: 14px;
  font-weight: 600;
  border-radius: 4px;
  border: 1px solid var(--color-dark);
  cursor: pointer;
  transition: background 0.2s ease, color 0.2s ease;
  line-height: 1;
}

.btn-default {
  background-color: var(--color-white);
  color: var(--color-dark);
}

.btn-dark {
  background-color: var(--color-dark);
  color: var(--color-white);
  border: none;
}

.btn-ghost {
  background-color: transparent;
  color: var(--color-dark);
  border: none;
}

.base-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.full-width {
  width: 100%;
}
</style>
