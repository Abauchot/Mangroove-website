<template>
  <div class="phase-card" :class="status">
    <div class="icon"><slot name="icon" /></div>
    <div class="label">{{ label }}</div>
    <div class="status">{{ statusText }}</div>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  status: { type: String, default: 'upcoming' },
  statusText: String
})
</script>

<style scoped>
.phase-card {
  flex: 1;
  padding: 1.5rem;
  text-align: center;
  border-radius: 8px;
  background: #f5f5f5;
  color: #666;
  border: 2px solid transparent;
}
.phase-card.done { opacity: 0.6; }
.phase-card.active {
  border-color: #333;
  background: #fff;
  color: #000;
}
.icon { font-size: 1.5rem; margin-bottom: .5rem; }
.label { font-weight: 600; }
.status { font-size: .9rem; margin-top: .25rem; }
</style>
