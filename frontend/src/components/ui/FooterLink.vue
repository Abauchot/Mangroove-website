<template>
  <a :href="href" class="footer-link" target="_blank" rel="noopener noreferrer">
    <slot />
  </a>
</template>

<script setup>
defineProps({
  href: {
    type: String,
    required: true,
  },
})
</script>

<style scoped>
.footer-link {
  font-size: 0.85rem;
  color: var(--color-text-secondary);
  text-decoration: none;
  transition: color 0.2s ease;
}

.footer-link:hover {
  color: var(--color-dark);
}
</style>
