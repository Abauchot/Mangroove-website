<template>
  <section class="stats-section full-width">
    <div class="container stats">
      <StatsCard number="247" label="Participants" />
      <StatsCard number="89" label="Jeux soumis" />
      <StatsCard number="156" label="Équipes" />
      <StatsCard number="1,234" label="Votes" />
    </div>
  </section>
</template>

<script setup>
import StatsCard from '@/components/home/StatsCard.vue'
</script>

<style scoped>
.stats-section {
  background: #f6f6f6;
  padding: 3rem 0;
}
.stats {
  display: flex;
  gap: 1rem;
}
</style>
