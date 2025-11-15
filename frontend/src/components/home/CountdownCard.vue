<template>
  <aside class="card">
    <h3 class="heading">Temps restant</h3>

    <div v-if="!isOver" class="grid">
      <div class="cell">
        <div class="num">{{ days }}</div>
        <div class="unit">Jours</div>
      </div>
      <div class="cell">
        <div class="num">{{ hours }}</div>
        <div class="unit">Heures</div>
      </div>
      <div class="cell">
        <div class="num">{{ minutes }}</div>
        <div class="unit">Minutes</div>
      </div>
    </div>

    <div v-else class="over">Terminé</div>
  </aside>
</template>

<script setup>
import { useCountdown } from '@/composables/useCountdown.js'

const props = defineProps({
  endDate: { type: [String, Number, Date], required: true }
})

const { days, hours, minutes, isOver } = useCountdown(() => props.endDate)
</script>

<style scoped>
.card {
  width: 70%;
  min-height: 250px;
  background: #2f2f3a;
  color: #fff;
  border-radius: 12px;
  padding: 2rem;
}

.heading {
  text-align: center;
  font-size: 1.4rem;
  font-weight: 600;
  margin: 0 0 1.5rem;
}

.grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.25rem;
}

.cell {
  text-align: center;
}

.num {
  font-size: 2.75rem;
  font-weight: 700;
  line-height: 1;
  margin-bottom: .25rem;
}

.unit {
  opacity: .9;
  font-size: .95rem;
}

.over {
  text-align: center;
  font-size: 1.2rem;
  font-weight: 600;
  opacity: .9;
}
</style>
