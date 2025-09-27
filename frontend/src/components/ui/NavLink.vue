<template>
  <router-link :to="to" class="nav-link" :class="{ active: isActive }">
    <slot />
  </router-link>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  to: {
    type: String,
    required: true,
  },
})

const route = useRoute()
const isActive = computed(() => route.path === props.to)
</script>

<style scoped>
.nav-link {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--color-text-secondary);
  text-decoration: none;
  padding: 0.5rem 0.75rem;
}

.nav-link:hover {
  color: var(--color-dark);
}

.active {
  font-weight: 600;
  color: var(--color-dark);
}
</style>
