<template>
  <section class="phase-section full-width">
    <div class="container">
      <h2 class="title">Phase Active</h2>
      <div class="phases">
        <PhaseCard label="Annonce" status="done" statusText="Terminé">
          <template #icon>💡</template>
        </PhaseCard>
        <PhaseCard label="Développement" status="done" statusText="Terminé">
          <template #icon>💻</template>
        </PhaseCard>
        <PhaseCard label="Soumission" status="active" statusText="En cours">
          <template #icon>⬆️</template>
        </PhaseCard>
        <PhaseCard label="Évaluation" status="upcoming" statusText="À venir">
          <template #icon>🏆</template>
        </PhaseCard>
      </div>
    </div>
  </section>
</template>

<script setup>
import PhaseCard from '@/components/home/PhaseCard.vue'
</script>

<style scoped>
.title {
  text-align: center;
  margin-bottom: 2rem;
}
.phases {
  display: flex;
  gap: 1rem;
}
</style>
