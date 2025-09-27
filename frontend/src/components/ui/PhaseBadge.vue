<template>
  <span class="phase-badge">
    <span class="icon">🕒</span>
    <span>Phase : {{ phase }}</span>
  </span>
</template>

<script setup>
defineProps({
  phase: String
})
</script>

<style scoped>
.phase-badge {
  background-color: var(--color-gray-light);
  color: var(--color-text-primary);
  border-radius: 999px;
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  height: 32px;
}
</style>
