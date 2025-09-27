<template>
  <section class="hero">
    <div class="container">
      <div class="left">
        <StatusPill>Phase active : En cours</StatusPill>

        <h1 class="title">{{ jam.title }}</h1>
        <p class="theme">Thème : <strong>“{{ jam.theme }}”</strong></p>

        <p class="desc">
          Vous avez 10 jours pour proposer la création de votre choix peu importe le support,
          cette jam accueille tout le monde.
        </p>

        <div class="actions">
          <BaseButton variant="dark">Participer maintenant</BaseButton>
          <BaseButton>Voir les créations</BaseButton>
        </div>
      </div>

      <div class="right">
        <CountdownCard :endDate="jam.endsAt" />
      </div>
    </div>
  </section>
</template>

<script setup>
import BaseButton from '@/components/ui/BaseButton.vue'
import StatusPill from '@/components/ui/StatusPill.vue'
import CountdownCard from '@/components/home/CountdownCard.vue'
import { ref } from 'vue'

const jam = ref({
  title: 'Mangroove session #1',
  theme: '“?”',
  endsAt: new Date(Date.now() + (2 * 24 + 14) * 60 * 60 * 1000 + 32 * 60 * 1000)
})
</script>

<style scoped>
.hero {
  width: 100vw;
  background: #f6f6f6;
  padding: 3rem 0 4rem;
}

.container {
  width: 100%;
  margin: 0 auto;
  padding: 2rem 6rem;

  display: grid;
  grid-template-columns: 1.1fr .9fr;
  align-items: start;
  gap: 3rem;
}

.title {
  font-size: 2.4rem;
  line-height: 1.15;
  font-weight: 700;
  margin: 0;
}

.theme {
  font-size: 1.1rem;
  color: #444;
  margin: .25rem 0 0;
}

.desc {
  font-size: 1rem;
  color: #4a4a4a;
  max-width: 48ch;
}

.actions {
  display: flex;
  gap: 1rem;
  margin-top: 1.25rem;
}

.left {
  display: flex;
  flex-direction: column;
  min-height: 250px;
  gap: 0.5rem;
}

.right {
  display: flex;
  align-items: center;
}
</style>
