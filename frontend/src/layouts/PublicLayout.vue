<template>
  <div class="min-h-screen bg-white text-ink">
    <SiteHeader />
    <main>
      <RouterView />
    </main>
    <SiteFooter />
  </div>
</template>

<script setup>
import SiteFooter from '../components/layout/SiteFooter.vue';
import SiteHeader from '../components/layout/SiteHeader.vue';
</script>
