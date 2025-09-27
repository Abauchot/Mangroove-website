<template>
  <span class="pill">
    <span class="icon">★</span>
    <slot />
  </span>
</template>

<script setup>
</script>

<style scoped>
.pill {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: .5rem;
  padding: .25rem .6rem;
  height: 28px;
  border-radius: 999px;
  background: #efefef;
  color: #333;
  font-size: .85rem;
  font-weight: 600;
  max-width: 250px;
}

.icon {
  font-size: .9rem;
  line-height: 1;
}
</style>
