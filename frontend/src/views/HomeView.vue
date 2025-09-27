<template>
  <section class="hero">
    <h1 class="title">Participe à la prochaine Jam !</h1>
    <BaseButton variant="primary" @click="handleJoin">Participer maintenant</BaseButton>
    <BaseButton variant="ghost">Voir les créations</BaseButton>
  </section>
</template>

<script setup lang="ts">
import BaseButton from '@/components/ui/BaseButton.vue'

function handleJoin() {
  console.log('Participer cliqué')
}
</script>

<style scoped>
.hero {
  text-align: center;
  padding: 4rem 2rem;
}

.title {
  font-size: 2rem;
  margin-bottom: 2rem;
}
</style>
