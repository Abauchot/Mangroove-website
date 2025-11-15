<template>
  <header class="header">
    <div class="container">
      <div class="left">
        <div class="logo">
          <div class="logo-circle">🐵</div>
          <span class="logo-text">Mangroove session</span>
        </div>
        <nav class="nav">
          <NavLink to="/">Accueil</NavLink>
          <NavLink to="/creations">Créations</NavLink>
          <NavLink to="/reglement">Règlement</NavLink>
          <NavLink to="/jams">Jams Passées</NavLink>
          <NavLink to="/a-propos">À Propos</NavLink>
        </nav>
      </div>
      <div class="right">
        <PhaseBadge phase="Soumission" />
        <BaseButton variant="dark">Se connecter</BaseButton>
      </div>
    </div>
  </header>
</template>

<script setup>
import NavLink from '@/components/ui/NavLink.vue'
import PhaseBadge from '@/components/ui/PhaseBadge.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
</script>

<style scoped>
.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 64px;
  background-color: var(--color-white);
  border-bottom: 1px solid var(--color-gray-medium);
  z-index: 1000;
  font-size: 14px;
}

.container {
  max-width: 100%;
  margin: 0 auto;
  padding: 0 1.5rem;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.left {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  font-size: 0.9rem;
  color: var(--color-dark);
  white-space: nowrap;
}

.logo-circle {
  width: 28px;
  height: 28px;
  background: var(--color-dark);
  color: white;
  border-radius: 999px;
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  height: 100%;
}

.right {
  display: flex;
  align-items: center;
  gap: 1rem;
}
</style>
