<template>
  <div class="footer-section">
    <h3 class="footer-title">{{ title }}</h3>
    <ul>
      <li v-for="(link, i) in links" :key="i">
        <FooterLink :href="link.href">{{ link.label }}</FooterLink>
      </li>
    </ul>
  </div>
</template>

<script setup>
import FooterLink from '@/components/ui/FooterLink.vue'

defineProps({
  title: String,
  links: Array,
})
</script>

<style scoped>
.footer-section {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.footer-title {
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: var(--color-white);
}

ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

li {
  margin-bottom: 0.3rem;
}
</style>
