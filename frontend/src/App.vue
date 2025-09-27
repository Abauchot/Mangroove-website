<template>
  <div class="layout">
    <AppHeader />
    <main class="page">
      <router-view />
    </main>
    <AppFooter />
  </div>
</template>

<script setup>
import AppHeader from '@/components/layout/AppHeader.vue'
import AppFooter from '@/components/layout/AppFooter.vue'
</script>

<style scoped>
.layout {
  display: flex;
  position: fixed;
  top: 0;
  left: 0;
  flex-direction: column;
  min-height: 100vh;
  width: 100vw;
  margin: 0;
  padding: 0;
}

.page {
  flex: 1;
  padding-top: 64px;
}
</style>
