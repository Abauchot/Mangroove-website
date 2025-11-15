<template>
  <div class="app">
    <div class="layout">
      <AppHeader />
      <main class="page">
        <router-view />
      </main>
     <AppFooter />
    </div>
  </div>
</template>

<script setup>
import AppHeader from '@/components/layout/AppHeader.vue'
import AppFooter from '@/components/layout/AppFooter.vue'
</script>

<style scoped>
.layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  margin: 0;
  padding: 0;
  /* position: fixed;
  top: 0;
  left: 0;
  width: 100vw; */
}

.page {
  flex: 1;
  padding-top: 64px;
}

</style>
