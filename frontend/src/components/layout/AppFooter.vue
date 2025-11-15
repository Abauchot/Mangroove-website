<template>
  <footer class="footer full-width">
    <div class="container">
      <div class="footer-brand">
        <div class="logo">
          <div class="logo-circle">🐵</div>
          <span class="logo-text">Mangroove sessions</span>
        </div>
        <p class="tagline">
          Phrase bateau genre, ouais ouais, on est trop des bons, rejoignez nous
        </p>
      </div>

      <div class="footer-sections">
        <FooterSection title="Navigation" :links="[
          { href: '/', label: 'Accueil' },
          { href: '/creations', label: 'Créations' },
          { href: '/reglement', label: 'Règlement' },
          { href: '/jams', label: 'Jams Passées' },
          { href: '/a-propos', label: 'À Propos' },
        ]" />

        <FooterSection title="Communauté" :links="[
          { href: '#', label: 'Discord' },
          { href: '#', label: 'Forum' },
          { href: '#', label: 'IRC' },
          { href: '#', label: 'Twitter' },
        ]" />

        <FooterSection title="Support" :links="[
          { href: '#', label: 'Contact' },
          { href: '#', label: 'FAQ' },
          { href: '#', label: 'Mentions légales' },
        ]" />
      </div>
    </div>

    <div class="footer-bottom">
      <div class="footer-bottom-content">
        <p>© 2025 Primate Lab. Tous droits réservés.</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import FooterSection from '@/components/ui/FooterSection.vue'
</script>

<style scoped>
.footer {
  width: 100%;
  background-color: var(--color-dark);
  color: var(--color-white);
  padding: 3rem 0 1.5rem;
  margin-top: 3rem;
}

.container {
  max-width: 90%;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 3rem;
}

.footer-brand {
  flex: 1;
  min-width: 200px;
}

.footer-sections {
  display: flex;
  flex: 3;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 2rem;
}

.footer-bottom {
  display: flex;
  justify-content: center;
  width: 100%;
}

.footer-bottom-content {
  width: 80%;
  border-top: 1px solid var(--color-gray-medium);
  margin-top: 2rem;
  padding-top: 1rem;
  text-align: center;
  font-size: 0.8rem;
  color: var(--color-gray-medium);
}
</style>
