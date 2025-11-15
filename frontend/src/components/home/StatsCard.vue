<template>
  <div class="stats-card">
    <div class="number">{{ number }}</div>
    <div class="label">{{ label }}</div>
  </div>
</template>

<script setup>
defineProps({
  number: [String, Number],
  label: String
})
</script>

<style scoped>
.stats-card {
  flex: 1;
  text-align: center;
  background: #fff;
  padding: 2rem 1rem;
  border-radius: 8px;
}
.number {
  font-size: 1.8rem;
  font-weight: 700;
}
.label {
  font-size: .9rem;
  color: #555;
}
</style>
